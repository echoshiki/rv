import { useState, useEffect, useCallback } from "react";
import { RvDetail } from "@/types/ui";
import { getRvDetail } from "@/api/rv";

const useRvDetail = (id: string, used: boolean = false) => {
    const [rvDetail, setRvDetail] = useState<RvDetail | null>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    // 获取房车详情
    const fetchRvDetail = useCallback(async () => {

        if (!id) {
            setError("房车 ID 不能为空");
            return;   
        }

        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getRvDetail(id, used ?? false);
            setRvDetail(responseData);
        } catch (e) {
            setError(e.message || '获取房车详情时出现问题');
        } finally {
            setLoading(false);
        }
    }, [id, used]);

    useEffect(() => {
        fetchRvDetail();
    }, [fetchRvDetail]);

    // 手动刷新
    const refresh = useCallback(() => {
        return fetchRvDetail();
    }, [fetchRvDetail]);

    return {
        rvDetail,
        loading,
        error,
        refresh
    }
}

export {
    useRvDetail
}