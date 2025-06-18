import { http } from "@/utils/request";
import { ApiResponse, UserInfo } from "@/types/api";
import { UserInfoSubmission } from "@/types/query";

const USER_API = `/api/v1/user/`;

const userApi = {
    // 获取用户信息
    info: (): Promise<ApiResponse<UserInfo>> => {
        return http.get(`${USER_API}`);
    },

    // 更新用户信息
    update: (data: UserInfoSubmission): Promise<ApiResponse<UserInfo>> => {
        return http.put(`${USER_API}`, data);
    },

    // 更新用户最后活跃时间
    updateLastActiveAt: (): Promise<ApiResponse<void>> => {
        return http.post(`${USER_API}active`);
    }
}

export {
    userApi
};