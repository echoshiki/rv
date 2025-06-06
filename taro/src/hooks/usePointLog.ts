// 优化后的 usePointLog.js
import { useState, useEffect, useCallback } from "react";
import { PointLogItem, BaseQueryParams } from "@/types/ui";
import { getPointLogList } from "@/api/point";

const usePointLog = (initialParams: BaseQueryParams) => {
    // 数据状态
    const [pointLogs, setPointLogs] = useState<PointLogItem[]>([]);
    const [total, setTotal] = useState<number>(0);

    const [page, setPage] = useState<number>(1); 
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchPointLogs = useCallback(async (
        requestPage: number,
        isRefresh: boolean = false
    ) => {
        // 【修正】在函数开头就判断 loading，防止并发请求
        if (loading) return;

        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getPointLogList({
                ...initialParams, // 使用初始参数
                page: requestPage, // 使用传入的页码
            });

            const { list, total, current_page, has_more_pages } = responseData;

            // 如果是刷新，则替换列表；否则，追加
            setPointLogs(prevLogs => (isRefresh ? list : [...prevLogs, ...list]));
            
            setTotal(total);
            setPage(current_page); // 【关键】始终以服务器返回的页码为准
            setHasMore(has_more_pages);

        } catch (e) {
            setError(e.message || 'Failed to fetch point logs.');
        } finally {
            setLoading(false); // 确保 loading 状态被重置
        }
    }, [loading, initialParams]); // 【修正】依赖项应该是稳定的 initialParams 和 loading

    // 初次加载
    useEffect(() => {
        fetchPointLogs(1, true); // 首次加载视为一次刷新
    }, []);

    // 刷新数据
    const refresh = useCallback(async () => {
        await fetchPointLogs(1, true);
    }, [fetchPointLogs]);

    // 载入更多
    const loadMore = useCallback(async () => {
        // 使用函数内部最新的 state 进行判断，更安全
        if (!loading && hasMore) {
            // 请求当前页码的下一页
            await fetchPointLogs(page + 1, false);
        }
    }, [loading, hasMore, page, fetchPointLogs]);

    return {
        pointLogs,
        loading,
        error,
        hasMore,
        total,
        refresh,
        loadMore
    }
}

export {
    usePointLog
}