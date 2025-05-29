import { useState, useEffect, useCallback } from "react";
import { MyCarList } from "@/types/ui";
import myCarApi from "@/api/car";

const useMyCarList = () => {
    const [myCars, setMyCars] =  useState<MyCarList | null>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    // 获取我的爱车列表
    const fetchMyCarList = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const { data } = await myCarApi.list();
            setMyCars(data || []);
        } catch (e) {
            setError(e.message || 'Failed to fetch my cars.');
            setMyCars(null);
        } finally {
            setLoading(false);
        }
    }, []);

    // 删除我的爱车
    const deleteMyCar = useCallback(async (id: string) => {
        setLoading(true);
        setError(null);
        try {
            await myCarApi.delete(id);
            await fetchMyCarList();
        } catch (e) {
            setError(e.message || 'Failed to delete my car.');
        } finally {
            setLoading(false);
        }
    }, [fetchMyCarList]);

    useEffect(() => {
        fetchMyCarList();
    }, [fetchMyCarList]);

    return {
        myCars,
        loading,
        error,
        deleteMyCar,
        refetch: fetchMyCarList
    }
}

export {
    useMyCarList
}