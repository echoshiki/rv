import { RvItem } from "@/types/ui";
import { BaseQueryParams } from "@/types/query";
import { getUsedRvList, getRvList } from "@/api/rv";
import { usePaginatedList } from "@/hooks/base/usePaginatedList";

/**
 * 二手车列表数据管理 Hook
 * @param initialParams - 初始查询参数，页码、每页数量、排序等
 */
const useRvList = (initialParams?: BaseQueryParams, used: boolean = true) => {
    const api = used ? getUsedRvList : getRvList;
    const paginatedResult = usePaginatedList<RvItem>(api, initialParams);
    return {
        ...paginatedResult,
        rvList: paginatedResult.list
    };
}

export {
    useRvList
}