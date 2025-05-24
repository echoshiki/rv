import Taro from "@tarojs/taro";
import { create } from "zustand";
import { persist } from 'zustand/middleware';

import { http } from "@/utils/request";
import { mapsTo } from "@/utils/common";

import { UserInfo as UserInfoProps } from "@/types/ui";

interface AuthStoreProps{
    openid: string | null;
    isBound: boolean | null;
    token: string | null;
    userInfo: UserInfoProps | null;
    loginInSilence: () => Promise<void>;
    loginOnBound: (
        e: any, 
        currentOpenid: string | null, 
        redirectUrl?: string
    ) => Promise<void>;
    login: (
        currentOpenid: string | null, 
        redirectUrl?: string
    ) => Promise<void>;
    logout: () => void;
    isLoggedIn: () => boolean;
    isLoggingOut: boolean;
}

const useAuthStore = create<AuthStoreProps>()(
    // 用于将状态持久化的中间件
    persist(
        (set, get) => ({
            openid: null,
            isBound: null,
            token: null,
            userInfo: null,
            // 是否正在登出
            isLoggingOut: false,

            loginInSilence: async () => {
                try {
                    const loginResponse = await Taro.login();

                    // 获取授权失败
                    if (loginResponse.errMsg !== 'login:ok') {
                        throw new Error('loginInSilence - 微信授权失败');
                    }

                    // 用 code 获取 openid 和 isBound
                    const response = await http.request({
                        url: '/api/v1/login-silence',
                        method: 'POST',
                        data: {
                            code: loginResponse.code
                        }
                    });

                    // 获取后存入本地
                    if (response?.openid && response.isBound !== null) {
                        set({ openid: response.openid, isBound: response.isBound });
                    } else {
                        throw new Error('静默处理错误');
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            loginOnBound: async (e, currentOpenid, redirectUrl?) => {
                try {
                    if (!currentOpenid) {
                        throw new Error('loginOnBound - 未获取到 openid');
                    }

                    if (e.detail.errMsg === 'getPhoneNumber:fail user deny') {
                        console.log('用户拒绝授权');
                        return ;
                    }

                    // 用 code 和 openid 获取手机号和 token
                    const response = await http.request({
                        url: '/api/v1/login-bound',
                        method: 'POST',
                        data: {
                            code: e.detail.code,
                            openid: currentOpenid
                        }
                    });

                    // 获取后存入本地
                    if (response?.token) {
                        set({ token: response.token, userInfo: response.user });
                        Taro.showToast({ title: '登录成功', icon: 'success' });

                        setTimeout(() => {
                            // 自定义跳转
                            mapsTo(redirectUrl || '/pages/index/index');
                        }, 1000);

                    } else {
                        throw new Error('登录失败');
                    }
                } catch (e) {
                    console.error(e);
                    Taro.showToast({ title: e.message || '登录失败', icon: 'none' });
                }
            },

            login: async (currentOpenid, redirectUrl?) => {
                try {
                    // 未获取到 openid
                    if (!currentOpenid) {
                        throw new Error('login - 未获取到 openid');
                    }

                    // 用 code 获取 token
                    const response = await http.request({
                        url: '/api/v1/login',
                        method: 'POST',
                        data: {
                            openid: currentOpenid
                        }
                    })

                    // 获取后存入本地
                    if (response?.token) {
                        set({ token: response.token, userInfo: response.user });
                        Taro.showToast({ title: '登录成功', icon: 'success' });
                        setTimeout(() => {
                            // 这里实则调用了 redirectTo() 跳转，在路径栈里将目标页替换掉登陆页
                            mapsTo(redirectUrl || '/pages/index/index', true);
                        }, 1000);
                    } else {
                        throw new Error('登录失败');
                    }

                } catch (e) {
                    console.error(e);
                    Taro.showToast({ title: e.message || '登录失败', icon: 'none' });
                }
            },

            logout: async () => {
                // 获取状态
                const state = get();
                if (!state.token) {
                    Taro.showToast({ title: '您并没有登陆', icon: 'none' });
                    return;
                }
                
                // 设置“正在登出”
                set({ isLoggingOut: true });

                try {
                    const response = await http.request({
                        url: '/api/v1/logout',
                        method: 'POST'
                    });
                    Taro.showToast({ 
                        title: response.message || '登出成功',
                        icon: 'success' 
                    });
                } catch (e) {
                    // 即使登出API失败 (例如网络错误)，我们仍然需要清理状态
                    console.error("后端登出 API 调用失败:", e);
                } finally {
                    set({
                        token: null,
                        userInfo: null,
                        isLoggingOut: false // <--- 重置标志位
                    });
                }
            },

            isLoggedIn: () => !!get().token
        }),
        {
            name: 'auth-storage',
            getStorage: () => ({
                getItem: (key) => Taro.getStorageSync(key),
                setItem: (key, value) => Taro.setStorageSync(key, value),
                removeItem: (key) => Taro.removeStorageSync(key),
            })
        }
    )
)

export default useAuthStore;