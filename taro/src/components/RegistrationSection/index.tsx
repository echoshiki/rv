import { View, Text } from "@tarojs/components";
import Taro from "@tarojs/taro";
import Card from '@/components/Card';
import Loading from "@/components/Loading";
import { useState } from "react";
import { checkLogin } from '@/utils/auth';
import { checkRegistrationStatus } from '@/utils/common';
import RegistrationButton from './RegistrationButton';
import RegistrationForm from './RegistrationForm';

/**
 * 页面报名区域组件
 * @param param activityId 活动 ID
 * @returns 
 */
const RegistrationSection = ({ activityId }) => {
    // 表单可视状态
    const [isFormVisible, setIsFormVisible] = useState(false);

    // 立即报名按钮状态
    const [buttonDisabled, setButtonDisabled] = useState(false);
    const [buttonVisible, setButtonVisible] = useState(true);
    const [buttonLoading, setButtonLoading] = useState(false);

    const handleRegistrationButton = async (id: string | undefined) => {
        // 检查登录
        if (!checkLogin()) {
            return;
        };

        if (!id) return;

        const status = await checkRegistrationStatus(id);
  
        if (status.isRegistration === false) {
            setButtonDisabled(true);
            Taro.showToast({
                icon: 'none',
                title: status.message
            });
            return;
        }

        setButtonVisible(false);
        setButtonLoading(true);

        // 虚拟延迟效果
        setTimeout(() => {
            setIsFormVisible(true);
            setButtonLoading(false);
        }, 500);
    }

    const handleFormSubmit = async (data: { name: string, phone: string, address: string }) => {
        // 提交表单
        console.log(data);
    }
    
    return (
        <Card className="mt-5">
            <View>
                <RegistrationButton
                    disabled={buttonDisabled}
                    visible={buttonVisible}
                    onClick={() => handleRegistrationButton(activityId)}
                />
            </View>
            {buttonLoading && (
                <View className="flex justify-center">
                    <Loading />
                </View>
            )}
            <RegistrationForm
                onSubmit={handleFormSubmit}
                isVisible={isFormVisible}
            />

            <View className="mt-2">
                <Text className="text-xs font-light block">* 请务必填写真实信息，以便我们与您取得联系</Text>
                <Text className="text-xs font-light block">* 活动最终解释权归主办方所有</Text>
            </View>
        </Card>
    );
}

export default RegistrationSection;