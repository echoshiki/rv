import { http } from "@/utils/request";
import { 
    ApiResponse, 
    BaseQueryParams,
    RvDetail, 
    RvList, 
    RvAllData,
    RvOrderItem,
    RvOrderList
} from "@/types/api";

const RV_API = `/api/v1/rvs/`;
const USED_RV_API = `/api/v1/used-rvs/`;
const RV_ORDER_API = `/api/v1/rv-orders/`;

/**
 * 获取房车列表
 * @param used 是否二手车
 * @returns 
 */
const getRvList = (initialParams?: BaseQueryParams): Promise<ApiResponse<RvList>> => {
    return http.get(`${RV_API}`, { params: initialParams });
};

/**
 * 获取房车详情
 * @param used 是否二手车
 * @returns 
 */
const getRvDetail = (id: string, used: boolean): Promise<ApiResponse<RvDetail>> => {
    return used ? http.get(`${USED_RV_API}${id}`) : http.get(`${RV_API}${id}`);
};

/**
 * 获取房车列表（底盘+列表）
 * @returns 
 */
const getRvAllData = (): Promise<ApiResponse<RvAllData[]>> => {
    return http.get(`${RV_API}all`);
};

/**
 * 创建房车订单
 */
const createRvOrder = (rvId: string): Promise<ApiResponse<RvOrderItem>> => {
    return http.post(`${RV_ORDER_API}`, { rv_id: rvId });
};

/**
 * 获取房车订单列表
 */
const getRvOrderList = (): Promise<ApiResponse<RvOrderList>> => {
    return http.get(`${RV_ORDER_API}`);
};

/**
 * 获取房车订单详情
 */
const getRvOrderDetail = (id: string): Promise<ApiResponse<RvOrderItem>> => {
    return http.get(`${RV_ORDER_API}${id}`);
};

/**
 * 获取二手车列表
 */
const getUsedRvList = (initialParams?: BaseQueryParams): Promise<ApiResponse<RvList>> => {
    return http.get(`${USED_RV_API}`, { params: initialParams });
};

export {
    getRvList,
    getRvDetail,
    getRvAllData,
    createRvOrder,
    getRvOrderList,
    getRvOrderDetail,
    getUsedRvList
};