import { http } from "@/utils/request";
import { ApiResponse, UserInfo } from "@/types/api";
import { UserInfoSubmission } from "@/types/query";

const USER_API = `/api/v1/user/`;

const userApi = {
    info: (): Promise<ApiResponse<UserInfo>> => {
        return http.get(`${USER_API}`);
    },

    update: (data: UserInfoSubmission): Promise<ApiResponse<UserInfo>> => {
        return http.put(`${USER_API}`, data);
    }
}

export {
    userApi
};