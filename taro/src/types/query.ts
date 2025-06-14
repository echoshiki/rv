/**
 * 基础查询参数
 * @param filter 过滤条件
 * @param orderBy 排序字段
 * @param sort 排序方式
 * @param page 页码
 * @param limit 每页数量
 */
interface BaseQueryParams {
    filter?: Record<string, any>;
    orderBy?: string;
    sort?: 'asc' | 'desc';
    page?: number;
    limit?: number;
}

/**
 * 文章列表查询参数
 * @param filter 过滤条件
 * @param orderBy 排序字段
 * @param sort 排序方式
 * @param page 页码
 * @param limit 每页数量
 */
interface ArticleListQueryParams extends BaseQueryParams {
    filter: {
        user_id?: number | string;
        category_id?: number | string;
        category_code?: string;
        search?: string;
    }
}

/**
 * 文章详情查询参数
 * @param id 文章ID
 * @param code 文章Code
 */
interface ArticleDetailQueryParams {
    id?: string,
    code?: string,
}


/**
 * 活动列表查询参数
 * @param filter 过滤条件
 * @param orderBy 排序字段
 * @param sort 排序方式
 * @param page 页码
 * @param limit 每页数量
 */
interface ActivityListQueryParams extends BaseQueryParams {
    filter: {
        user_id?: number | string;
        category_id?: number | string;
        is_recommend?: number;
        search?: string;
    }
}

/**
 * 用户修改资料提交数据
 * @param name 用户名
 * @param avatar 头像
 * @param birthday 生日
 * @param sex 性别
 * @param province 省份
 * @param city 城市
 * @param address 地址
 */
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
 * 报名表单提交数据
 * @param activity_id 活动ID
 * @param name 称呼
 * @param phone 手机号
 * @param province 省份
 * @param city 城市
 * @param form_data 表单数据
 * @param remarks 备注
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
 * 添加爱车提交数据
 * @param name 称呼
 * @param phone 手机号
 * @param province 省份
 * @param city 城市
 * @param brand 品牌
 * @param vin 车架号
 * @param licence_plate 车牌号
 * @param listing_at 上牌时间
 * @param birthday 生日
 * @param address 地址
 */
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

/**
 * 维保预约表单提交数据
 * @param name 称呼
 * @param phone 手机号
 * @param province 省份
 * @param city 城市
 * @param issues 问题描述
 */
interface MaintenanceSubmission {
    name: string,
    phone: string,
    province?: string,
    city?: string,
    issues: string
}

/**
 * 用户建议表单提交数据
 * @param name 称呼
 * @param content 建议内容
 */
interface SuggestSubmission {
    name: string,
    content: string
}

export {
    BaseQueryParams,
    ArticleListQueryParams,
    ArticleDetailQueryParams,
    ActivityListQueryParams,
    UserInfoSubmission,
    RegistrationSubmission,
    MyCarSubmission,
    MaintenanceSubmission,
    SuggestSubmission
}