import rvOrderApi from "@/api/order";
import { BaseQueryParams } from "@/types/query";
import { usePaginatedList } from "@/hooks/base/usePaginatedList";
import { RvOrderItem } from "@/types/api";

/**
 * 房车订单列表数据管理 Hook
 */
const useRvOrderList = (initialParams?: BaseQueryParams) => {
    // 调用通用分页列表管理 Hook
    const paginatedResult = usePaginatedList<RvOrderItem>(rvOrderApi.list, initialParams);
    return { 
        ...paginatedResult, 
        rvOrderList: paginatedResult.list
    };
}

export {
    useRvOrderList
}