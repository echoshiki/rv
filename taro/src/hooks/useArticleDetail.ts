import { useState, useEffect } from "react";
import { ArticleDetail } from "@/types/ui";
import { getArticleByCode, getArticleById } from "@/api/article";

const useArticleDetail = (id: string | null, code: string | null) => {
    // 文章详情
    const [articleDetail, setArticleDetail] = useState<ArticleDetail | null>(null);
    // 加载状态
    const [loading, setLoading] = useState<boolean>(false);
    // 错误信息
    const [error, setError] = useState<string | null>(null);

    const fetchArticleByCode = async (code: string) => {
        setLoading(true);
        setError(null);
        try {
            const { data } = await getArticleByCode(code);
            setArticleDetail({
                ...data,
                date: data.published_at
            });
        } catch (e) {
            setError(e.message || 'Failed to fetch articles.');
        } finally {
            setLoading(false);
        }
    };

    const fetchArticleById = async (id: string) => {
        setLoading(true);
        setError(null);
        try {
            const { data } = await getArticleById(id);
            setArticleDetail({
                ...data,
                date: data.published_at
            });
        } catch (e) {
            setError(e.message || 'Failed to fetch articles.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (id) {
            fetchArticleById(id);
        } else if (code) {
            fetchArticleByCode(code);
        }
    }, [id, code]);

    return {
        articleDetail,
        loading,
        error,
        fetchArticleByCode,
        fetchArticleById
    }
}

export {
    useArticleDetail
}