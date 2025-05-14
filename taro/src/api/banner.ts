import { http } from "@/utils/request";
import { ApiResponse, BannerItem } from "@/types/api";

const BANNER_API = `/api/v1/banners/`;

const getBannerList = (channel: string): Promise<ApiResponse<BannerItem[]>> => {
      return  http.get(`${BANNER_API}${channel}`);
};

export {
    getBannerList
};