import { http } from "@/utils/request";
import { ApiResponse, RegistrationList, RegistrationItem, RegistrationSubmission } from "@/types/api";

const REGIONS_API = `/api/v1/registrations/`;

// 查询当前用户的报名列表
const getRegistrations = (): Promise<ApiResponse<RegistrationList>> => {
    return http.get(`${REGIONS_API}my`);
};

// 提交报名信息
const submitRegistration = (data: RegistrationSubmission): Promise<ApiResponse<RegistrationItem>> => {
    return http.post(`${REGIONS_API}`, data);
};

export {
    getRegistrations,
    submitRegistration
};