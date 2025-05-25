import { View, Text, RichText } from "@tarojs/components";
import DefaultCover from '@/assets/images/cover.jpg';
import { useActivityDetail } from "@/hooks/useActivityDetail";
import Taro from "@tarojs/taro";
import { cleanHTML } from '@/utils/common';
import { Tag, Price, Button } from '@nutui/nutui-react-taro';
import Card from '@/components/Card';
import AspectRatioImage from '@/components/AspectRatioImage';
import RegistrationForm from '@/components/RegistrationForm';
import Loading from "@/components/Loading";
import { useState } from "react";
import { checkLogin } from '@/utils/auth';
import { checkRegistrationStatus } from '@/utils/common';

// 立即登录按钮
const RegistrationButton = ({ disabled, visible, onClick }: {
    disabled: boolean,
    visible: boolean,
    onClick: () => void
}) => {

    return (
        <>
        {visible && (
            <View className="text-center">
                <Button
                    type="primary"
                    disabled={disabled}
                    onClick={onClick}
                >
                    立即报名
                </Button>
            </View>
        )}  
        </>
    )
}

const Detail = () => {

    const { router } = Taro.getCurrentInstance();
    const id = router?.params?.id;

    // 表单可视状态
    const [isFormVisible, setIsFormVisible] = useState(false);

    // 立即报名按钮状态
    const [buttonDisabled, setButtonDisabled] = useState(false);
    const [buttonVisible, setButtonVisible] = useState(true);
    const [buttonLoading, setButtonLoading] = useState(false);

    const {
        activityDetail,
        loading
    } = useActivityDetail(id);

    Taro.useDidShow(() => {
        if (!id) {
            Taro.navigateTo({
                url: '/pages/404/index'
            });
        }
    });

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

    return (
        <View className="bg-gray-100 min-h-screen pb-5">
            <View className="w-full">
                <AspectRatioImage
                    src={activityDetail?.cover || DefaultCover}
                    ratio={.5}
                />
            </View>
            <View className="px-5 relative mt-[-3rem]">
                <Card>
                    {/* 标题 */}
                    <View className="mb-5">
                        <Text className="text-xl font-bold text-justify leading-relaxed">
                            {activityDetail?.title}
                        </Text>
                        <View className="flex flex-nowrap items-center space-x-3 mt-3 text-xs font-light">
                            <Text className="text-gray-500">{activityDetail?.date}</Text>
                            <Tag type='info'>{activityDetail?.category?.title}</Tag>
                        </View>
                    </View>

                    {/* 活动信息 */}
                    <View className="bg-gray-100 p-5 rounded mb-5">
                        <View className="flex flex-col space-y-2">

                            {activityDetail?.registration_start_at && activityDetail?.registration_end_at && (
                                <View className="flex flex-nowrap items-center space-x-2">
                                    <Text className="text-xs font-light">报名时间</Text>
                                    <Text className="text-xs font-light">
                                        {activityDetail?.registration_start_at ? activityDetail?.registration_start_at : '即日起'}
                                        {' - '}
                                        {activityDetail?.registration_end_at ? activityDetail?.registration_end_at : '不定期'}
                                    </Text>
                                </View>
                            )}

                            {activityDetail?.started_at && activityDetail?.ended_at && (
                                <View className="flex flex-nowrap items-center space-x-2">
                                    <Text className="text-xs font-light">活动时间</Text>
                                    <Text className="text-xs font-light">
                                        {activityDetail?.started_at ? activityDetail?.started_at : '即日起'}
                                        {' - '}
                                        {activityDetail?.ended_at ? activityDetail?.ended_at : '不定期'}
                                    </Text>
                                </View>
                            )}

                            {activityDetail?.registration_fee && (
                                <View className="flex flex-nowrap items-center space-x-2">
                                    <Text className="text-xs font-light">报名费用</Text>
                                    {activityDetail?.registration_fee === '0.00' ? (
                                        <Tag type="success">免费</Tag>
                                    ) : (
                                        <Price size="normal" price={activityDetail?.registration_fee} />
                                    )}
                                </View>
                            )}

                        </View>
                    </View>
                    {/* 内容 */}
                    <View>
                        <RichText
                            className="font-light text-left leading-loose"
                            nodes={cleanHTML(activityDetail?.content || '')}
                        />
                    </View>
                </Card>

                {/* 底部报名表单 */}
                <Card className="mt-5">
                    <View>
                        <RegistrationButton
                            disabled={buttonDisabled}
                            visible={buttonVisible}
                            onClick={() => handleRegistrationButton(id)}
                        />
                    </View>
                    {buttonLoading && (
                        <View className="flex justify-center">
                            <Loading />
                        </View>
                    )}
                    <RegistrationForm
                        onSubmit={(data) => {
                            console.log('表单数据', data);
                            setIsFormVisible(false);
                        }}
                        isVisible={isFormVisible}
                    />

                    <View className="mt-2">
                        <Text className="text-xs font-light block">* 请务必填写真实信息，以便我们与您取得联系</Text>
                        <Text className="text-xs font-light block">* 活动最终解释权归主办方所有</Text>
                    </View>
                </Card>
            </View>

            {/* 加载状态展示 */}
            {loading && (
                <View className="flex justify-center py-4">
                    <Loading />
                </View>
            )}

            {/* 加载完成提示 */}
            {!activityDetail && (
                <View className="text-center text-gray-500 text-sm py-4">
                    活动未找到或者已被删除
                </View>
            )}

        </View>
    );
}

export default Detail;