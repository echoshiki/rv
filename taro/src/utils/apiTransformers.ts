import { MenuItem as ApiMenuItem } from "@/types/api"; 
import { MenuItem } from "@/types/ui";
import { checkLoginBeforeNavigate } from '@/utils/auth';
import Taro from '@tarojs/taro';
import useAuthStore from '@/stores/auth';

/**
 * 客户端菜单函数映射查找表
 * 将 API 提供的函数名称映射到客户端实际函数
 */
const clientSideFunctions: Record<string, () => void> = {
    // 拨打客服电话 TODO 统一后端获取
    "makePhoneCall": () => {
        Taro.makePhoneCall({
            phoneNumber: '15050773500'
        });
    },
    // 打开客服
    "openCustomerServiceChat": () => {
        Taro.openCustomerServiceChat({
            extInfo: {url: ''},
            corpId: '',
            success: function (res) {
                console.log(res);
            }
        });
    },
    // 登出
    "logout": () => {
        useAuthStore.getState().logout();
    }
};

/**
 * 将 API 提供的菜单项转换为客户端菜单项
 * @param apiItem API 提供的菜单项
 * @returns 转换后的菜单项
 */
function transformApiMenuItem(apiItem: ApiMenuItem): MenuItem {
    // 初始化时包含的属性
    const displayProps: Partial<MenuItem> = {
        title: apiItem.title,
        icon: apiItem.icon,
        description: apiItem.subtitle || undefined,
    };

    if (apiItem.link_type === "page") {
        // 如果是页面类型，先判断是否需要登录
        if (apiItem.requires_auth) {
            // 需要登陆则进行跳转前验证
            displayProps.onClick = ({ link }) => checkLoginBeforeNavigate(link);
        }
        displayProps.link = apiItem.link_value;
    } else if (apiItem.link_type === "function") {
        // 如果是函数类型，使用 # 作为占位符
        displayProps.link = "#";

        // link_value 此时应为函数名称，通过查找表找到对应的函数
        const action = clientSideFunctions[apiItem.link_value as keyof typeof clientSideFunctions];
        
        // 如果找到对应的函数，设置 onClick
        if (action) {
            displayProps.onClick = action;
        } else {
            console.warn(`没有能为这个菜单找到对应的映射函数: ${apiItem.link_value}`);
            displayProps.onClick = () => alert(`没有能为这个菜单找到对应的映射函数: ${apiItem.link_value}`);
        }
    } else if (apiItem.link_type === "channel" && apiItem.link_value) {
        // 给标签栏传参
        displayProps.link = apiItem.link_value;
    } else {
        displayProps.link = "#";
        console.warn(`不支持的链接类型: ${apiItem.link_type}`);
    }

    return displayProps as MenuItem;
}

export {
    transformApiMenuItem
}
