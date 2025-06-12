import { useState, useEffect, useCallback } from "react";
import { Category } from "@/types/ui";

type PaginatedResponse<TRawItem> = {
    list: TRawItem[];
    total: number;
    current_page: number;
    has_more_pages: boolean;
};

type ApiFunction<TParams, TRawItem> = (
    params: TParams
) => Promise<{ data: PaginatedResponse<TRawItem> }>;

interface UseResourceListOptions<TItem, TRawItem, TParams> {
    autoLoad?: boolean;
    // 分类加载完成后的回调
    onCategoryLoaded?: (category: Category) => void;
    // 用于转换 API 返回的原始列表
    transformFn?: (list: TRawItem[]) => TItem[];
    // 用于从列表中提取分类信息
    categoryExtractor?: (list: TRawItem[], params: TParams) => Category | null;
}

const defaultOptions = {
    autoLoad: true,
    // 对列表数据进行格式化转换，默认不转换
    transformFn: (list: any[]) => list,
    // 从列表中提取分类信息，默认不提取
    categoryExtractor: () => null,
};

/**
 * 通用的、可配置的列表数据管理 Hook
 * @param apiFn - 用于获取数据的 API 函数
 * @param initialParams - 初始查询参数
 * @param options - 配置选项
 * @returns 包含列表数据、加载状态、错误信息等属性的对象
 */
const useResourceList = <TItem, TRawItem, TParams extends { page?: number }>(
    apiFn: ApiFunction<TParams, TRawItem> | null | undefined,
    initialParams: TParams,
    options: UseResourceListOptions<TItem, TRawItem, TParams> = {}
) => {
    const {
        autoLoad,
        onCategoryLoaded,
        transformFn,
        categoryExtractor
    } = { ...defaultOptions, ...options };

    const [params, setParams] = useState<TParams>(initialParams);
    const [list, setList] = useState<TItem[]>([]);
    const [category, setCategory] = useState<Category | null>(null);
    const [total, setTotal] = useState<number>(0);
    const [page, setPage] = useState<number>(1);
    const [hasMore, setHasMore] = useState<boolean>(true);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    // 实现核心的数据获取函数
    const fetchData = useCallback(async (
        fetchParams: TParams,
        isLoadMore: boolean = false
    ) => {
        if (loading || !apiFn) return;

        setLoading(true);
        setError(null);

        try {
            // 调用传入的 API 函数
            const { data: responseData } = await apiFn(fetchParams);
            const { list: rawList, total, current_page, has_more_pages } = responseData;

            // 使用传入的 transformFn 转换列表
            const transformedList = transformFn(rawList);

            if (isLoadMore) {
                setList(prev => [...prev, ...transformedList]);
            } else {
                setList(transformedList);

                // 使用传入的 categoryExtractor 提取分类
                const extractedCategory = categoryExtractor(rawList, fetchParams);
                if (extractedCategory) {
                    setCategory(extractedCategory);
                    onCategoryLoaded?.(extractedCategory);
                }
            }

            setTotal(total);
            setPage(current_page);
            setHasMore(has_more_pages);

        } catch (e) {
            setError(e.message || 'Failed to fetch data.');
        } finally {
            setLoading(false);
        }
    }, [loading, apiFn, transformFn, categoryExtractor, onCategoryLoaded]);

    // 首次载入
    useEffect(() => {
        if (autoLoad) {
            fetchData(params);
        }
    }, [apiFn]); // 依赖 apiFn 以支持条件性获取

    // 刷新载入
    const refresh = useCallback(async () => {
        const refreshParams = { ...params, page: 1 };
        setParams(refreshParams);
        return fetchData(refreshParams);
    }, [params, fetchData]);

    // 载入更多
    const loadMore = useCallback(async () => {
        if (!loading && hasMore) {
            const loadMoreParams = { ...params, page: page + 1 };
            return fetchData(loadMoreParams, true);
        }
    }, [loading, hasMore, page, params, fetchData]);

    const updateParams = useCallback((newParams: Partial<TParams>) => {
        const updatedParams = { ...params, ...newParams, page: 1 };
        setParams(updatedParams);
        fetchData(updatedParams);
    }, [params, fetchData]);

    return {
        list,
        category,
        loading,
        error,
        hasMore,
        page,
        total,
        params,
        refresh,
        loadMore,
        updateParams,
    };
}

export { useResourceList };