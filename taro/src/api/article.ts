import { http } from "@/utils/request";
import { ApiResponse, ArticleList, ArticleDetail } from "@/types/api";
import { ArticleListQueryParams, ArticleDetailQueryParams } from "@/types/query";

const ARTICLE_API = `/api/v1/articles/`;

/**
 * 用于从后端获取文章列表
 * @param params - 包含筛选、排序和分页选项的对象。
 * @returns Promise<ArticleList> - 返回一个 Promise，它会解析为文章列表数据。
 */
const getArticleList = (
    params: ArticleListQueryParams
): Promise<ApiResponse<ArticleList>> => {
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

    return http.get(ARTICLE_API, combinedParams);
};

/**
 * 获取文章详情
 * @param params - 查询参数（id 或 code）
 */
const getArticleDetail = (
    params: ArticleDetailQueryParams
): Promise<ApiResponse<ArticleDetail>> => {
    if (params.id) {
        return http.get(`${ARTICLE_API}${params.id}`);
    } else if (params.code) {
        return http.get(`${ARTICLE_API}code/${params.code}`);
    } else {
        // 如果没有提供有效的查询参数，返回一个被拒绝的 Promise
        return Promise.reject(new Error("ID 或 Code 不能为空"));
    }
};

export {
    getArticleList,
    getArticleDetail
};