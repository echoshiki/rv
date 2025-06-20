import { useEffect } from "react";
import useCarStore from "@/stores/car";

const useMyCarList = () => {
    const { cars, deleteCar, loading, fetchCars } = useCarStore();

    useEffect(() => {
        fetchCars();
    }, [fetchCars]);

    return {
        cars,
        loading,
        deleteCar,
        refetch: fetchCars
    }
}

export default useMyCarList;