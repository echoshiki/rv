import { getCurrentInstance } from "@tarojs/taro";
import { useRvDetail } from "@/hooks/useRvDetail";
import { View, RichText, Button } from "@tarojs/components";
import { cleanHTML } from '@/utils/common';
import { ArticleItemSkeleton } from '@/components/Skeleton';

const RvDetail = () => {
    const { router } = getCurrentInstance();
    const id = router?.params?.id ?? '';
    const used = router?.params?.used ?? ''

    const {
        rvDetail,
        loading
    } = useRvDetail(id, Boolean(used));

    if (loading) {
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
            <View className="w-full flex flex-nowrap bg-black bg-opacity-85 p-5 items-center fixed bottom-0">
                <Button
                    className="w-52 text-[.8rem] !border border-solid !border-white !bg-transparent !text-white"
                >
                    现在下订
                </Button>
                <Button 
                    className="w-28 text-[.8rem] bg-red-600 text-white !border-solid !border-red-600 !border-2"
                >
                    立即咨询
                </Button>
            </View>
        </View>
    );
}

export default RvDetail;