import { useState, useEffect, useCallback } from "react";
import { ActivityItem, Category } from "@/types/ui";
import { getActivityList, type ActivityListQueryParams } from "@/api/activity";

interface UseActivityListOptions {
    autoLoad?: boolean,
    // 分类加载完成后的回调，比如修改标题
    onCategoryLoaded?: (category: Category) => void,
}

// 默认配置选项
const defaultOptions: UseActivityListOptions = {
    autoLoad: true,
};

/**
 * 活动列表数据管理 Hook
 * @param initialParams - 初始查询参数
 * @param options - 配置选项
 */
const useActivityList = (
    initialParams: ActivityListQueryParams,
    options: UseActivityListOptions = {}
) => {
    const {
        autoLoad,
        onCategoryLoaded
    } = { ...defaultOptions, ...options };

    const [params, setParams] = useState<ActivityListQueryParams>(initialParams);
    const [activityList, setActivityList] = useState<ActivityItem[]>([]);
    const [activityCategory, setActivityCategory] = useState<Category | null>(null);
    const [total, setTotal] = useState<number>(0);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    // 格式化活动列表
    const transformActivityList = (list: any[]) => {
        return list.map(item => ({
            ...item,
            date: item.published_at
        }));
    };

    const fetchActivities = useCallback(async (
        fetchParams: ActivityListQueryParams,
        isLoadMore: boolean = false
    ) => {
        if (loading) return;

        // 开始执行请求
        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getActivityList(fetchParams);

            if (isLoadMore) {
                // 格式化列表并累加数据
                setActivityList(pre => [
                    ...pre,
                    ...transformActivityList(responseData.list)
                ]);
            } else {
                // 格式化列表
                const transformedList = transformActivityList(responseData.list);
                setActivityList(transformedList);

                // 从列表子项里拿出分类并注入
                const filter = fetchParams.filter;
                if (filter && (filter.category_id !== undefined)) {
                    if (responseData.list && responseData.list.length > 0) {
                        // 获取第一个项的分类
                        const firstItem = responseData.list[0];
                        if (firstItem.category) {
                            setActivityCategory(firstItem.category);
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
            setError(e.message || 'Failed to fetch activities.');
        } finally {
            setLoading(false);
        }
    }, [loading, transformActivityList, onCategoryLoaded]);

    // 初次加载
    useEffect(() => {
        if (autoLoad) {
            fetchActivities(initialParams);
        }
    }, []);

    // 刷新数据
    const refresh = useCallback(async () => {
        const refreshParams = {
            ...params,
            page: 1
        };
        setParams(refreshParams);
        return fetchActivities(refreshParams);
    }, [params, fetchActivities]);

    // 载入更多
    const loadMore = useCallback(async () => {
        if (!loading && hasMore) {
            const loadMoreParams = {
                ...params,
                page: page + 1
            };
            setParams(loadMoreParams);
            return fetchActivities(loadMoreParams, true);
        }
    }, [loading, hasMore, params, page, fetchActivities]);

    // 更新查询参数
    const updateParams = useCallback((newParams: Partial<ActivityListQueryParams>) => {
        const updatedParams = { ...params, ...newParams, page: 1 };
        setParams(updatedParams);
        fetchActivities(updatedParams);
    }, [params, fetchActivities]);

    return {
        activityList,
        activityCategory,
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
    useActivityList
}