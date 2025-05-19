import { View, Text } from '@tarojs/components';
import CustomSwiper from '@/components/CustomSwiper';
import { useBanner } from '@/hooks/useBanner';
import Loading from '@/components/Loading';
import { useActivityList } from '@/hooks/useActivityList';
import CustomTabs from '@/components/CustomTabs';
import { useMemo } from 'react';
import { useActivityCategoryList } from '@/hooks/useActivityCategoryList';
import { ActivityList } from '@/components/ActivityList';
import Taro from '@tarojs/taro';

/**
 * 活动频道轮播图
 */
const ActivitySwiper = () => {
    const { banners: cmsBanners, loading: cmsLoading } = useBanner('activity');
    const { activityList: activityRecommend, loading: recommendLoading } = useActivityList({
        filter: {
            is_recommend: 1
        },
        limit: 5
    });

    // 使用 useMemo 节省计算合并两个列表
    const combinedBanners = useMemo(() => {
        const bannerFromActivityRecommend = activityRecommend.map(item => ({
            id: item.id,
            image: item.cover || '',
            title: item.title,
            link: `/pages/activity/detail/${item.id}`
        }));
        return [...cmsBanners, ...bannerFromActivityRecommend];
    }, [cmsBanners, activityRecommend]);

    // 载入中
    const isLoading = cmsLoading || recommendLoading;
    if (isLoading && combinedBanners.length === 0) {
        return <Loading />;
    }

    // 没有数据不显示组件
    if (combinedBanners.length === 0 && !isLoading) {
        return null;
    }

    return (
        <View>
            <CustomSwiper
                imageList={combinedBanners}
                imageRatio={1.8}
                rounded="lg"
            />
        </View>
    )
}

/**
 * Tab 标签页下的活动列表内容
 * @param param0 
 * @returns 
 */
const ActivityTabList = ({ category_id }: { category_id: string | number }) => {
    const { 
        activityList,
        loading,
        refresh,
        loadMore,
        hasMore,
     } = useActivityList({
        filter: {
            category_id
        },
        limit: 5
    });

    const handlePullDownRefresh = async () => {
        console.log('下拉刷新');
        try {
            await refresh();
        } finally {
            Taro.stopPullDownRefresh();
        }
    };

    const handleReachBottom = async () => {
        console.log('触底加载');
        if (hasMore && !loading) {
            await loadMore();
        }
    };

    // 注册下拉刷新与触底加载
    Taro.usePullDownRefresh(handlePullDownRefresh);
    Taro.useReachBottom(handleReachBottom);

    if (activityList.length === 0 && !loading) {
        return (
            <View className="flex justify-center items-center h-64">
                <Text className="text-gray-500">该分类下还没有活动</Text>
            </View>
        );
    }
    return (
        <View>
            <ActivityList list={activityList} />
            {loading && <Loading />}
        </View>
    );
};

const Activity = () => {
    const { categories, loading } = useActivityCategoryList();

    return (
        <View className="bg-gray-100 min-h-screen py-3">
            {/* 活动频道轮播图 */}
            <View className="px-5">
                <ActivitySwiper />
            </View>

            {/* 活动频道标签页 */}
            <View className="w-full px-5 mt-2">
                <CustomTabs 
                    items={categories}
                    renderTabContent={(item) => <ActivityTabList category_id={item.id} />}
                    isLoading={loading}
                />
            </View>
        </View>
    )
}

export default Activity;