import { http } from "@/utils/request";
import { 
    ApiResponse, 
    RegistrationList, 
    RegistrationItem, 
    RegistrationSubmission,
    RegistrationStatus
} from "@/types/api";
import { BaseQueryParams } from "@/types/ui";

const REGISTRATION_API = `/api/v1/registrations/`;

const registrationApi = {
    /**
     * 获取当前用户的报名列表
     * @returns 
     */
    list: ({
        orderBy,
        sort,
        page,
        limit
    }: BaseQueryParams): Promise<ApiResponse<RegistrationList>> => {
        const queryParams = {
            orderBy,
            sort,
            page,
            limit,
        };

        // 清理未定义的参数
        Object.keys(queryParams).forEach(key => {
            if (queryParams[key] === undefined || queryParams[key] === null) {
                delete queryParams[key];
            }
        });

        return http.get(`${REGISTRATION_API}my`, queryParams);
    },

    /**
     * 提交报名信息
     * @param data - 报名信息
     * @returns 
     */
    store: (data: RegistrationSubmission): Promise<ApiResponse<RegistrationItem>> => {
        return http.post(`${REGISTRATION_API}`, data);
    },

    /**
     * 获取报名详情
     * @param id - 报名ID
     * @returns 
     */
    get: (id: string): Promise<ApiResponse<RegistrationItem>> => {
        return http.get(`${REGISTRATION_API}${id}`);
    },

    /**
     * 获取当前用户的活动报名状态
     * @param activityId - 活动ID
     * @returns 
     */
    status: (activityId: string): Promise<ApiResponse<RegistrationStatus | null>> => {
        return http.get(`${REGISTRATION_API}${activityId}/status`);
    },

    // 取消报名
    cancel: (id: string): Promise<ApiResponse<void>> => {
        return http.post(`${REGISTRATION_API}${id}/cancel`);
    }
}

export default registrationApi;