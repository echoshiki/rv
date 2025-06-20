
import useCarStore from "@/stores/car";
import useAuthStore from "@/stores/auth";
import { useDidShow } from "@tarojs/taro";

const useMyCar = () => {
    const { isLoggedIn } = useAuthStore();
    const { cars, deleteCar, loading, fetchCars } = useCarStore();

    useDidShow(() => {
        if (isLoggedIn()) {
            fetchCars();
        }
    });

    const myCar = cars && cars.length > 0 ? cars[0] : null;

    return {
        cars,
        myCar,
        loading,
        isLoggedIn,
        deleteCar,
        refetch: fetchCars
    }
}

export default useMyCar;