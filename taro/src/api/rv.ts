import { http } from "@/utils/request";
import { ApiResponse, RvDetail, RvList } from "@/types/api";

const RV_API = `/api/v1/rvs/`;
const USED_RV_API = `/api/v1/used-rvs/`;

/**
 * 获取房车列表
 * @param used 是否二手车
 * @returns 
 */
const getRvList = (used: boolean): Promise<ApiResponse<RvList>> => {
    return used ? http.get(`${USED_RV_API}`) : http.get(`${RV_API}`);
};

/**
 * 获取房车详情
 * @param used 是否二手车
 * @returns 
 */
const getRvDetail = (id: string, used: boolean): Promise<ApiResponse<RvDetail>> => {
    return used ? http.get(`${USED_RV_API}${id}`) : http.get(`${RV_API}${id}`);
};

export {
    getRvList,
    getRvDetail
};