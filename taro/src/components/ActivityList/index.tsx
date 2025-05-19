import { View, Text, Image } from '@tarojs/components';
import DefaultCover from '@/assets/images/cover.jpg';
import { ActivityItem as ActivityItemProps } from '@/types/ui';
import { type ActivityListQueryParams } from '@/api/activity';
import { mapsTo } from '@/utils/common';
import { Tag } from '@nutui/nutui-react-taro';
import { useActivityList } from '@/hooks/useActivityList';
import Taro from '@tarojs/taro';
import Loading from '@/components/Loading';

const ActivityItem = ({ item }: { item: ActivityItemProps }) => {
    return (
        <View
            className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed"
            onClick={() => mapsTo(`/pages/activity/detail/index?id=${item.id}`)}
        >
            <View className="w-24">
                <View className="relative block h-0 p-0 overflow-hidden pb-[80%] rounded-xl">
                    <Image
                        src={item.cover ? item.cover : DefaultCover}
                        className="absolute object-cover w-full h-full border-none align-middle"
                        mode={`aspectFill`}
                    />
                </View>
            </View>
            <View className="flex-1 flex flex-col space-y-2">
                <View className="text-sm text-ellipsis overflow-hidden line-clamp-2 h-10">
                    <Text>{item.title}</Text>
                </View>
                <View className="flex flex-row justify-between items-center">
                    <View>
                        <Text className="text-gray-400 text-xs">
                            报名开始: {item.registration_start_at}
                        </Text>
                    </View>
                    <View>
                        {item.registration_fee === '0.00' && (
                            <Tag type="success">免费</Tag>
                        )}
                    </View>
                </View>
            </View>
        </View>
    )
}

interface ActivityListProps {
    queryParams: ActivityListQueryParams;
    isPullDownRefresh?: boolean;
    isReachBottomRefresh?: boolean;
    changePageTitle?: boolean;
}

const ActivityList = ({
    queryParams,
    isPullDownRefresh = false,
    isReachBottomRefresh = false,
    changePageTitle = false
}: ActivityListProps) => {
    const {
        activityList,
        loading,
        refresh,
        loadMore,
        hasMore,
    } = useActivityList(queryParams, {
        // 分类加载完成后的回调，设置页面标题
        onCategoryLoaded: changePageTitle ? (category) => {
            if (category?.title) {
                Taro.setNavigationBarTitle({
                    title: category.title
                });
            }
        } : undefined
    });

    // 处理下拉刷新
    const handlePullDownRefresh = async () => {
        console.log('下拉刷新');
        try {
            await refresh();
        } finally {
            Taro.stopPullDownRefresh();
        }
    };

    // 处理触底加载
    const handleReachBottom = async () => {
        console.log('触底加载');
        if (hasMore && !loading) {
            await loadMore();
        }
    };

    // 注册下拉刷新与触底加载
    if (isPullDownRefresh) {
        Taro.usePullDownRefresh(handlePullDownRefresh);
    }
    if (isReachBottomRefresh) {
        Taro.useReachBottom(handleReachBottom);
    }

    return (
        <View className="w-full p-3 rounded-xl bg-white pb-5">
            <View>
                {activityList.map(item => (
                    <ActivityItem item={item} />
                ))}

                {activityList.length === 0 && !loading && (
                    <View className="flex justify-center items-center h-64">
                        <Text className="text-gray-500">该分类下还没有活动</Text>
                    </View>
                )}

                {loading && (
                    <View className="flex justify-center items-center h-64">
                        <Loading />
                    </View>
                )}

                {!hasMore && activityList.length > 0 && (
                    <View className="text-center text-gray-500 text-sm py-4">
                        没有更多数据了
                    </View>
                )}
            </View>
        </View>
    )
}

export default ActivityList;