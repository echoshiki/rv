import Taro from '@tarojs/taro';
import useAuthStore from '../stores/auth';

import { getCurrentPageUrl } from '@/utils/common';

/**
 * 判断用户是否已登录
 * @returns 是否已登录
 */
function isLoggedIn(): boolean {
    return useAuthStore.getState().isLoggedIn();
}

/**
 * 验证用户是否已登录，未登录则跳转到登录页（页面内验证）
 * Route: PageA - PageB = PageLogin = PageB
 * @param targetUrl 目标页面URL，登录成功后跳转
 * @returns 是否已登录
 */
function checkLogin(targetUrl?: string): boolean {
    if (!isLoggedIn()) {
        // 跳转到登录页，并传递目标页面参数
        const redirectUrl = targetUrl || getCurrentPageUrl();

        // 这里实则在目标页面内，所以使用 redirectTo 方法替换成登陆页
        // 解决了路径栈重复问题
        Taro.redirectTo({ 
            url: `/pages/login/index?redirect=${encodeURIComponent(redirectUrl)}` 
        });
        return false;
    }
    return true;
}

/**
 * 验证用户是否已登录，未登录则跳转到登录页（跳转前验证）
 * Route: PageA - PageLogin = PageB
 * @param targetUrl 目标页面URL，登录成功后跳转
 * @returns 是否已登录
 */
function checkLoginBeforeNavigate(targetUrl?: string): void {
    if (isLoggedIn()) {
        // 已登陆，导航到目标页面
        Taro.navigateTo({ url: targetUrl || getCurrentPageUrl() });
    } else {
        // 未登陆，导航到登录页，并传递目标页面参数
        const navigateUrl = targetUrl || getCurrentPageUrl();
        Taro.navigateTo({ 
            url: `/pages/login/index?redirect=${encodeURIComponent(navigateUrl)}` 
        });
        return;
    }
}



export {
    isLoggedIn,
    checkLogin,
    checkLoginBeforeNavigate
}
