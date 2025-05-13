import { View, Text, Image } from "@tarojs/components";
import DefaultCover from '@/assets/images/cover.jpg';

const Detail = () => {
    return (
        <View className="bg-gray-100 min-h-screen">
            <View className="w-full">
                <View className="relative block h-0 p-0 overflow-hidden pb-[50%]">
                    <Image
                        src={DefaultCover}
                        className="absolute object-cover w-full h-full border-none align-middle" 
                        mode={`aspectFill`}
                    />
                </View>
            </View>
            <View className="px-5 relative mt-[-3rem]">
                <View className="bg-white rounded-xl p-5">
                    <View className="mb-5">
                        <Text className="text-xl font-bold text-justify">2018年买皮卡房车选哪款合适年买皮卡房车选哪款合适2018年买皮卡房车选哪款合适？</Text>
                        <View className="flex flex-nowrap space-x-3 mt-2">
                            <Text className="underline">用车常识</Text>
                            <Text className="text-gray-500">2018年12月01日</Text>
                        </View>
                    </View>
                    <View>
                        <Text>
                        随着中国房车市场的逐步成熟，越来越多的人喜欢上了房车生活，而且房车生产制造业也是越来越成熟，皮卡房车一款适合中国大众需求的房车种类，那么，2018年迈皮卡房车选哪款比较合适呢？
                        </Text>
                        <Text>
                        览众C7房车一经上市非常受欢迎，超大的储物空间，大无止境的房车生活，是这款车的亮点所在。超大储物箱，单排驾驶后部生活空间大。采用的是房车专用底盘，轴距加长后轮距加宽，能让房车前后轴受力更均匀，行驶更加稳定，变道安全。柴油四驱行走在各种路况中都没问题。
                        </Text>
                        <Text>
                        柴油四驱房车，长城自主研发的绿静发动机，动力十足。经典的布局设计，两用的上下铺床，以及升级后的三七开，充分利用空间，让房车空间有效利用。加上房车专用底盘，让房车更加安全可靠。
                        </Text>
                    </View>
                </View>
            </View>
        </View>
    );
}

export default Detail;