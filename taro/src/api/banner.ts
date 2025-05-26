import { http } from "@/utils/request";
import { ApiResponse, BannerItem } from "@/types/api";

const BANNER_API = `/api/v1/banners/`;

const bannerApi = {
    /**
     * 获取指定频道的轮播图列表
     * @param channel - 频道标识
     * @returns 
     */
    get: (channel: string): Promise<ApiResponse<BannerItem[]>> => {
      return  http.get(`${BANNER_API}${channel}`);
    },
}

export default bannerApi;