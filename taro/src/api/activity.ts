import { http } from "@/utils/request";
import { ApiResponse, ActivityList, ActivityDetail, Category, RegistrationStatus } from "@/types/api";

const ACTIVITY_API = `/api/v1/activities/`;

// 活动列表查询参数
interface ActivityListQueryParams {
    filter: {
        user_id?: number | string;
        category_id?: number | string;
        is_recommend?: number;
        search?: string;
    };
    orderBy?: string;
    sort?: 'asc' | 'desc';
    page?: number;
    limit?: number;
}

/**
 * 用于从后端获取活动列表
 * @param params - 包含筛选、排序和分页选项的对象。
 * @returns Promise<ActivityList> - 返回一个 Promise，它会解析为活动列表数据。
 */
const getActivityList = ({
    filter,
    orderBy,
    sort,
    page,
    limit
}: ActivityListQueryParams): Promise<ApiResponse<ActivityList>> => {
    // 条件字段
    const queryParams: any = {
        ...filter,
        orderBy,
        sort,
        page,
        limit,
    };

    // 清理未定义的参数
    Object.keys(queryParams).forEach(key => {
        if (queryParams[key] === undefined || queryParams[key] === null) {
            delete queryParams[key];
        }
    });

    return http.get(ACTIVITY_API, queryParams);
};

/**
 * 获取活动详情
 * @param id - 活动ID
 */
const getActivityDetail = (id: string): Promise<ApiResponse<ActivityDetail>> => {
    return http.get(`${ACTIVITY_API}${id}`);
};

const getActivityCategoryList = (): Promise<ApiResponse<Category[]>> => {
    return http.get(`${ACTIVITY_API}categories`);
};

/**
 * 获取当前用户的活动报名状态
 * @param id - 活动ID
 * @returns 
 */
const getActivityStatus = (id: string): Promise<ApiResponse<RegistrationStatus | null>> => {
    return http.get(`${ACTIVITY_API}${id}/status`);
};

export {
    getActivityList,
    type ActivityListQueryParams,
    getActivityDetail,
    getActivityCategoryList,
    getActivityStatus,
};