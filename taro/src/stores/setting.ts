import { create } from "zustand";
import { http } from "@/utils/request"; // 假设你已封装 http

interface SettingData {
    phone: string;
    email: string;
    address: string;
    [key: string]: any; // 允许其他配置项
}

interface SettingStoreProps {
    settings: Partial<SettingData>;
    isLoading: boolean;
    fetchSettings: () => Promise<void>;
}

const useSettingStore = create<SettingStoreProps>((set) => ({
    // 全局设置
    settings: {},
    isLoading: false,
    fetchSettings: async () => {
        set({ isLoading: true });
        try {
            const response = await http.request({
                url: '/api/v1/settings/general',
                method: 'GET'
            });

            if (response?.data) {
                set({ settings: response.data, isLoading: false });
                console.log('Global settings loaded successfully.');
            } else {
                 throw new Error("Failed to parse settings data");
            }

        } catch (error) {
            console.error("Failed to fetch global settings:", error);
            set({ isLoading: false });
        }
    }
}));

export default useSettingStore;
