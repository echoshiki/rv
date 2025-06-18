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

    switch (apiItem.link_type) {
        // 页面跳转
        case "page":
            displayProps.link = apiItem.link_value;
            if (apiItem.requires_auth) {
                // 需要登陆则进行跳转前验证
                displayProps.onClick = () => checkLoginBeforeNavigate(displayProps.link as string);
            }
            break;

        // 函数调用
        case "function":
            displayProps.link = "#"; // 函数类型使用 # 作为占位符
            const action = clientSideFunctions[apiItem.link_value as keyof typeof clientSideFunctions];
            if (action) {
                displayProps.onClick = action;
            } else {
                console.warn(`没有能为这个菜单找到对应的映射函数: ${apiItem.link_value}`);
            }
            break;

        // 电话拨打
        case "phone":
            displayProps.link = "#";
            displayProps.onClick = () => {
                const phoneNumber = apiItem.link_value;
                if (phoneNumber) {
                    Taro.makePhoneCall({ phoneNumber });
                } else {
                    console.warn(`电话菜单项 [${apiItem.title}] 未提供号码。`);
                }
            };
            break;
        
        // 客服按钮
        case "contact":
            displayProps.link = "#";
            displayProps.openType = 'contact';
            break;

        // 标签栏传参
        case "channel":
            displayProps.link = apiItem.link_value;
            break;

        // 未知类型
        default:
            displayProps.link = "#";
            console.warn(`不支持的链接类型: ${apiItem.link_type}`);
    }

    return displayProps as MenuItem;
}

export {
    transformApiMenuItem
}
