import { useState, useEffect, useCallback } from "react";
import { RvAllData } from "@/types/ui";
import { getRvAllData } from "@/api/rv";

const useRvListWithCategory = () => {
    const [rvAllData, setRvAllData] = useState<RvAllData[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchRvAllData = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getRvAllData();
            // 剔除没有数据的分类
            const filteredData = responseData.filter(item => item.rvs.list.length > 0);
            setRvAllData(filteredData);
        } catch (e) {
            setError(e.message || 'Failed to fetch rvs.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchRvAllData();
    }, []);

    return {
        rvAllData,
        loading,
        error,
        refetch: fetchRvAllData
    }
}

export {
    useRvListWithCategory
}