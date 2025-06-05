import { useState, useEffect, useCallback } from "react";
import { RvItem, RvAllData } from "@/types/ui";
import { getRvList, getRvAllData } from "@/api/rv";

/**
 * 房车列表数据管理 Hook
 * @param used 是否为二手车
 */
const useRvList = (used?: boolean) => {
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
            const { data: responseData } = await getRvList(used ?? false);
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
            setError(e.message || 'Failed to fetch rvs.');
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

const useRvAllData = () => {
    const [rvAllData, setRvAllData] = useState<RvAllData[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchRvAllData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getRvAllData();
            // 剔除没有数据的分类
            const filteredData = responseData.filter(item => item.rvs.list.length > 0);
            setRvAllData(filteredData);
        } catch (e) {
            setError(e.message || 'Failed to fetch rvs.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchRvAllData();
    }, []);

    return {
        rvAllData,
        loading,
        error,
        refetch: fetchRvAllData
    }
}

export {
    useRvList,
    useRvAllData
}