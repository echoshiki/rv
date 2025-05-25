import { View, Text, RichText } from "@tarojs/components";
import DefaultCover from '@/assets/images/cover.jpg';
import { useActivityDetail } from "@/hooks/useActivityDetail";
import Taro from "@tarojs/taro";
import { cleanHTML } from '@/utils/common';
import { Tag } from '@nutui/nutui-react-taro';
import Card from '@/components/Card';
import AspectRatioImage from '@/components/AspectRatioImage';
import Loading from "@/components/Loading";
import RegistrationSection from '@/components/RegistrationSection';
import ActivityNote from '@/components/ActivityNote';

const Detail = () => {
    // 获取活动 ID
    const { router } = Taro.getCurrentInstance();
    const id = router?.params?.id;

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

                    {/* 活动相关信息 */}
                    <View className="bg-gray-100 p-5 rounded mb-5">
                        <ActivityNote activityDetail={activityDetail} />
                    </View>
                    
                    {/* 内容 */}
                    <View>
                        <RichText
                            className="font-light text-left leading-loose"
                            nodes={cleanHTML(activityDetail?.content || '')}
                        />
                    </View>
                </Card>

                {/* 活动报名区域 */}
                <RegistrationSection activityId={id} />
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