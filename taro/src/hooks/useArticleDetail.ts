import { useState, useEffect, useCallback } from "react";
import { ArticleDetail } from "@/types/ui";
import { getArticleDetail, type ArticleDetailQueryParams } from "@/api/article";

interface UseArticleDetailOptions {
    autoLoad?: boolean;
    onLoaded?: (detail: ArticleDetail) => void;
}

const useArticleDetail = (
    params: ArticleDetailQueryParams,
    options: UseArticleDetailOptions = {}
) => {
    const { autoLoad = true, onLoaded } = options;

    const [articleDetail, setArticleDetail] = useState<ArticleDetail | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    // 获取文章详情
    const fetchArticleDetail = useCallback(async () => {
        // 检测参数
        if (!params.id && !params.code) {
            setError("文章 ID 或 Code 不能为空");
            return;   
        }

        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getArticleDetail(params);
            setArticleDetail({
                ...responseData,
                date: responseData.published_at,
                category: responseData.category || undefined
            });
            // 执行加载完成后的回调
            if (onLoaded && articleDetail) {
                onLoaded(articleDetail);
            }
        } catch (e) {
            setError(e.message || '获取文章详情时出现问题');
        } finally {
            setLoading(false);
        }
    }, [params.id, params.code, onLoaded]);

    useEffect(() => {
        if (autoLoad && (params.id || params.code)) {
            fetchArticleDetail();
        }
    }, [fetchArticleDetail, autoLoad]);

    // 手动刷新
    const refresh = useCallback(() => {
        return fetchArticleDetail();
    }, [fetchArticleDetail]);

    return {
        articleDetail,
        loading,
        error,
        refresh
    }
}

export {
    useArticleDetail
}