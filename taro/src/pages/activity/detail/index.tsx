import { View, Text, Image, RichText } from "@tarojs/components";
import DefaultCover from '@/assets/images/cover.jpg';
import { useActivityDetail } from "@/hooks/useActivityDetail";
import Taro from "@tarojs/taro";
import Loading from "@/components/Loading";
import { cleanHTML } from '@/utils/common';

import { Button, Tag } from '@nutui/nutui-react-taro'

const Detail = () => {

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
                <View className="relative block h-0 p-0 overflow-hidden pb-[50%]">
                    {/* 封面图片 */}
                    <Image
                        src={activityDetail?.cover || DefaultCover}
                        className="absolute object-cover w-full h-full border-none align-middle"
                        mode={`aspectFill`}
                    />
                </View>
            </View>
            <View className="px-5 relative mt-[-3rem]">
                <View className="bg-white rounded-xl p-5 pb-10">
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
                                    <Text className="text-xs font-light font-mono">
                                        {activityDetail?.registration_start_at ? activityDetail?.registration_start_at : '即日起'} 
                                        {' 至 '}
                                        {activityDetail?.registration_end_at ? activityDetail?.registration_end_at : '不定期'}
                                    </Text>
                                </View>
                            )}

                            {activityDetail?.started_at && activityDetail?.ended_at && (
                            <View className="flex flex-nowrap items-center space-x-2">
                                <Text className="text-xs font-light">活动时间</Text>
                                <Text className="text-xs font-light font-mono">
                                    {activityDetail?.started_at ? activityDetail?.started_at : '即日起'} 
                                    {' 至 '}
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
                                    <Text className="text-red-500">{activityDetail?.registration_fee} 元</Text>
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

                    {/* 底部报名按钮 */}
                    <View className="mt-5">
                        <Button 
                            className="w-full rounded-sm bg-gray-900 text-white text-sm h-10 flex items-center justify-center font-semibold"
                            onClick={() => {
                               Taro.showToast({
                                   title: '报名成功',
                                   icon: 'success'
                               }) 
                            }}
                        >
                            立即报名
                        </Button>
                    </View>
                </View>
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