import { useResourceList } from "@/hooks/base/useResourceList";
import { BaseQueryParams } from "@/types/query";

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
 * 简化且通用的分页列表数据管理 Hook
 * @param apiFn - 用于获取数据的 API 函数
 * @param initialParams - 初始查询参数
 */
const usePaginatedList = <T>(
    apiFn: ApiFunction<T>,
    initialParams?: BaseQueryParams
) => {
    const {
        list,
        loading,
        error,
        hasMore,
        page,
        total,
        refresh,
        loadMore
    } = useResourceList<T, T, BaseQueryParams>(apiFn, initialParams || {});

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
