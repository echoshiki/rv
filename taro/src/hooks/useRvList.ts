import { useState, useEffect, useCallback } from "react";
import { RvItem } from "@/types/ui";
import { getRvList } from "@/api/rv";

/**
 * 房车列表数据管理 Hook
 */
const useRvList = () => {
    const [rvList, setRvList] = useState<RvItem[]>([]);
    const [total, setTotal] = useState<number>(0);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchRvs = useCallback(async (
        isLoadMore: boolean = false
    ) => {
        if (loading) return;

        // 开始执行请求
        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getRvList();

            if (isLoadMore) {
                // 格式化列表并累加数据
                setRvList(pre => [
                    ...pre,
                    ...responseData.list
                ]);
            } else {
                // 格式化列表
                const transformedList = responseData.list;
                setRvList(transformedList);
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
    }, [loading]);

    // 初次加载
    useEffect(() => {
        fetchRvs();
    }, []);

    // 刷新数据
    const refresh = useCallback(async () => {
        return fetchRvs();
    }, [fetchRvs]);

    // 载入更多
    const loadMore = useCallback(async () => {
        if (!loading && hasMore) {
            return fetchRvs(true);
        }
    }, [loading, hasMore, page, fetchRvs]);

    return {
        rvList,
        loading,
        error,
        hasMore,
        page,
        total,
        refresh,
        loadMore,
    }
}

export {
    useRvList
}