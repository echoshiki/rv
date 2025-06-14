import { http } from "@/utils/request";
import { ApiResponse, ActivityList, ActivityDetail, Category } from "@/types/api";
import { ActivityListQueryParams } from "@/types/query";

const ACTIVITY_API = `/api/v1/activities/`;

/**
 * 用于从后端获取活动列表
 * @param params - 包含筛选、排序和分页选项的对象。
 * @returns Promise<ActivityList> - 返回一个 Promise，它会解析为活动列表数据。
 */
const getActivityList = (
    params: ActivityListQueryParams
): Promise<ApiResponse<ActivityList>> => {
    // 将条件字段扁平化后构成新的条件对象
    const combinedParams: Record<string, any> = {
        ...(params.filter || {}),
        orderBy: params.orderBy,
        sort: params.sort,
        page: params.page,
        limit: params.limit,
    };

    // 清理未定义的参数
    Object.keys(combinedParams).forEach(key => {
        if (combinedParams[key] === undefined || combinedParams[key] === null) {
            delete combinedParams[key];
        }
    });

    return http.get(ACTIVITY_API, combinedParams);
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

export {
    getActivityList,
    getActivityDetail,
    getActivityCategoryList
};