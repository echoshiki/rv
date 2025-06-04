import { View, Image, Button } from '@tarojs/components';
import MyCarBg from '@/assets/images/mycar.jpg';
import { useDidShow } from '@tarojs/taro';
import myCarApi from '@/api/car';
import { useState } from 'react';
import { MyCarItem } from '@/types/api';
import Loading from '@/components/Loading';
import { isLoggedIn, checkLoginBeforeNavigate } from '@/utils/auth';

const MyCarSection = () => {
    // 有车、无车、未获取
    const [myCar, setMyCar] = useState<MyCarItem | null>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const fetchMyCar = async () => {
        setLoading(true);
        try {
            const response = await myCarApi.list();
            setMyCar(response.data.list[0] || null);
        } catch (error) {
            console.error('获取我的爱车失败:', error);
        } finally {
            setLoading(false);
        }
    }

    const renderContent = () => {
        if (loading) {
            return (
                <Loading />
            );
        }
        if (myCar) {
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

    useDidShow(() => {
        // 如果登录且未有我的爱车数据状态时请求        
        if (isLoggedIn() && myCar === null) {
            fetchMyCar();
        }

        // 如果未登录且有我的爱车数据状态时清除
        if (!isLoggedIn() && myCar) {
            setMyCar(null);
        }
    });

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