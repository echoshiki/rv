import { http } from "@/utils/request";
import { ApiResponse, PointLogList } from "@/types/api";
import { BaseQueryParams } from "@/types/query";

const POINT_LOG_API = `/api/v1/user/point-logs/consumption`;

/**
 * 获取用户积分明细（消耗记录）
 * @returns 
 */
const getPointLogList = ({
    orderBy,
    sort,
    page,
    limit
}: BaseQueryParams): Promise<ApiResponse<PointLogList>> => {

    const queryParams = {
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

    return http.get(`${POINT_LOG_API}`, queryParams);
};

export {
    getPointLogList
};