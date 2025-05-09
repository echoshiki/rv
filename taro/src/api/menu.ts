import { http } from "@/utils/request";

const MENU_API = `/api/v1/menus/group/`;

/**
 * 通过菜单标识请求菜单组数据
 * @param code 菜单标识
 * @returns 
 */
const getMenuGroup = (code: string) => {
    return http.get(`${MENU_API}${code}`);
};

export {
    getMenuGroup
};