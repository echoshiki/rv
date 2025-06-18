import { getCurrentInstance, makePhoneCall } from "@tarojs/taro";
import { useRvDetail } from "@/hooks/useRvDetail";
import { View, RichText, Button, Text } from "@tarojs/components";
import { cleanHTML } from '@/utils/common';
import { ArticleItemSkeleton } from '@/components/Skeleton';
import { useBookingFlow } from "@/hooks/useRvOrder";
import { Dialog } from '@nutui/nutui-react-taro'
import { RvItem } from "@/types/ui";
import { useState } from "react";
import useSettingStore from "@/stores/setting";

const RvBookingDialog = ({
    item,
    visible,
    loading,
    onConfirm,
    onCancel
}: {
    item: RvItem | null;
    visible: boolean;
    loading: boolean;
    onConfirm: () => void;
    onCancel: () => void;
}) => {
    return (
        <Dialog
            title="确定订单"
            visible={visible}
            confirmText="确认下订"
            onConfirm={onConfirm}
            onCancel={onCancel}
            disableConfirmButton={loading}
        >
            {item && (
                <View className="flex flex-col space-y-2 py-5">
                    <View>
                        <Text>订购车型：{item.name}</Text>
                    </View>
                    <View>
                        <Text>车型售价：{item.price}</Text>
                    </View>
                    <View>
                        <Text>订购定金：{item.order_price}</Text>
                    </View>
                    <View>
                        <Text>支付金额：{item.order_price}</Text>
                    </View>
                </View>
            )}
        </Dialog>
    )
}

const RvDetail = () => {
    const { router } = getCurrentInstance();
    const id = router?.params?.id ?? '';
    const used = router?.params?.used ?? ''

    // 获取页面的基础信息
    const { rvDetail, loading: pageLoading } = useRvDetail(id, Boolean(used));

    // 确认订购对话框状态
    const [isDialogVisible, setIsDialogVisible] = useState(false);

    // 实例化订购流程
    const { isProcessing, startBookingFlow } = useBookingFlow();

    // 只需传入 id 即可发起支付流程
    const handleBookingClick = async () => {
        if (rvDetail) {
            await startBookingFlow(rvDetail.id);
        }
    };

    const handlePhoneCallClick = () => {
        const phoneNumber = useSettingStore.getState().settings.phone ?? '';
        makePhoneCall({
            phoneNumber: phoneNumber
        });
    };

    if (pageLoading) {
        return (
            <View className="flex justify-center py-4">
                <ArticleItemSkeleton />
            </View>
        );
    }

    if (!rvDetail) {
        return (
            <View className="text-center text-gray-500 text-sm py-4">
                车型尚未发布或者已删除
            </View>
        )
    }

    return (
        <View>
            <View>
                <RichText
                    className="font-light text-left leading-loose"
                    nodes={cleanHTML(rvDetail?.content || '', true)}
                />
            </View>

            {/* 底部按钮 */}
            <View className="w-full flex flex-nowrap bg-black bg-opacity-85 p-6 items-center fixed bottom-0">
                <Button
                    className="w-24 text-[.8rem] bg-red-600 text-white !border-solid !border-red-600 !border-2"
                    onClick={() => setIsDialogVisible(true)}
                    loading={isProcessing}
                    disabled={isProcessing}
                >
                    现在下订
                </Button>
                <Button 
                    className="w-24 text-[.8rem] !border border-solid !border-white !bg-transparent !text-white"
                    openType="contact"
                >
                    在线咨询
                </Button>
                <Button 
                    className="w-24 text-[.8rem] !border border-solid !border-white !bg-transparent !text-white"
                    onClick={handlePhoneCallClick}
                >
                    客服电话
                </Button>
            </View>

            {/* 确认订购对话框 */}
            <RvBookingDialog
                item={rvDetail as RvItem}
                visible={isDialogVisible}
                loading={isProcessing}
                onConfirm={handleBookingClick}
                onCancel={() => setIsDialogVisible(false)}
            />
        </View>
    );
}

export default RvDetail;