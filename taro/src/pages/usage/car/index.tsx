import { View, Text, Button } from "@tarojs/components";
import Card from '@/components/Card';
import Loading from "@/components/Loading";
import { showModal } from "@tarojs/taro";
import { mapsTo } from "@/utils/common";
import useMyCar from "@/hooks/useMyCar";

const AddMyCarButton = () => {
    return (
        <View className="flex justify-center items-center mx-auto pb-10 min-h-36">
            <Button 
                className="w-52 !border-2 !border-solid !border-white text-white bg-transparent"
                onClick={() => mapsTo('/pages/usage/car/add/index')}
            >
                添加爱车
            </Button>
        </View>
    )
}

const MyCar = () => {
    const { cars, deleteCar, loading } = useMyCar();

    const onDeleteMyCar = async (id: string) => {
        if (loading) return;

        await showModal({
            title: '解绑爱车',
            content: '确定要解绑这辆车吗？',
            confirmText: '解绑',
            cancelText: '取消',
            confirmColor: '#e54d42',
            success: (res) => {
                if (res.confirm && !loading) {
                    deleteCar(id);
                }
            }
        });
    }

    if (cars?.length === 0 || !cars) {
        return (
            <View className="bg-black flex flex-col justify-center items-center h-screen">
                <Text className="text-white">你还没有添加爱车</Text>
                <AddMyCarButton />
            </View>
        );
    }

    if (loading) {
        return (
            <View className="bg-black flex justify-center items-center h-screen">
                <Loading />
            </View>
        );
    }

    return (
        <View className="bg-black p-5 min-h-screen flex flex-col justify-between">
            <View className="flex flex-col space-y-3">
                {cars && cars.map(car => (
                    <Card key={car.id} className="bg-opacity-50 !bg-[#3c3c3c] text-white">
                        <View className="flex flex-col space-y-2">
                            <View className="flex space-x-2 items-center">
                                <Text className="font-semibold text-lg">{car.vin}</Text>
                                <Text className="font-light text-xs">{car.brand}</Text>
                            </View>
                            <View>
                                <View className="flex space-x-2">
                                    <Text className="font-light text-xs">{car.name}</Text>
                                    <Text className="font-light text-xs">{car.licence_plate || '暂无车牌号'}</Text>
                                </View>
                                <View className="flex flex-nowrap justify-between items-center">
                                    <View>
                                        <Text className="font-light text-xs">
                                            上牌日期：{car.listing_at ?? '-'}
                                        </Text>
                                    </View>
                                    <View onClick={() => onDeleteMyCar(car.id)}>
                                        <Text className="font-light text-xs underline">
                                            解绑爱车
                                        </Text>
                                    </View>
                                </View>
                            </View>
                        </View>
                    </Card>
                ))}
            </View>
            <AddMyCarButton />
        </View >
    )
}

export default MyCar;