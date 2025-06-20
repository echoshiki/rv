import { create } from "zustand";
import myCarApi from "@/api/car";
import { MyCarItem } from "@/types/ui";
import { MyCarSubmission } from "@/types/query";

interface CarStoreProps {
    cars: MyCarItem[],
    fetchCars: (force?: boolean) => Promise<void>,
    addCar: (carSubmission: MyCarSubmission) => Promise<{ success: boolean }>,
    deleteCar: (carId: string) => Promise<{ success: boolean }>,
    loading: boolean,
    hasFetched: boolean,
}

const useCarStore = create<CarStoreProps>((set, get) => ({
    cars: [],
    loading: false,
    hasFetched: false,

    // 获取我的爱车列表
    fetchCars: async (force = false) => {
        if (get().loading || (get().hasFetched && !force)) {
            return;
        }
        set({ loading: true });
        try {
            const response = await myCarApi.list();
            if (response.success) {
                set({
                    cars: response.data.list,
                    hasFetched: true
                });
            }
        } catch (error) {
            console.error('Failed to fetch cars:', error);
        } finally {
            set({ loading: false });
        }
    },
    
    // 添加我的爱车
    addCar: async(carSubmission: MyCarSubmission) => {
        try {
            const response = await myCarApi.create(carSubmission);
            if (response.success) {
                set({ cars: [...get().cars, response.data] });
                return { success: true };
            }
            throw new Error(response.message);
        } catch (error) {
            console.error('Failed to add car:', error);
            return { success: false };
        }
    },

    // 删除我的爱车
    deleteCar: async (carId) => {
        try {
            const response = await myCarApi.delete(carId);
            if (response.success) {
                set({ cars: get().cars.filter(car => car.id !== carId) });
                return { success: true };
            }
            throw new Error(response.message);
        } catch (error) {
            console.error('Failed to delete car:', error);
            return { success: false };
        }
    }
}));

export default useCarStore;