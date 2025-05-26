import Taro from '@tarojs/taro';
import registrationApi from '@/api/registration';

/**
 * 判断当前页面是否在 tabBar 中
 * @param path 当前页面路径
 * @returns 是否在 tabBar 中
 */
const isTabBarPage = (path: string): boolean => {

    const appConfig = Taro.getApp().config;

    if (appConfig.tabBar && appConfig.tabBar.list) {
        // 检查当前 path 是否以斜杠开头，如果是去掉斜杠，如果不是直接返回
        const normalizedPath = path.startsWith('/') ? path.substring(1) : path;
        // 检查当前 path 是否在 tabBar 中
        return appConfig.tabBar.list.some(item => item.pagePath === normalizedPath);
    }
    return false;
}

/**
 * 获取当前页面URL（包含参数）
 * @returns 当前页面URL（包含参数）
 */
const getCurrentPageUrl = (): string => {
    // 获取所有打开的页面实例数组
    const pages = Taro.getCurrentPages();

    // 获取数组中最后一个页面实例
    const currentPage = pages[pages.length - 1];

    // 获取当前页面路由路径
    const route = `/${currentPage.route}`;

    // 获取当前页面路由参数
    const params = currentPage.options;

    let queryString = '';

    // 如果当前页面路由参数不为空，则拼接参数
    // entire 将 params 对象转换成包含键值对的数组
    // 最后遍历数组，将键值对组合成字符串
    if (Object.keys(params).length > 0) {
        queryString = '?' + Object.entries(params)
            .map(([key, value]) => `${key}=${value}`)
            .join('&');
    }

    // 返回当前页面路由路径和参数
    return route + queryString;
}

/**
 * 自定义跳转函数
 */
const mapsTo = (url: string, isLoginPage = false) => {
    // 获取 url 中的路径部分，不包含参数
    const pathWithoutParams = url.split('?')[0];

    // 判断当前页面是否在 tabBar 中
    if (isTabBarPage(pathWithoutParams)) {
        Taro.switchTab({ url });
    } else {
        isLoginPage ? Taro.redirectTo({ url }) : Taro.navigateTo({ url });
    }
}

/**
 * 格式化富文本中的图片
 * @param html 
 * @returns 
 */
const cleanHTML = (html: string, noMargin: boolean = false) => {
    return html
        // 移除 figure 标签但保留内部内容
        .replace(/<figure[^>]*>/g, '').replace(/<\/figure>/g, '')
        // 移除 figcaption 标签
        .replace(/<figcaption[^>]*>.*?<\/figcaption>/g, '')
        // 移除 data-trix-* 自定义属性
        .replace(/ data-trix-[^=]+="[^"]*"/g, '')
        // 为图片添加自适应样式（核心修改）
        .replace(/<img([^>]*)>/gi, (_match, attrs) => {
            // 保留原有属性，移除可能存在的width/height
            const cleanAttrs = attrs
                .replace(/(width|height)\s*=\s*["']\d+["']/gi, '')
                .replace(/style\s*=\s*["'][^"']*["']/gi, '');
            return `<img style="margin-top:${noMargin ? '0' : '10px'};margin-bottom:${noMargin ? '0' : '10px'};max-width:100%;height:auto;${cleanAttrs.match(/style\s*=\s*["']([^"']*)["']/)?.[1] || ''}" ${cleanAttrs}>`;
        });
};

/**
 * 检查用户报名状况
 */
const checkRegistrationStatus = async (activityId: string) => {
    try {
        const { data } = await registrationApi.status(activityId);

        if (
            !data || 
            data.value === 'cancelled' || 
            data.value === 'rejected'
        ) return {
            isRegistration: true,
            message: ""
        }

        if (data.value === 'approved') {
            return {
                isRegistration: false,
                message: "您已报名，无需重复报名"
            }
        }

        if (data.value === 'pending') {
            return {
                isRegistration: false,
                message: "您似乎有尚未付款的报名信息，请去个人中心查看"
            }
        }

    } catch (e) {
        console.log('获取报名状态失败', e.message);
        return;
    }

    return {
        isRegistration: false,
        message: "网络请求出现了问题，请稍后再试"
    };
}

export {
    isTabBarPage,
    getCurrentPageUrl,
    mapsTo,
    cleanHTML,
    checkRegistrationStatus
};
