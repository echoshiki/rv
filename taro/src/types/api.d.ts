/**
 * API 响应泛用类型
 */
interface ApiResponse<T> {
    success: boolean;
    code: number;
    message: string;
    data: T;
}

/**
 * 轮播图
 * @param id 轮播图ID
 * @param image 图片路径
 * @param title 轮播图标题
 * @param link 跳转链接
 */
interface BannerItem {
    id: string,
    image: string,
    title?: string,
    link?: string
}

/**
 * 菜单项
 * @param id 菜单项ID
 * @param title 菜单标题
 * @param subtitle 菜单副标题
 * @param icon 菜单图标
 * @param cover 菜单封面
 * @param link_type 链接类型: page, miniprogram, webview, function
 * @param link_value 链接值
 * @param requires_auth 是否需要登录
 */
interface MenuItem {
    id: string,
    title: string,
    subtitle?: string,
    icon?: string,
    cover?: string,
    link_type: string,
    link_value?: string,
    requires_auth: boolean
}

interface ArticleItem {
    id: string,
    title: string,
    cover?: string,
    published_at: string
}

interface ArticleList {
    list: ArticleItem[],
    total: number,
    limit: number,
    page: number,
    has_more_pages: boolean
}

interface ArticleView {
    id: string,
    title: string,
    content: string,
    cover?: string,
    published_at: string
    category: any
}

export {
    ApiResponse,
    BannerItem,
    MenuItem,
    ArticleList,
    ArticleView
}