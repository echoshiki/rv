import { View, Text } from '@tarojs/components';
import DefaultCover from '@/assets/images/cover.jpg';
import { useRvOrderList } from '@/hooks/useRvOrderList';
import Taro from '@tarojs/taro';
import Loading from '@/components/Loading';
import { StatusBadge } from '@/components/CustomBadge';
import { Tag } from '@nutui/nutui-react-taro';
import { Dialog } from '@nutui/nutui-react-taro'
import { useState } from 'react';
import Card from '@/components/Card';
import AspectRatioImage from '@/components/AspectRatioImage';
import { RvOrderItem as RvOrderItemProps } from '@/types/ui';

const RvOrderDialog = ({
    item,
    visible,
    onConfirm,
    onCancel
}: {
    item: RvOrderItemProps | null;
    visible: boolean;
    onConfirm: () => void;
    onCancel: () => void;
}) => {
    return (
        <Dialog
            title="预定详情"
            visible={visible}
            confirmText={item?.status.value === 'pending' ? '立即支付' : '确认'}
            onConfirm={onConfirm}
            onCancel={onCancel}
        >
            {item && (
                <View className="flex flex-col space-y-2 py-5">
                    <View>
                        <Text>订单号：{item.order_no}</Text>
                    </View>
                    <View>
                        <Text>订购金额：{item.deposit_amount}</Text>
                    </View>
                    <View>
                        <Text>预定时间：{item.created_at}</Text>
                    </View>
                    <View>
                        <Text>预定状态：{item.status.label}</Text>
                    </View>
                </View>
            )}
        </Dialog>
    )
}

const RvOrderItem = ({ 
    item,
    onClickItem
}: {
    item: RvOrderItemProps;
    onClickItem: (item: RvOrderItemProps) => void;
}) => {
    return (
        <View
            className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed"
            onClick={() => onClickItem(item)}
        >
            <View className="w-14">
                <AspectRatioImage
                    src={item.rv.cover ? item.rv.cover : DefaultCover}
                    ratio={1}
                    rounded="full"
                />
            </View>
            <View className="h-12 flex-1 flex flex-col space-y-2">
                <View className="text-sm text-ellipsis overflow-hidden line-clamp-2">
                    <Text>{item.rv.name}</Text>
                </View>
                <View className="flex flex-row items-center space-x-2">
                    <View>
                        <Tag type={item.status.color}>{item.status.label}</Tag>
                    </View>
                    <View>
                        <Text className="text-gray-400 text-xs">
                            预定日期：{item.created_at}
                        </Text>
                    </View>
                </View>
            </View>
        </View>
    )
}

interface RvOrderListProps {
    isPullDownRefresh?: boolean;
    isReachBottomRefresh?: boolean;
}

const RvOrderList = ({
    isPullDownRefresh = false,
    isReachBottomRefresh = false,
}: RvOrderListProps) => {

    const [isDialogVisible, setIsDialogVisible] = useState(false);
    const [dialogItem, setDialogItem] = useState<RvOrderItemProps | null>(null);

    const {
        rvOrderList,
        loading,
        refresh,
        loadMore,
        hasMore,
    } = useRvOrderList();

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

    const handleClickItem = (item: RvOrderItemProps) => {
        setIsDialogVisible(true);
        setDialogItem(item);
    };

    // 处理弹窗按钮函数
    const handleDialogConfirm = () => {
        if (dialogItem?.status.value === 'pending') {
            Taro.showToast({
                icon: 'success',
                title: '模拟支付成功'
            });
            return;
        }
        setIsDialogVisible(false);
    };

    return (
        <Card>
            <View>
                {rvOrderList.map(item => (
                    <RvOrderItem
                        item={item}
                        onClickItem={handleClickItem}
                    />
                ))}

                {rvOrderList.length === 0 && !loading && (
                    <View className="flex justify-center items-center h-64">
                        <Text className="text-gray-500">你还没有订购任何房车</Text>
                    </View>
                )}

                {loading && (
                    <View className="flex justify-center items-center h-64">
                        <Loading />
                    </View>
                )}

                {!hasMore && rvOrderList.length > 0 && (
                    <View className="text-center text-gray-500 text-sm py-4">
                        没有更多数据了
                    </View>
                )}
            </View>
            <RvOrderDialog
                item={dialogItem}
                visible={isDialogVisible}
                onConfirm={handleDialogConfirm}
                onCancel={() => setIsDialogVisible(false)}
            />
        </Card>
    )
}

export default RvOrderList;