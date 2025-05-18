import { useState, useEffect, useCallback } from "react";
import { BannerItem } from "@/types/ui";
import { getBannerList } from "@/api/banner";

const useBanner = (channel: string) => {
    const [banners, setBanners] =  useState<BannerItem[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchBanners = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const { data } = await getBannerList(channel);
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

export {
    useBanner
}