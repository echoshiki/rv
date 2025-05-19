import { useState, useEffect, useCallback } from "react";
import { Category } from "@/types/ui";
import { getActivityCategoryList } from "@/api/activity";

const useActivityCategoryList = () => {
    const [categories, setCategories] =  useState<Category[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchCategories = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const { data } = await getActivityCategoryList();
            setCategories(data);
        } catch (e) {
            setError(e.message || 'Failed to fetch categories.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchCategories();
    }, [fetchCategories]);

    return {
        categories,
        loading,
        error,
        refetch: fetchCategories
    }
}

export {
    useActivityCategoryList
}