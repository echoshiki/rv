import { http } from "@/utils/request";
import { ApiResponse, RvDetail, RvList } from "@/types/api";

const RV_API = `/api/v1/rvs/`;

const getRvList = (): Promise<ApiResponse<RvList>> => {
      return  http.get(`${RV_API}`);
};

const getRvDetail = (): Promise<ApiResponse<RvDetail>> => {
    return http.get(`${RV_API}`);
};

export {
    getRvList,
    getRvDetail
};