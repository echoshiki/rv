import { useState, useEffect, useCallback } from "react";
import { ActivityDetail } from "@/types/ui";
import { getActivityDetail } from "@/api/activity";

interface UseActivityDetailOptions {
    autoLoad?: boolean;
    // 加载数据后的回调
    onLoaded?: (detail: ActivityDetail) => void;
}

const useActivityDetail = (
    id: string | undefined,
    options: UseActivityDetailOptions = {}
) => {
    const { autoLoad = true, onLoaded } = options;

    const [activityDetail, setActivityDetail] = useState<ActivityDetail | null>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    // 获取文章详情
    const fetchActivityDetail = useCallback(async () => {
        // 检测参数
        if (!id ) {
            setError("活动 ID 不能为空");
            return;   
        }

        setLoading(true);
        setError(null);

        try {
            const { data: responseData } = await getActivityDetail(id);
            setActivityDetail({
                ...responseData,
                date: responseData.published_at,
                category: responseData.category || undefined
            });
            // 执行加载完成后的回调
            if (onLoaded && activityDetail) {
                onLoaded(activityDetail);
            }
        } catch (e) {
            setError(e.message || '获取活动详情时出现问题');
        } finally {
            setLoading(false);
        }
    }, [id, onLoaded]);

    useEffect(() => {
        if (autoLoad && id) {
            fetchActivityDetail();
        }
    }, [fetchActivityDetail, autoLoad]);

    // 手动刷新
    const refresh = useCallback(() => {
        return fetchActivityDetail();
    }, [fetchActivityDetail]);

    return {
        activityDetail,
        loading,
        error,
        refresh
    }
}

export {
    useActivityDetail
}