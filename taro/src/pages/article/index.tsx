import Taro from "@tarojs/taro";
import ArticleList from '@/components/ArticleList';
import { useMemo } from "react";
import { ArticleListQueryParams } from "@/types/query";
import PageCard from "@/components/PageCard";

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

    return (
        <PageCard>
            <ArticleList 
                queryParams={queryParams} 
                isPullDownRefresh
                isReachBottomRefresh
                changePageTitle
            />
        </PageCard>
    );
}

export default Articles;