import { useState, useEffect, useCallback } from "react";
import { ArticleItem, Category } from "@/types/ui";
import { getArticleList, type ArticleListQueryParams } from "@/api/article";

interface UseArticleListOptions {
    autoLoad?: boolean,
    // 分类加载完成后的回调，比如修改标题
    onCategoryLoaded?: (category: Category) => void,
}

// 默认配置选项
const defaultOptions: UseArticleListOptions = {
    autoLoad: true,
};

/**
 * 文章列表数据管理 Hook
 * @param initialParams - 初始查询参数
 * @param options - 配置选项
 */
const useArticleList = (
    initialParams: ArticleListQueryParams,
    options: UseArticleListOptions = {}
) => {
    const {
        autoLoad,
        onCategoryLoaded
    } = { ...defaultOptions, ...options };

    const [params, setParams] = useState<ArticleListQueryParams>(initialParams);
    const [articleList, setArticleList] = useState<ArticleItem[]>([]);
    const [articleCategory, setArticleCategory] = useState<Category | null>(null);
    const [total, setTotal] = useState<number>(0);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    // 格式化文章列表
    const transformArticleList = (list: any[]) => {
        return list.map(item => ({
            ...item,
            date: item.published_at
        }));
    };

    const fetchArticles = useCallback(async (
        fetchParams: ArticleListQueryParams,
        isLoadMore: boolean = false
    ) => {
        if (loading) return;

        // 开始执行请求
        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getArticleList(fetchParams);

            if (isLoadMore) {
                // 格式化列表并累加数据
                setArticleList(pre => [
                    ...pre,
                    ...transformArticleList(responseData.list)
                ]);
            } else {
                // 格式化列表
                const transformedList = transformArticleList(responseData.list);
                setArticleList(transformedList);

                // 从列表子项里拿出分类并注入
                const filter = fetchParams.filter;
                if (filter && (filter.category_id !== undefined || filter.category_code !== undefined)) {
                    if (responseData.list && responseData.list.length > 0) {
                        // 获取第一个项的分类
                        const firstItem = responseData.list[0];
                        if (firstItem.category) {
                            setArticleCategory(firstItem.category);
                            // 执行分类加载完成后的回调
                            onCategoryLoaded?.(firstItem.category);
                        }
                    }
                }
            }
            
            // 更新信息
            setTotal(responseData.total);
            setPage(responseData.current_page);
            setHasMore(responseData.has_more_pages);
        } catch (e) {
            setError(e.message || 'Failed to fetch articles.');
        } finally {
            setLoading(false);
        }
    }, [loading, transformArticleList, onCategoryLoaded]);

    // 初次加载
    useEffect(() => {
        if (autoLoad) {
            fetchArticles(initialParams);
        }
    }, []);

    // 刷新数据
    const refresh = useCallback(async () => {
        const refreshParams = {
            ...params,
            page: 1
        };
        setParams(refreshParams);
        return fetchArticles(refreshParams);
    }, [params, fetchArticles]);

    // 载入更多
    const loadMore = useCallback(async () => {
        if (!loading && hasMore) {
            const loadMoreParams = {
                ...params,
                page: page + 1
            };
            setParams(loadMoreParams);
            return fetchArticles(loadMoreParams, true);
        }
    }, [loading, hasMore, params, page, fetchArticles]);

    // 更新查询参数
    const updateParams = useCallback((newParams: Partial<ArticleListQueryParams>) => {
        const updatedParams = { ...params, ...newParams, page: 1 };
        setParams(updatedParams);
        fetchArticles(updatedParams);
    }, [params, fetchArticles]);

    return {
        articleList,
        articleCategory,
        loading,
        error,
        hasMore,
        page,
        total,
        refresh,
        loadMore,
        updateParams
    }
}

export {
    useArticleList,
    UseArticleListOptions
}