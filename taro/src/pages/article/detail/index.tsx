import { View, Text, RichText } from "@tarojs/components";
import DefaultCover from '@/assets/images/cover.jpg';
import { useArticleDetail } from "@/hooks/useArticleDetail";
import Taro from "@tarojs/taro";
import { type ArticleDetailQueryParams } from "@/api/article";
import { ArticleDetailSkeleton } from '@/components/Skeleton';
import { cleanHTML } from '@/utils/common';
import Card from '@/components/Card';
import AspectRatioImage from "@/components/AspectRatioImage";
import BlankPage from "@/components/BlankPage";

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

    if (loading) {
        return (
            <ArticleDetailSkeleton />
        );
    }

    if (!articleDetail) {
        return (
            <BlankPage title="页面未找到" description="文章未找到或者已被删除" />
        );
    }

    return (
        <View className="bg-gray-100 min-h-screen pb-5">
            <View className="w-full">
                <AspectRatioImage
                    src={articleDetail.cover || DefaultCover}
                    ratio={.5}
                    rounded="none"
                />
            </View>
            <View className="px-5 relative mt-[-3rem]">
                <Card>
                    {articleDetail.is_single_page ? (
                        // 单页标题 UI
                        <View className="mb-5 text-center">
                            <Text className="text-xl font-bold leading-relaxed">
                                {articleDetail.title}
                            </Text>
                            <View className="h-[1px] w-1/6 bg-black mt-3 mx-auto"></View>
                        </View>
                    ) : (
                        // 常规标题 UI
                        <View className="mb-5">
                            <Text className="text-xl font-bold text-justify leading-relaxed">
                                {articleDetail.title}
                            </Text>
                            <View className="flex flex-nowrap space-x-3 mt-3 text-xs font-light">
                                <Text className="text-gray-500">{articleDetail.date}</Text>
                                <Text className="underline">{articleDetail.category?.title}</Text>
                            </View>
                        </View>
                    )}
                    
                    <View>
                        <RichText
                            className="font-light text-left leading-loose"
                            nodes={cleanHTML(articleDetail.content || '')}
                        />
                    </View>
                </Card>
            </View>
        </View>
    );
}

export default Detail;