import { View, Text, Image, RichText } from "@tarojs/components";
import DefaultCover from '@/assets/images/cover.jpg';
import { useArticleDetail } from "@/hooks/useArticleDetail";
import Taro from "@tarojs/taro";
import { type ArticleDetailQueryParams } from "@/api/article";
import Loading from "@/components/Loading";
import { cleanHTML } from '@/utils/common';

const Detail = () => {

    const { router } = Taro.getCurrentInstance();
    const queryParams: ArticleDetailQueryParams = router?.params || {};

    const {
        articleDetail,
        loading
    } = useArticleDetail(queryParams);

    Taro.useDidShow(() => {
        if (!queryParams.id && !queryParams.code) {
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
                        src={articleDetail?.cover || DefaultCover}
                        className="absolute object-cover w-full h-full border-none align-middle"
                        mode={`aspectFill`}
                    />
                </View>
            </View>
            <View className="px-5 relative mt-[-3rem]">
                <View className="bg-white rounded-xl p-5 pb-10">
                    <View className="mb-5">
                        <Text className="text-xl font-bold text-justify leading-relaxed">
                            {articleDetail?.title}
                        </Text>
                        <View className="flex flex-nowrap space-x-3 mt-3 text-xs font-light">
                            <Text className="text-gray-500">{articleDetail?.date}</Text>
                            <Text className="underline">{articleDetail?.category?.title}</Text>
                        </View>
                    </View>
                    <View>
                        <RichText
                            className="font-light text-left leading-loose"
                            nodes={cleanHTML(articleDetail?.content || '')}
                        />
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
            {!articleDetail && (
                <View className="text-center text-gray-500 text-sm py-4">
                    文章未找到或者已被删除
                </View>
            )}
        </View>
    );
}

export default Detail;