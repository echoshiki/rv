import { View, Text } from "@tarojs/components";
import { usePointLog } from "@/hooks/usePointLog";
import PageCard from "@/components/PageCard";
import { usePullDownRefresh, stopPullDownRefresh, useReachBottom } from "@tarojs/taro";
import { TextListSkeleton } from "@/components/Skeleton";
import Loading from "@/components/Loading";

const Point = () => {
    const {
        pointLogs,
        loadMore,
        hasMore,
        loading,
        refresh
    } = usePointLog({
        sort: 'desc',
        page: 1,
        limit: 10
    });

    // 处理下拉刷新
    const handlePullDownRefresh = async () => {
        console.log('下拉刷新');
        try {
            await refresh();
        } finally {
            stopPullDownRefresh();
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
    usePullDownRefresh(handlePullDownRefresh);
    useReachBottom(handleReachBottom);

    if (loading && pointLogs.length === 0) {
        return (
            <View className="bg-gray-100 min-h-screen">
                <PageCard
                    title="积分明细"
                    subtitle="这里能够查询您近一年的积分兑换记录"
                >
                    <TextListSkeleton />
                </PageCard>
            </View>
        )
    }

    return (
        <View className="bg-gray-100 min-h-screen">
            <PageCard
                title="积分明细"
                subtitle="这里能够查询您近一年的积分兑换记录"
            >
                <View className="flex flex-col space-y-3">
                    {pointLogs.map(item => (
                        <View key={item.id} className="flex justify-between items-center pb-3 border-b border-dotted border-gray-300">
                            <View className="flex flex-col space-y-1">
                                <Text className="text-sm">{item.type_description}</Text>
                                <Text className="text-xs text-gray-500">{item.transaction_at}</Text>
                            </View>
                            <View className="flex flex-col space-y-1 items-end">
                                <Text className="text-sm font-semibold">{item.points_change}</Text>
                                <Text className="text-xs text-gray-500">{item.remarks}</Text>
                            </View>
                        </View>
                    ))}
                </View>

                <View className="flex justify-center items-center h-24">
                    {loading && pointLogs.length > 0 ? (
                        // 正在加载更多
                        <Loading />
                    ) : hasMore ? (
                        // 还有更多，可以点击或上拉加载
                        <Text className="text-gray-500">
                            上拉加载更多
                        </Text>
                    ) : (
                        // 没有更多了
                        <Text className="text-gray-500">没有更多数据了</Text>
                    )}
                </View>

            </PageCard>
        </View>
    )
}

export default Point;