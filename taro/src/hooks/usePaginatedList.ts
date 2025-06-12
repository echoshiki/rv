
import { useState, useEffect, useCallback } from "react";
import { BaseQueryParams } from "@/types/ui";

// 定义 API 函数必须遵守的格式和返回的数据结构
// T 是列表项的类型
type PaginatedResponse<T> = {
    list: T[];
    total: number;
    current_page: number;
    has_more_pages: boolean;
};

type ApiFunction<T> = (
    params: BaseQueryParams & { page: number }
) => Promise<{ data: PaginatedResponse<T> }>;

/**
 * 通用的分页列表数据管理 Hook
 * @param apiFn - 用于获取数据的 API 函数
 * @param initialParams - 初始查询参数
 */
const usePaginatedList = <T>(
    apiFn: ApiFunction<T>,
    initialParams?: BaseQueryParams
) => {
    // 定义状态
    const [list, setList] = useState<T[]>([]);
    const [total, setTotal] = useState<number>(0);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    // 获取数据的核心逻辑
    const fetchData = useCallback(async (
        requestPage: number,
        isRefresh: boolean = false
    ) => {
        if (loading) return;

        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await apiFn({
                ...initialParams,
                page: requestPage,
            });

            const { list: newList, total, current_page, has_more_pages } = responseData;

            // 如果是刷新，则替换列表；否则，追加
            setList(prevList => (isRefresh ? newList : [...prevList, ...newList]));
            setTotal(total);
            setPage(current_page);
            setHasMore(has_more_pages);
        } catch (e) {
            setError(e.message || 'Failed to fetch data.');
        } finally {
            setLoading(false);
        }
    }, [loading, initialParams, apiFn]);

    // 初始化数据
    useEffect(() => {
        fetchData(1, true);
    }, [apiFn]); 

    // 刷新数据
    const refresh = useCallback(async () => {
        await fetchData(1, true);
    }, [fetchData]);

    // 载入更多
    const loadMore = useCallback(async () => {
        if (!loading && hasMore) {
            await fetchData(page + 1, false);
        }
    }, [loading, hasMore, page, fetchData]);

    return {
        list,
        loading,
        error,
        hasMore,
        page,
        total,
        refresh,
        loadMore
    };
};

export { usePaginatedList };
