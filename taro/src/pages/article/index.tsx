import { View } from "@tarojs/components";
import Taro from "@tarojs/taro";
import ArticleList from '@/components/ArticleList';
import { useMemo } from "react";
import { type ArticleListQueryParams } from "@/api/article";

const Articles = () => {
    const { router } = Taro.getCurrentInstance();

    // 构造查询参数对象
    const queryParams = useMemo(() => {
        // 初始化查询参数
        const params: ArticleListQueryParams = {
            filter: {},
            page: 1,
            limit: 8
        };

        if (router?.params?.code) {
            params.filter.category_code = router.params.code
        }

        if (router?.params?.id) {
            params.filter.category_id = router.params.id
        }

        return params;
    }, [router?.params?.code, router?.params?.id]);

    // 检查是否有必要的查询参数
    const hasValidParams = useMemo(() => {
        return !!(queryParams.filter?.category_id || queryParams.filter?.category_code);
    }, [queryParams.filter?.category_id, queryParams.filter?.category_code]);

    Taro.useDidShow(() => {
        if (!hasValidParams) {
            Taro.navigateTo({
                url: '/pages/404/index'
            });
        }
    });

    return (
        <View className="bg-gray-100 min-h-screen py-5">
            <ArticleList 
                queryParams={queryParams} 
                isPullDownRefresh
                isReachBottomRefresh
                changePageTitle
            />
        </View>
    );
}

export default Articles;