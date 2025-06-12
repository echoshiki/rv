import { PointLogItem, BaseQueryParams } from "@/types/ui";
import { getPointLogList } from "@/api/point";
import { usePaginatedList } from "@/hooks/base/usePaginatedList";

/**
 * 积分日志列表数据管理 Hook
 * @param initialParams - 初始查询参数，页码、每页数量、排序等
 */
const usePointLog = (initialParams?: BaseQueryParams) => {
    const paginatedResult = usePaginatedList<PointLogItem>(getPointLogList, initialParams);
    return {
        ...paginatedResult,
        pointLogList: paginatedResult.list
    };
}

export {
    usePointLog
}