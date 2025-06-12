import { View, Text } from '@tarojs/components';
import { RvItem as RvItemProps } from "@/types/ui";
import { mapsTo } from '@/utils/common';
import Taro from '@tarojs/taro';
import Loading from '@/components/Loading';
import AspectRatioImage from '@/components/AspectRatioImage';
import { useRvList } from '@/hooks/useRvList';

const RvItem = ({ item, used }: { item: RvItemProps, used: boolean }) => {
    return (
        <View
            className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed"
            onClick={() => mapsTo(`/pages/sale/detail/index?id=${item.id}${used ? '&used=1' : ''}`)}
        >
            <View className="w-24">
                <AspectRatioImage
                    src={item.cover}
                    ratio={.8}
                />
            </View>
            <View className="flex-1 flex flex-col space-y-2">
                <View className="text-sm text-ellipsis overflow-hidden line-clamp-2 h-10">
                    <Text>{item.name}</Text>
                </View>
                <View className="flex flex-nowrap justify-between items-center">
                    <View>
                        <Text className="text-gray-400 text-xs">
                            {used ? '二手车' : '官方发布'}
                        </Text>
                    </View>
                    <View>
                        <Text className="text-red-500 text-xs font-semibold">
                            ¥{item.price}起
                        </Text>
                    </View>
                </View>
            </View>
        </View>
    )
}

interface RvListProps {
    used: boolean,
    isPullDownRefresh?: boolean;
    isReachBottomRefresh?: boolean;
    changePageTitle?: boolean;
}

const RvList = ({
    used = false,
    isPullDownRefresh = false,
    isReachBottomRefresh = false
}: RvListProps) => {
    const {
        rvList,
        loading,
        refresh,
        loadMore,
        hasMore,
    } = useRvList();

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

    // 注册下拉刷新
    if (isPullDownRefresh) {
        Taro.usePullDownRefresh(handlePullDownRefresh);
    }

    // 注册触底加载
    if (isReachBottomRefresh) {
        Taro.useReachBottom(handleReachBottom);
    }

    return (
        <View>
            {rvList.map(item => (
                <RvItem
                    item={item}
                    used={used}
                />
            ))}

            {rvList.length === 0 && !loading && (
                <View className="flex justify-center items-center h-64">
                    <Text className="text-gray-500">尚未发布房车</Text>
                </View>
            )}

            {loading && (
                <View className="flex justify-center items-center h-64">
                    <Loading />
                </View>
            )}

            {!hasMore && rvList.length > 0 && (
                <View className="text-center text-gray-500 text-sm py-4">
                    没有更多数据了
                </View>
            )}
        </View>
    )
}

export default RvList;