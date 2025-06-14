import registrationApi from "@/api/registration";
import { RegistrationItem } from "@/types/ui";
import { BaseQueryParams } from "@/types/query";
import { usePaginatedList } from "@/hooks/base/usePaginatedList";

/**
 * 报名列表数据管理 Hook
 */
const useRegistrationList = (initialParams?: BaseQueryParams) => {
    const paginatedResult = usePaginatedList<RegistrationItem>(registrationApi.list, initialParams);
    return {
        ...paginatedResult,
        registrationList: paginatedResult.list
    };
}

export {
    useRegistrationList
}