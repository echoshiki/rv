import { http } from "@/utils/request";
import { 
    ApiResponse, 
    OrderList, 
    OrderItem, 
} from "@/types/api";
import { BaseQueryParams } from "@/types/ui";

const ORDER_API = `/api/v1/rv-orders/`;

const orderApi = {
    /**
     * 获取当前用户的订单列表
     * @param params - 查询参数
     * @returns 
     */
    list: (params: BaseQueryParams): Promise<ApiResponse<OrderList>> => {
        // 清理未定义的参数
        Object.keys(params).forEach(key => {
            if (params[key] === undefined || params[key] === null) {
                delete params[key];
            }
        });
        return http.get(`${ORDER_API}`, params);
    },

    /**
     * 提交房车订单
     * @param rv_id - 房车ID
     * @returns 
     */
    store: (rv_id: string): Promise<ApiResponse<OrderItem>> => {
        return http.post(`${ORDER_API}`, {
            rv_id
        });
    },

    /**
     * 获取订单详情
     * @param id - 订单ID
     * @returns 
     */
    get: (id: string): Promise<ApiResponse<OrderItem>> => {
        return http.get(`${ORDER_API}${id}`);
    }
}

export default orderApi;