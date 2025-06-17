import { View, Text } from '@tarojs/components';
import DefaultCover from '@/assets/images/cover.jpg';
import { RegistrationItem as RegistrationItemProps } from '@/types/ui';
import { useRegistrationList } from '@/hooks/useRegistrationList';
import Taro from '@tarojs/taro';
import Loading from '@/components/Loading';
import { Tag } from '@nutui/nutui-react-taro';
import { Dialog } from '@nutui/nutui-react-taro'
import { useState } from 'react';
import Card from '@/components/Card';
import AspectRatioImage from '@/components/AspectRatioImage';
import usePayment from '@/hooks/usePayment';

const RegistrationDialog = ({
    item,
    visible,
    loading,
    onConfirm,
    onCancel
}: {
    item: RegistrationItemProps | null;
    visible: boolean;
    loading: boolean;
    onConfirm: () => void;
    onCancel: () => void;
}) => {
    return (
        <Dialog
            title="报名详情"
            visible={visible}
            confirmText={item?.status.value === 'pending' ? '立即支付' : '确认'}
            onConfirm={onConfirm}
            onCancel={onCancel}
            disableConfirmButton={loading}
        >
            {item && (
                <View className="flex flex-col space-y-2 py-5">
                    <View>
                        <Text>报名人：{item.name}</Text>
                    </View>
                    <View>
                        <Text>手机号：{item.phone}</Text>
                    </View>
                    <View>
                        <Text>报名时间：{item.created_at}</Text>
                    </View>
                    <View>
                        <Text>报名编号：{item.registration_no}</Text>
                    </View>
                    <View>
                        <Text>报名状态：{item.status.label}</Text>
                    </View>
                    <View>
                        <Text>支付金额：{item.fee}</Text>
                    </View>
                </View>
            )}
        </Dialog>
    )
}

const RegistrationItem = ({ 
    item,
    onClickItem
}: {
    item: RegistrationItemProps;
    onClickItem: (item: RegistrationItemProps) => void;
}) => {
    return (
        <View
            className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed"
            onClick={() => onClickItem(item)}
        >
            <View className="w-14">
                <AspectRatioImage
                    src={item.activity.cover ? item.activity.cover : DefaultCover}
                    ratio={1}
                    rounded="full"
                />
            </View>
            <View className="h-12 flex-1 flex flex-col space-y-2">
                <View className="text-sm text-ellipsis overflow-hidden line-clamp-2">
                    <Text>{item.activity.title}</Text>
                </View>
                <View className="flex flex-row items-center space-x-2">
                    <View>
                        <Tag type={item.status.color}>{item.status.label}</Tag>
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

    const [isDialogVisible, setIsDialogVisible] = useState(false);
    const [dialogItem, setDialogItem] = useState<RegistrationItemProps | null>(null);

    const {
        registrationList,
        loading,
        refresh,
        loadMore,
        hasMore,
    } = useRegistrationList();

    const { startPayment, isPaying } = usePayment();

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

    const handleClickItem = (item: RegistrationItemProps) => {
        setIsDialogVisible(true);
        setDialogItem(item);
    };

    // 处理弹窗按钮函数
    const handleDialogConfirm =  async () => {
        if (!dialogItem) return;

        if (dialogItem?.status.value === 'pending') {
            const result = await startPayment({
                orderId: dialogItem.id,
                orderType: 'activity',
            });

            if (result.success) {
                setIsDialogVisible(false);
                await refresh();
            }

            return;
        }
        setIsDialogVisible(false);
    };

    return (
        <Card>
            <View>
                {registrationList.map(item => (
                    <RegistrationItem
                        item={item}
                        onClickItem={handleClickItem}
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
            <RegistrationDialog
                item={dialogItem}
                visible={isDialogVisible}
                loading={isPaying}
                onConfirm={handleDialogConfirm}
                onCancel={() => setIsDialogVisible(false)}
            />
        </Card>
    )
}

export default RegistrationList;