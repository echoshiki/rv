import { http } from "@/utils/request";
import { MenuItem } from "@/types/api";

const MENU_GROUP_API = `/api/v1/menus/group/`;

/**
 * 通过菜单标识请求菜单组数据
 * @param code 菜单标识
 * @returns 
 */
const getMenuGroup = (code: string): Promise<MenuItem[]> => {
      return http.get(`${MENU_GROUP_API}${code}`);
};

export {
    getMenuGroup
};