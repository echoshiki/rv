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
 * 用户信息
 * @param id 用户ID
 * @param name 用户昵称
 * @param phone 用户手机号
 * @param avatar 用户头像
 * @param birthday 用户生日
 * @param sex 用户性别
 * @param province 用户省份
 * @param city 用户城市
 * @param address 用户详细地址
 * @param level 用户等级
 * @param points 用户积分
 */
interface UserInfo {
    id: string,
    name: string,
    phone: string,
    avatar: string | null,
    birthday: string | null,
    sex: string | null,
    province: string | null,
    city: string | null,
    address: string | null,
    level: {
        id: number,
        name: string
    },
    points: number,
}

interface UserInfoSubmission {
    name: string,
    avatar?: string,
    birthday?: string,
    sex?: string,
    province?: string,
    city?: string,
    address?: string
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
    is_single_page: boolean,
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
    status: RegistrationStatus,
    paid_amount: string,
    payment_method: string,
    payment_time: string,
    payment_no: string,
    remarks: string,
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

interface RegistrationStatus {
    label: string,
    value: string
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
 * 报名表单数据
 */
interface RegistrationSubmission {
    activity_id: string,
    name: string,
    phone: string,
    province: string,
    city: string,
    form_data?: Record<string, any>;
    remarks?: string
}

/**
 * 发起支付的数据？
 */
interface PaymentData {
    registration_id: string,
    amount: number
}

/**
 * 返回的房车数据
 */
interface RvItem {
    id: string,
    name: string,
    cover: string,
    price: string,
    order_price: string
}

interface RvList {
    list: RvItem[],
    total: number,
    per_page: number,
    current_page: number,
    has_more_pages: boolean
}

interface RvDetail extends RvItem {
    photos: string[],
    content: string
} 

interface RvAllData {
    category: Category,
    rvs: RvList
}

interface RvOrderItem {
    id: string,
    order_no: string,
    deposit_amount: string,
    status: {
        label: string,
        value: string,
        color: string
    },
    created_at: string,
    rv: RvItem
}

interface RvOrderList {
    list: RvOrderItem[],
    total: number,
    per_page: number,
    current_page: number,
    has_more_pages: boolean
}

interface MyCarSubmission {
    name: string,
    phone: string,
    province?: string,
    city?: string,
    brand: string,
    vin: string,
    licence_plate: string,
    listing_at?: string | null,
    birthday?: string | null,
    address?: string
}

interface MyCarItem {
    id: string,
    name: string,
    phone: string,
    province?: string,
    city?: string,
    brand: string,
    vin: string,
    licence_plate: string,
    listing_at?: string | null,
    birthday?: string | null,
    address?: string
}

interface MyCarList {
    list: MyCarItem[]
}

interface PointLogList {
    list: PointLogItem[],
    total: number,
    per_page: number,
    current_page: number,
    has_more_pages: boolean
}

interface PointLogItem {
    id: string,
    operation_type: string,
    type_description: string,
    points_change: string,
    points_after_change: string,
    remarks: string,
    transaction_at: string
}

interface MaintenanceItem {
    id: string,
    name: string,
    phone: string,
    province?: string,
    city?: string,
    issues: string,
    created_at: string
}

interface MaintenanceList {
    list: MaintenanceItem[]
}

interface MaintenanceSubmission {
    name: string,
    phone: string,
    province?: string,
    city?: string,
    issues: string
}

interface SuggestSubmission {
    name: string,
    content: string
}

/**
 * 支付状态
 */
interface PaymentStatus {
    status: 'pending' | 'paid' | 'failed',
    paid_at?: string,
    transaction_id?: string,
    out_trade_no: string,
    amount: number
}

/**
 * 微信返回的用于拉起支付的参数
 */
interface PaymentParam {
    appId: string,
    timeStamp: string,
    nonceStr: string,
    package: string,
    signType: 'MD5' | 'HMAC-SHA256' | 'RSA',
    paySign: string,
    out_trade_no: string
}

/**
 * 支付详情
 */
interface PaymentDetail {
    out_trade_no: string,
    amount: number,
    status: PaymentStatus['status'],
    paid_at?: string,
    transaction_id?: string,
    created_at: string,
    updated_at: string,
}

export enum PaymentMethod {
    WECHAT = 'wechat',
    ALIPAY = 'alipay'
}

export enum PaymentStatusEnum {
    PENDING = 'pending',
    PAID = 'paid',
    FAILED = 'failed'
}
  
export enum RefundStatus {
    PENDING = 'pending',
    SUCCESS = 'success',
    FAILED = 'failed'
}

export {
    ApiResponse,
    UserInfo,
    UserInfoSubmission,
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
    RegistrationSubmission,
    RegistrationStatus,
    PaymentData,
    RvItem,
    RvList,
    RvDetail,
    RvAllData,
    RvOrderItem,
    RvOrderList,
    MyCarSubmission,
    MyCarItem,
    MyCarList,
    PointLogList,
    PointLogItem,
    MaintenanceItem,
    MaintenanceList,
    MaintenanceSubmission,
    SuggestSubmission,
    PaymentStatus,
    PaymentParam,
    PaymentDetail
}