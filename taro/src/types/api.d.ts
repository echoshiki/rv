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

interface Category {
    id: string,
    title: string,
    description: string,
    code: string
}

interface ArticleItem {
    id: string,
    title: string,
    cover: string | null,
    is_recommend: boolean,
    category: Category,
    published_at: string
}

interface ArticleList {
    list: ArticleItem[],
    total: number,
    per_page: number,
    current_page: number,
    has_more_pages: boolean
}

interface ArticleDetail {
    id: string,
    title: string,
    content: string,
    cover: string | null,
    is_recommend: boolean,
    published_at: string,
    category: Category | null
}

interface ActivityItem {
    id: string,
    title: string,
    cover: string | null,
    description: string,
    registration_fee: string,
    max_participants: number,
    current_participants: number,
    registration_start_at: string,
    registration_end_at: string,
    started_at: string,
    ended_at: string,
    is_recommend: boolean,
    category: Category,
    published_at: string
}

interface ActivityList {
    list: ActivityItem[],
    total: number,
    per_page: number,
    current_page: number,
    has_more_pages: boolean
}

interface ActivityDetail extends ActivityItem {
    content: string
}

interface RegionItem {
    code: string,
    name: string
}

/**
 * 返回的报名记录
 */
interface RegistrationItem {
    id: string,
    activity_id: string,
    registration_no: string,
    name: string,
    phone: string,
    province: string,
    city: string,
    status: string,
    paid_amount: string,
    payment_method: string,
    payment_time: string,
    payment_no: string,
    created_at: string,
    updated_at: string,
    activity: RegistrationActivity
}

interface RegistrationActivity {
    id: string,
    title: string,
    cover: string | null,
    started_at: string,
    ended_at: string,
}

/**
 * 返回的报名列表
 */
interface RegistrationList {
    list: RegistrationItem[],
    total: number,
    per_page: number,
    current_page: number,
    has_more_pages: boolean
}

/**
 * 报名提交数据
 */
interface RegistrationSubmission {
    activity_id: string,
    name: string,
    phone: string,
    province: string,
    city: string,
    remarks?: string
}

export {
    ApiResponse,
    BannerItem,
    MenuItem,
    ArticleList,
    ArticleDetail,
    ActivityList,
    ActivityDetail,
    Category,
    RegionItem,
    RegistrationItem,
    RegistrationList,
    RegistrationSubmission
}