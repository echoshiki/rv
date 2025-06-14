import { http } from "@/utils/request";
import { 
    ApiResponse, 
    MaintenanceList, 
    MaintenanceItem
} from "@/types/api";
import { MaintenanceSubmission } from "@/types/query";

const MAINTENANCE_API = `/api/v1/maintenances/`;

const maintenanceApi = {
    // 我的预约列表
    list: (): Promise<ApiResponse<MaintenanceList>> => {
        return http.get(`${MAINTENANCE_API}`);
    },

    // 预约详情
    get: (id: string): Promise<ApiResponse<MaintenanceItem>> => {
        return http.get(`${MAINTENANCE_API}${id}`);
    },

    // 创建预约
    create: (data: MaintenanceSubmission): Promise<ApiResponse<MaintenanceItem>> => {
        return http.post(`${MAINTENANCE_API}`, data);
    },
}

export default maintenanceApi;