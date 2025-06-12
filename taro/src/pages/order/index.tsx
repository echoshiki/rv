import { View } from "@tarojs/components";
import RvOrderList from "@/components/RvOrderList";

const Order = () => {

    return (
        <View className="w-full min-h-screen bg-gray-100 p-5">
            <RvOrderList
                isPullDownRefresh={true}
                isReachBottomRefresh={true} 
            />
        </View>
    );
}

export default Order;