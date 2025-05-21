import { View } from "@tarojs/components";
import RegistrationList from "@/components/RegistrationList";

const Registration = () => {

    return (
        <View className="w-full min-h-screen bg-gray-100 p-5">
            <RegistrationList
                isPullDownRefresh={true}
                isReachBottomRefresh={true} 
            />
        </View>
    );
}

export default Registration;