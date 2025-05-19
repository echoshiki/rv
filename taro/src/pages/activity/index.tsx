import { View } from '@tarojs/components';
import CustomSwiper from '@/components/CustomSwiper';
import { useBanner } from '@/hooks/useBanner';
import Loading from '@/components/Loading';
import { useActivityList } from '@/hooks/useActivityList';
import { Tabs } from '@nutui/nutui-react-taro'
import { useState, useMemo, useEffect } from 'react';
import { useActivityCategoryList } from '@/hooks/useActivityCategoryList';
import { ActivityList } from '@/components/ActivityList';
import { ConfigProvider } from '@nutui/nutui-react-taro'
// import { nutuiTheme } from '@/theme/nutui-theme'

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

const ActivityTabList = ({ category_id }: { category_id: string }) => {
    const { activityList, loading } = useActivityList({
        filter: {
            category_id
        }
    }); 

    return (
        <View>
            {loading ? <Loading /> : (
                <ActivityList list={activityList} />
            )}
        </View>
    )
}

const Activity = () => {
    const [tabIndex, setTabIndex] = useState<number | string>(0);
    const { categories, loading: categoriesLoading } = useActivityCategoryList();

    useEffect(() => {
        if (categories.length > 0 && tabIndex === 0) {
            setTabIndex(categories[0].id);
        }
    }, [categories, tabIndex]);

    const handleTabChange = (index: number | string) => {
        setTabIndex(index);
    };

    return (
        <View className="p-3 bg-gray-100">
            <View>
                <ActivitySwiper />
            </View>
            <View className="mt-5">
                {categoriesLoading && <Loading />}
                <ConfigProvider theme={{ 
                    '--nutui-tabs-tabpane-padding': '0px',
                    '--nutui-tabs-titles-background-color': 'transparent',
                    '--nutui-tabs-tab-line-width': '32px',
                    '--nutui-tabs-titles-item-active-color': '#000',
                    '--nutui-tabs-titles-item-active-font-weight': 'bold',
                    '--nutui-tabs-line-bottom': '8%',
                    '--nutui-tabs-tab-line-color': '#000',
                }}>
                    <Tabs
                        value={tabIndex}
                        onChange={handleTabChange}
                    >
                        {categories.map((category) => (
                            <Tabs.TabPane 
                                value={category.id} 
                                title={category.title}
                            >
                                <ActivityTabList category_id={category.id} />
                            </Tabs.TabPane>
                        ))}
                    </Tabs>
                </ConfigProvider>
            </View>
        </View>
    )
}

export default Activity;