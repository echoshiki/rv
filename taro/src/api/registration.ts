import { http } from "@/utils/request";
import { 
    ApiResponse, 
    RegistrationList, 
    RegistrationItem, 
    RegistrationSubmission,
    RegistrationStatus
} from "@/types/api";

const REGISTRATION_API = `/api/v1/registrations/`;

// 查询当前用户的报名列表
const getRegistrations = (): Promise<ApiResponse<RegistrationList>> => {
    return http.get(`${REGISTRATION_API}my`);
};

// 提交报名信息
const submitRegistration = (data: RegistrationSubmission): Promise<ApiResponse<RegistrationItem>> => {
    return http.post(`${REGISTRATION_API}`, data);
};

// 检查当前用户报名状态
const checkRegistrationStatus = (activityId: string): Promise<ApiResponse<RegistrationStatus | null>> => {
    return http.get(`${REGISTRATION_API}check/${activityId}`);
};

export {
    getRegistrations,
    submitRegistration,
    checkRegistrationStatus
};