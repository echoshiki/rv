import { http } from "@/utils/request";
import { BannerItem } from "@/types/api";
const BANNER_API = `/api/v1/banners/`;

const getBanner = (channel: string): Promise<BannerItem[]> => {
      return  http.get(`${BANNER_API}${channel}`);
};

export {
    getBanner
};