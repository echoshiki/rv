import {
    UserInfo,
    BannerItem,
    Category as ApiCategory,
    ActivityItem,
    ActivityDetail,
    RegionItem,
    RegistrationItem,
    StatusItem,
    RegistrationActivity,
    RvOrderItem,
    RvOrderList,
    PointLogItem,
    PointLogList,
    MyCarItem,
    MyCarList,
    ArticleItem,
    ArticleDetail,
    RvItem,
    RvDetail,
    RvAllData
} from './api';

/**
 * 页面内的菜单项
 * 由菜单接口原始数据转换而来
 * @param title 菜单标题
 * @param icon 菜单图标
 * @param description 菜单副标题
 * @param link 菜单链接
 * @param onClick 菜单点击事件
 * @param openType 打开方式: contact, feedback, ...
 */
interface MenuItem {
    title: string,
    icon: string,
    description?: string,
    link: string,
    onClick?: (item: MenuItem) => void,
    openType?: string
}

/**
 * 分类项
 * 同于 Tab 组件，由接口分类原始数据转换而来
 * @param channel 频道
 */
interface Category extends ApiCategory {
    channel?: string
}

/**
 * 用户报名表单初始提交数据
 * @param name 姓名
 * @param phone 手机号
 * @param address 地址（未格式化）
 * @param remarks 备注
 */
interface RegistrationFormData {
    name: string,
    phone: string,
    address: string,
    remarks: string
}

export {
    UserInfo,
    BannerItem,
    StatusItem,
    MenuItem,
    ArticleItem,
    ArticleDetail,
    Category,
    ActivityItem,
    ActivityDetail,
    RegistrationItem,
    RegistrationActivity,
    RegistrationFormData,
    RegionItem,
    RvItem,
    RvDetail,
    RvAllData,
    MyCarItem,
    MyCarList,
    PointLogList,
    PointLogItem,
    RvOrderItem,
    RvOrderList
}