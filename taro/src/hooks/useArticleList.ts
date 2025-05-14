import { useState, useEffect } from "react";
import { ArticleItem, ArticleCategory } from "@/types/ui";
import { getArticleList, type ArticleListQueryParams } from "@/api/article";

const useArticleList = (params: ArticleListQueryParams) => {
    const [articleList, setArticleList] =  useState<ArticleItem[]>([]);
    const [articleCategory, setArticleCategory] = useState<ArticleCategory | null>(null);
    const [total, setTotal] = useState<number>(0);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchArticles = async (
        fetchParams: ArticleListQueryParams,
        isLoadMore: boolean = false
    ) => {
        setLoading(true);
        setError(null);
        try {
            console.log(fetchParams);
            const response = await getArticleList(fetchParams);
            const responseData = response.data;

            if (isLoadMore) {
                // 累加数据
                setArticleList(pre => [
                    ...pre,
                    ...responseData.list.map(item => ({
                        ...item,
                        date: item.published_at
                    }))
                ]);
            } else {
                // 设置数据
                setArticleList(responseData.list.map(item => ({
                    ...item,
                    date: item.published_at
                })));
            }

            setTotal(responseData.total);
            setPage(responseData.current_page);
            setHasMore(responseData.has_more_pages);

            const filter = fetchParams.filter;

            // 获取列表里的分类用于展示
            if (filter && (filter.category_id !== undefined || filter.category_code !== undefined)) {
                if (responseData.list && responseData.list.length > 0) {
                    // 获取第一个项的分类
                    const firstItem = responseData.list[0];
                    if (firstItem.category) {
                        setArticleCategory(firstItem.category);
                    }
                }
            }
        } catch (e) {
            setError(e.message || 'Failed to fetch articles.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchArticles(params);
    }, []);

    // 刷新数据
    const refresh = () => {
        return fetchArticles({
            ...params,
            page: 1
        });
    };

    // 载入更多
    const loadMore = () => {
        if (!loading && hasMore) {
            return fetchArticles({
                ...params,
                page: Number(page) + 1
            }, true);
        }
    }

    return {
        articleList,
        articleCategory,
        loading,
        error,
        hasMore,
        page,
        total,
        refresh,
        loadMore
    }
}

export {
    useArticleList
}