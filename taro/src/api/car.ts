import { http } from "@/utils/request";
import { 
    ApiResponse, 
    MyCarList, 
    MyCarItem
} from "@/types/api";
import { MyCarSubmission } from "@/types/query";

const MY_CAR_API = `/api/v1/my-cars/`;

const myCarApi = {
    // 爱车列表
    list: (): Promise<ApiResponse<MyCarList>> => {
        return http.get(`${MY_CAR_API}`);
    },

    // 爱车详情
    get: (id: string): Promise<ApiResponse<MyCarItem>> => {
        return http.get(`${MY_CAR_API}${id}`);
    },

    // 创建爱车
    create: (data: MyCarSubmission): Promise<ApiResponse<MyCarItem>> => {
        return http.post(`${MY_CAR_API}`, data);
    },

    // 更新爱车
    update: (id: string, data: MyCarItem): Promise<ApiResponse<MyCarItem>> => {
        return http.put(`${MY_CAR_API}${id}`, data);
    },

    // 删除爱车
    delete: (id: string): Promise<ApiResponse<void>> => {
        return http.delete(`${MY_CAR_API}${id}`);
    }
}

export default myCarApi;