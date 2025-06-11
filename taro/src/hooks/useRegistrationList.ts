import { useState, useEffect, useCallback } from "react";
import registrationApi from "@/api/registration";
import { RegistrationItem, BaseQueryParams } from "@/types/ui";

/**
 * 报名列表数据管理 Hook
 */
const useRegistrationList = (initialParams?: BaseQueryParams) => {
    const [registrationList, setRegistrationList] = useState<RegistrationItem[]>([]);
    const [total, setTotal] = useState<number>(0);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchRegistrations = useCallback(async (
        requestPage: number,
        isRefresh: boolean = false
    ) => {
        if (loading) return;

        // 开始执行请求
        setLoading(true);
        setError(null);

        try {
            // 获取报名列表
            const { data: responseData } = await registrationApi.list({
                ...initialParams,
                page: requestPage,
            });

            const { list, total, current_page, has_more_pages } = responseData;

            // 如果是刷新，则替换列表；否则，追加
            setRegistrationList(prevList => (isRefresh ? list : [...prevList, ...list]));
            
            setTotal(total);
            setPage(current_page); // 【关键】始终以服务器返回的页码为准
            setHasMore(has_more_pages);
        } catch (e) {
            setError(e.message || 'Failed to fetch registrations.');
        } finally {
            setLoading(false);
        }
    }, [loading, initialParams]);

    // 初次加载
    useEffect(() => {
        fetchRegistrations(1, true);
    }, []);

    // 刷新数据
    const refresh = useCallback(async () => {
        await fetchRegistrations(1, true);
    }, [fetchRegistrations]);

    // 载入更多
    const loadMore = useCallback(async () => {
        if (!loading && hasMore) {
            await fetchRegistrations(page + 1, false);
        }
    }, [loading, hasMore, page, fetchRegistrations]);

    return {
        registrationList,
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
    useRegistrationList
}