import Taro from "@tarojs/taro";
import { useRvDetail } from "@/hooks/useRvDetail";
import { View, RichText } from "@tarojs/components";
import Loading from "@/components/Loading";
import { cleanHTML } from '@/utils/common';

import { ArticleItemSkeleton } from '@/components/Skeleton';

const RvDetail = () => {

    const { router } = Taro.getCurrentInstance();
    const id = router?.params?.id ?? '';

    const {
        rvDetail,
        loading
    } = useRvDetail(id);

    return (
        <View>
            <ArticleItemSkeleton />
            {/* <View className="relative">
                <View>
                    <RichText
                        className="font-light text-left leading-loose"
                        nodes={cleanHTML(rvDetail?.content || '', true)}
                    />
                </View>
            </View> */}

            {/* 加载状态展示 */}
            {loading && (
                <View className="flex justify-center py-4">
                    <Loading />
                </View>
            )}

            {/* 加载完成提示 */}
            {!rvDetail && (
                <View className="text-center text-gray-500 text-sm py-4">
                    房车未发布或者已删除
                </View>
            )}
        </View>
    );
}

export default RvDetail;