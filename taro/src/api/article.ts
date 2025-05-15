import { http } from "@/utils/request";
import { ApiResponse, ArticleList, ArticleDetail } from "@/types/api";

const ARTICLE_API = `/api/v1/articles/`;

// 文章列表查询参数
interface ArticleListQueryParams {
    filter: {
        user_id?: number | string;
        category_id?: number | string;
        category_code?: string;
        search?: string;
    };
    orderBy?: string;
    sort?: 'asc' | 'desc';
    page?: number;
    limit?: number;
}

// 文章详情查询参数
interface ArticleDetailQueryParams {
    id?: string,
    code?: string,
}

/**
 * 用于从后端获取文章列表
 * @param params - 包含筛选、排序和分页选项的对象。
 * @returns Promise<ArticleList> - 返回一个 Promise，它会解析为文章列表数据。
 */
const getArticleList = ({
    filter,
    orderBy,
    sort,
    page,
    limit
}: ArticleListQueryParams): Promise<ApiResponse<ArticleList>> => {
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

    return http.get(ARTICLE_API, queryParams);
};

/**
 * 获取文章详情
 * @param params - 查询参数（id 或 code）
 */
const getArticleDetail = (params: ArticleDetailQueryParams): Promise<ApiResponse<ArticleDetail>> => {
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
    type ArticleListQueryParams,
    getArticleDetail,
    type ArticleDetailQueryParams
};