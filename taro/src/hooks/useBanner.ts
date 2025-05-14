import { useState, useEffect } from "react";
import { BannerItem } from "@/types/api";
import { getBannerList } from "@/api/banner";

const useBanner = (channel: string) => {
    const [banners, setBanners] =  useState<BannerItem[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchBanners = async () => {
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
    };

    useEffect(() => {
        fetchBanners();
    }, []);

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