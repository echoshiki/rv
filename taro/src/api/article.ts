import { http } from "@/utils/request";
import { ApiResponse, ArticleList, ArticleView } from "@/types/api";

const ARTICLE_API = `/api/v1/articles/`;

interface ArticleListQueryParams {
    filter?: {
        user_id?: number | string; 
        is_active?: boolean | number; 
        category_id?: number | string;  
        category_code?: string;       
        search?: string;              
    };
    orderBy?: string;
    sort?: 'asc' | 'desc';
    page?: number;
    limit?: number;
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
 }: ArticleListQueryParams = {}): Promise<ApiResponse<ArticleList>> => {
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
 * 通过文章 ID 获取文章详情
 */
const getArticleById = (id: string): Promise<ApiResponse<ArticleView>> => {
    return http.get(`${ARTICLE_API}${id}`);
};

/**
 * 通过 code 获取单页详情
 */
const getArticleByCode = (code: string): Promise<ApiResponse<ArticleView>> => {
    return http.get(`${ARTICLE_API}code/${code}`);
};

export {
    getArticleList,
    type ArticleListQueryParams,
    getArticleById,
    getArticleByCode
};