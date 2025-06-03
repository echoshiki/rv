import { useState, useEffect, useCallback } from "react";
import { BannerItem } from "@/types/ui";
import bannerApi from "@/api/banner";
import { useActivityList } from "./useActivityList";
import { useMemo } from "react";

const useBanner = (channel: string) => {
    const [banners, setBanners] =  useState<BannerItem[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    const fetchBanners = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const { data } = await bannerApi.get(channel);
            setBanners(data || []);
        } catch (e) {
            setError(e.message || 'Failed to fetch banners.');
        } finally {
            setLoading(false);
        }
    }, [channel]);

    useEffect(() => {
        fetchBanners();
    }, [fetchBanners]);

    return {
        banners,
        loading,
        error,
        refetch: fetchBanners
    }
}

const useActivityBanner = () => {
    // 获取管理后台上传的轮播图
    const { banners: cmsBanners, loading: cmsLoading } = useBanner("activity");

    // 获取被推荐的活动列表
    const { activityList: activityRecommend, loading: recommendLoading } = useActivityList({
        filter: {
            is_recommend: 1
        },
        limit: 5
    });

    // 混合它们
    const activityBanners = useMemo(() => {
        const bannerFromActivityRecommend = activityRecommend.map(item => ({
            id: item.id,
            image: item.cover || '',
            title: item.title,
            link: `/pages/activity/detail/${item.id}`
        }));
        return [...cmsBanners, ...bannerFromActivityRecommend];
    }, [cmsBanners, activityRecommend]);

    const isLoading = cmsLoading || recommendLoading;

    return {
        banners: activityBanners,
        loading: isLoading
    }
}

export {
    useBanner,
    useActivityBanner
}