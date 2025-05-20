import { http } from "@/utils/request";
import { ApiResponse, RegionItem } from "@/types/api";

const REGIONS_API = `/api/v1/regions/`;

const getProvinces = (): Promise<ApiResponse<RegionItem[]>> => {
    return http.get(`${REGIONS_API}provinces`);
};

const getCities = (provinceCode: string): Promise<ApiResponse<RegionItem[]>> => {
    return http.get(`${REGIONS_API}cities/${provinceCode}`);
};

const getNameByCode = (code: string): Promise<ApiResponse<string>> => {
    return http.get(`${REGIONS_API}name/${code}`);
};

export {
    getProvinces,
    getCities,
    getNameByCode
};