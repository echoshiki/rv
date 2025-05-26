import { useState, useEffect, useCallback } from "react";
import registrationApi from "@/api/registration";
import { RegistrationItem } from "@/types/ui";

/**
 * 报名列表数据管理 Hook
 */
const useRegistrationList = () => {
    const [registrationList, setRegistrationList] = useState<RegistrationItem[]>([]);
    const [total, setTotal] = useState<number>(0);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchRegistrations = useCallback(async (
        isLoadMore: boolean = false
    ) => {
        if (loading) return;

        // 开始执行请求
        setLoading(true);
        setError(null);

        try {
            // 获取报名列表
            const { data: responseData } = await registrationApi.list();
            if (isLoadMore) {
                // 格式化列表并累加数据
                setRegistrationList(pre => [
                    ...pre,
                    ...responseData.list
                ]);
            } else {
                setRegistrationList(responseData.list);
            }

            // 更新信息
            setTotal(responseData.total);
            setPage(responseData.current_page);
            setHasMore(responseData.has_more_pages);

        } catch (e) {
            setError(e.message || 'Failed to fetch registrations.');
        } finally {
            setLoading(false);
        }
    }, [loading]);

    // 初次加载
    useEffect(() => {
        fetchRegistrations();
    }, []);

    // 刷新数据
    const refresh = useCallback(async () => {
        return fetchRegistrations();
    }, [fetchRegistrations]);

    // 载入更多
    const loadMore = useCallback(async () => {
        if (!loading && hasMore) {
            return fetchRegistrations(true);
        }
    }, [loading, hasMore, fetchRegistrations]);

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