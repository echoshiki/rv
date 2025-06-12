import registrationApi from "@/api/registration";
import { RegistrationItem, BaseQueryParams } from "@/types/ui";
import { usePaginatedList } from "@/hooks/usePaginatedList";

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