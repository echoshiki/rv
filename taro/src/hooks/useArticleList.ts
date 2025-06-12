import { ArticleItem, Category } from "@/types/ui";
import { getArticleList, type ArticleListQueryParams } from "@/api/article";
import { useResourceList } from "@/hooks/base/useResourceList";

const transformArticleList = (list: any[]): ArticleItem[] => {
    return list.map(item => ({ ...item, date: item.published_at }));
};

const articleCategoryExtractor = (list: any[], params: ArticleListQueryParams): Category | null => {
    const filter = params.filter;
    if (filter && (filter.category_id !== undefined || filter.category_code !== undefined)) {
        if (list && list.length > 0 && list[0].category) {
            return list[0].category;
        }
    }
    return null;
};

/**
 * 文章列表数据管理 Hook
 * @param initialParams - 初始查询参数
 * @param options - 配置选项
 */
const useArticleList = (
    initialParams: ArticleListQueryParams,
    options: any = {}
) => {
    const resourceResult = useResourceList<ArticleItem, any, ArticleListQueryParams>(
        getArticleList,
        initialParams,
        {
            ...options,
            transformFn: transformArticleList,
            categoryExtractor: articleCategoryExtractor,
        }
    );

    return {
        ...resourceResult,
        articleList: resourceResult.list,
        articleCategory: resourceResult.category,
    };
}

export {
    useArticleList
}