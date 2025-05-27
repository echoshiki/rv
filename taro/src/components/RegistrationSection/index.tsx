import { View, Text, Button } from "@tarojs/components";
import Taro from "@tarojs/taro";
import Card from '@/components/Card';
import Loading from "@/components/Loading";
import { useState } from "react";
import { checkLogin } from '@/utils/auth';
import RegistrationButton from './RegistrationButton';
import RegistrationForm from './RegistrationForm';
import { useRegistrationFlow, RegistrationStatusCode, RegistrationStep } from '@/hooks/useRegistration';
import { ActivityDetail, RegistrationFormData } from "@/types/ui";

interface RegistrationSectionProps {
    activityDetail: ActivityDetail;
}

/**
 * 页面报名区域组件
 * @param param activityId 活动 ID
 * @returns 
 */
const RegistrationSection = ({ activityDetail }: RegistrationSectionProps) => {
    // 按钮禁用状态（用于处理不可报名的情况）
    const [buttonDisabled, setButtonDisabled] = useState(false);
    // 虚拟加载状态（用于 UI 过渡效果）
    const [showTransitionLoading, setShowTransitionLoading] = useState(false);

    // 使用报名流程 Hook
    const {
        currentStep,
        checking,
        submitting,
        paying,
        handleCheckStatus,
        handleFormSubmit,
        handlePayment,
        registration
    } = useRegistrationFlow({
        activityDetail
    });

    // 处理检测
    const handleRegistrationButton = async () => {
        if (!checkLogin()) return;

        try {
            const statusCode = await handleCheckStatus();
            switch (statusCode) {
                case RegistrationStatusCode.ACTIVITY_NOT_STARTED:
                    setButtonDisabled(true);
                    Taro.showToast({
                        icon: 'none',
                        title: '活动报名未开始'
                    });
                    break;
                case RegistrationStatusCode.ACTIVITY_ENDED:
                    setButtonDisabled(true);
                    Taro.showToast({
                        icon: 'none',
                        title: '活动报名已截止'
                    });
                    break;
                case RegistrationStatusCode.ACTIVITY_FULL:
                    setButtonDisabled(true);
                    Taro.showToast({
                        icon: 'none',
                        title: '活动名额已满'
                    });
                    break;
                case RegistrationStatusCode.ALREADY_REGISTERED:
                    setButtonDisabled(true);
                    Taro.showToast({
                        icon: 'none',
                        title: '您已报名，无需重复报名'
                    });
                    break;
                case RegistrationStatusCode.PENDING_PAYMENT:
                    setButtonDisabled(true);
                    Taro.showToast({
                        icon: 'none',
                        title: '存在尚未付款的报名信息，请去个人中心查看'
                    });
                    break;
                case RegistrationStatusCode.NOT_ELIGIBLE:
                    setButtonDisabled(true);
                    Taro.showToast({
                        icon: 'none',
                        title: '暂无报名资格'
                    });
                    break;
                case RegistrationStatusCode.SUCCESS:
                    // 检查成功，显示过渡加载效果
                    setShowTransitionLoading(true);
                    // 虚拟延迟效果，然后自动切换到表单
                    setTimeout(() => {
                        setShowTransitionLoading(false);
                        // currentStep 已经在 Hook 内部自动设置为 'form'
                    }, 500);
                    break;
            }
        } catch (error) {
            console.log('检查报名状态失败', error);
            Taro.showToast({
                icon: 'error',
                title: '检查失败，请重试'
            });
        }
    }

    // 处理提交
    const handleFormSubmitWithFeedback = async (data: RegistrationFormData) => {
        try {
            await handleFormSubmit(data);
            Taro.showToast({
                icon: 'success',
                title: '报名成功！'
            });

            // 根据是否需要支付自动跳转
        } catch (error) {

            Taro.showToast({
                icon: 'none',
                title: '报名遇到一些问题，请稍后再试',
                duration: 3000
            });
            console.error('提交报名失败:', error);
        }
    }

    // 处理支付
    const handlePaymentWithFeedback = async () => {
        try {
            await handlePayment();
            Taro.showToast({
                icon: 'success',
                title: '支付成功！'
            });
        } catch (error) {
            console.error('支付失败:', error);

            // 支付取消不显示错误提示
            if (!error.errMsg?.includes('cancel')) {
                Taro.showToast({
                    icon: 'error',
                    title: '支付失败，请重试'
                });
            }
        }
    }

    // 根据步骤渲染内容
    const renderStepContent = () => {
        switch (currentStep) {
            // 初始阶段
            case RegistrationStep.INITIAL:
                return (
                    <View>
                        <RegistrationButton
                            disabled={buttonDisabled}
                            visible={true}
                            loading={checking}
                            onClick={handleRegistrationButton}
                        />
                        {/* 过渡加载效果 */}
                        {showTransitionLoading && (
                            <View className="flex justify-center mt-4">
                                <Loading />
                            </View>
                        )}
                    </View>
                );
            case RegistrationStep.FORM:
                return (
                    <RegistrationForm
                        onSubmit={handleFormSubmitWithFeedback}
                        loading={submitting}
                        isVisible={true}
                    />
                );
            case RegistrationStep.PAYMENT:
                return (
                    <View className="text-center py-5 flex flex-col space-y-5">
                        <View className="flex flex-col">
                            <Text className="text-lg font-bold text-green-600">
                                只差一步！
                            </Text>
                            <Text className="text-xs text-gray-600">
                                报名信息提交成功，请完成支付以确认报名
                            </Text>
                        </View>
                        <View className="bg-gray-50 p-5 rounded-lg flex flex-col space-y-2">
                            <Text className="text-xl font-bold text-orange-500">
                                ¥{activityDetail.registration_fee}
                            </Text>
                            <View className="flex justify-center">
                                <Button
                                    type="primary"
                                    className="text-[.8rem] w-full"
                                    loading={paying}
                                    onClick={handlePaymentWithFeedback}
                                >
                                    立即支付
                                </Button>
                            </View>
                        </View>  
                    </View>
                );
            case RegistrationStep.SUCCESS:
                return (
                    <View className="text-center py-5">
                        <View className="flex flex-col">
                            <Text className="text-lg font-bold text-green-600">
                                {activityDetail.registration_fee > 0 ? '支付成功！' : '报名成功！'}
                            </Text>
                            <Text className="text-xs text-gray-600">
                                我们会尽快与您联系，请保持手机畅通
                            </Text>
                        </View>
                        {registration && (
                            <View className="text-left mt-4 p-4 bg-gray-50 rounded-lg text-xs flex flex-col space-y-1">
                                <Text>姓名：{registration.name}</Text>
                                <Text>联系方式：{registration.phone}</Text>
                                <Text>报名编号：{registration.registration_no}</Text>
                                <Text>报名时间：{registration.created_at}</Text>
                            </View>
                        )}
                    </View>
                );
            default:
                return null;
        }
    }

    return (
        <Card className="mt-5">
            {renderStepContent()}

            {/* 底部说明文字 */}
            <View className="mt-4">
                <Text className="text-xs font-light block">* 请务必填写真实信息，以便我们与您取得联系</Text>
                <Text className="text-xs font-light block">* 活动最终解释权归主办方所有</Text>
            </View>
        </Card>
    );
}

export default RegistrationSection;