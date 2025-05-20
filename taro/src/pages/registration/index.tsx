import { View } from "@tarojs/components";
import RegistrationList from "@/components/RegistrationList";

const Registration = () => {

    return (
        <View>
            <RegistrationList
                isPullDownRefresh={true}
                isReachBottomRefresh={true} 
            />
        </View>
    );
}

export default Registration;