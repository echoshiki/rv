import { useState, useEffect } from "react";
import { ArticleItem } from "@/types/ui";
import { getArticleList, type ArticleListQueryParams } from "@/api/article";

const useArticleList = (params: ArticleListQueryParams) => {
    const [articleList, setArticleList] =  useState<ArticleItem[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchArticles = async () => {
        setLoading(true);
        setError(null);
        try {
            const { data } = await getArticleList(params);
            setArticleList(data.list.map(item => ({
                ...item,
                date: item.published_at
            })) || []);
        } catch (e) {
            setError(e.message || 'Failed to fetch articles.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchArticles();
    }, []);

    return {
        articleList,
        loading,
        error,
        refresh: fetchArticles
    }
}

export {
    useArticleList
}