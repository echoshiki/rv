import { View, Text } from "@tarojs/components";
import Card from '@/components/Card';
import { useMyCarList } from '@/hooks/useMyCarList';
import Loading from "@/components/Loading";
import { showModal } from "@tarojs/taro";

const MyCar = () => {
    const { myCars, deleteMyCar, loading } = useMyCarList();

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
                    deleteMyCar(id);
                }
            }
        });
    }

    if (myCars?.list.length === 0 || !myCars) {
        return (
            <View className="bg-black flex justify-center items-center h-screen">
                <Text className="text-white">你还没有添加爱车</Text>
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
        <View className="bg-black p-5 min-h-screen">
            <View className="flex flex-col space-y-3">
                {myCars && myCars.list.map(car => (
                    <Card key={car.id} className="bg-opacity-50 !bg-[#3c3c3c] text-white">
                        <View className="flex flex-col space-y-2">
                            <View className="flex space-x-2 items-center">
                                <Text className="font-semibold text-lg">{car.licence_plate}</Text>
                                <Text className="font-light text-xs">{car.brand}</Text>
                            </View>
                            <View>
                                <View className="flex space-x-2">
                                    <Text className="font-light text-xs">{car.name}</Text>
                                    <Text className="font-light text-xs">{car.vin}</Text>
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
        </View >
    )
}

export default MyCar;