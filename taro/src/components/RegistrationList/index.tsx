import { View, Text, Image } from '@tarojs/components';
import DefaultCover from '@/assets/images/cover.jpg';
import { RegistrationItem as RegistrationItemProps } from '@/types/ui';
import { mapsTo } from '@/utils/common';
import { useRegistrationList } from '@/hooks/useRegistrationList';
import Taro from '@tarojs/taro';
import Loading from '@/components/Loading';
import { Tag } from '@nutui/nutui-react-taro';

const RegistrationItem = ({ item }: { item: RegistrationItemProps }) => {
    return (
        <View
            className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed"
            onClick={() => mapsTo(`/pages/activity/detail/index?id=${item.activity_id}`)}
        >
            <View className="w-14">
                <View className="relative block h-0 p-0 overflow-hidden pb-[100%] rounded-full">
                    <Image
                        src={item.activity.cover ? item.activity.cover : DefaultCover}
                        className="absolute object-cover w-full h-full border-none align-middle"
                        mode={`aspectFill`}
                    />
                </View>
            </View>
            <View className="h-12 flex-1 flex flex-col space-y-2">
                <View className="text-sm text-ellipsis overflow-hidden line-clamp-2">
                    <Text>{item.activity.title}</Text>
                </View>
                <View className="flex flex-row justify-between items-center">
                    <View>
                        <Tag type="success">{item.status}</Tag>
                    </View>
                    <View>
                        <Text className="text-gray-400 text-xs">
                            报名日期：{item.created_at}
                        </Text>
                    </View>
                </View>
            </View>
        </View>
    )
}

interface ArticleListProps {
    isPullDownRefresh?: boolean;
    isReachBottomRefresh?: boolean;
}

const RegistrationList = ({
    isPullDownRefresh = false,
    isReachBottomRefresh = false,
}: ArticleListProps) => {
    const {
        registrationList,
        loading,
        refresh,
        loadMore,
        hasMore,
    } = useRegistrationList();

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
                {registrationList.map(item => (
                    <RegistrationItem
                        item={item}
                    />
                ))}

                {registrationList.length === 0 && !loading && (
                    <View className="flex justify-center items-center h-64">
                        <Text className="text-gray-500">你还没有报名任何活动</Text>
                    </View>
                )}

                {loading && (
                    <View className="flex justify-center items-center h-64">
                        <Loading />
                    </View>
                )}

                {!hasMore && registrationList.length > 0 && (
                    <View className="text-center text-gray-500 text-sm py-4">
                        没有更多数据了
                    </View>
                )}
            </View>
        </View>
    )
}

export default RegistrationList;