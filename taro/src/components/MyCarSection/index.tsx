import { View, Image, Button } from '@tarojs/components';
import MyCarBg from '@/assets/images/mycar.jpg';
import Loading from '@/components/Loading';
import { checkLoginBeforeNavigate } from '@/utils/auth';
import useMyCar from '@/hooks/useMyCar';

const MyCarSection = () => {
    const { myCar, loading, isLoggedIn } = useMyCar();

    const renderContent = () => {
        if (loading) {
            return <Loading />
        }

        if (myCar && isLoggedIn()) {
            return (
                <View className="w-4/5 bg-[#3c3c3c] bg-opacity-50 p-5 rounded-lg text-white flex flex-col space-y-1">
                    <View className="text-lg font-semibold">
                        {myCar.licence_plate}
                    </View>
                    <View className="text-xs text-gray-400">
                        {myCar.name} 
                    </View>
                    <View className="text-xs text-gray-400">
                        {myCar.brand}
                    </View>
                </View>
            );
        }

        return (
            <Button
                className="w-52 !border-2 !border-solid !border-white text-white bg-transparent"
                onClick={() => checkLoginBeforeNavigate('/pages/usage/car/add/index')}
            >
                添加爱车
            </Button>
        );
    }

    return (
        <View className="w-full relative flex justify-center">
            <Image
                src={MyCarBg}
                mode={'widthFix'}
                className="w-full"
            />
            <View className="w-full h-full absolute flex justify-center items-center">
                {renderContent()}
            </View>
        </View>
    )
}

export default MyCarSection;