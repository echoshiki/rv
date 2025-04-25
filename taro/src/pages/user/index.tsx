import { View, Text, Image } from "@tarojs/components";
import BottomCopyright from "@/components/Copyright";
import { MenuColumn, MenuRow } from "@/components/Menu";
import { userCenterColumnMenu, userCenterRowMenu } from '@/config/menu.config';
import useAuthStore from "@/stores/auth";
import { checkLoginBeforeNavigate } from "@/utils/auth";
import UserInfoProps from "@/types/user";
import avatarRightImg from '@/assets/images/avatar-right-img.png';
import bottomImg from '@/assets/images/center-bottom-img.svg';

/**
 * 包含昵称、头像以及右边图片的用户信息块
 * @param userInfo 用户信息
 */
const UserInfo = ({ userInfo }: {
    userInfo: UserInfoProps | null
}) => {
    return (
        <View className="max-w-screen-md mx-auto rounded-xl shadow-sm flex flex-nowrap p-4 justify-between items-center bg-white">
            <View className="flex flex-nowrap space-x-3">
                {userInfo ? (
                <View className="flex flex-col justify-center">
                    <Text className="text-lg font-semibold">
                        {userInfo.name}
                    </Text>
                    <Text className="text-xs font-light font-mono text-gray-600">
                        {userInfo.phone}
                    </Text>
                </View> 
                ) : (
                <View>
                    <View className="flex flex-col justify-center" onClick={() => checkLoginBeforeNavigate()}>
                        <Text className="text-lg font-semibold">
                            未登录
                        </Text>
                        <Text className="text-xs font-light text-gray-600">
                            点击立即授权登录
                        </Text>
                    </View>
                </View>
                )}
            </View>
            <View className="py-2">
                <Image
                    className="w-24 h-16"
                    src={avatarRightImg}
                />
            </View>
        </View>
    )
}

/**
 * 用户中心
 */
const UserCenter = () => {
    const { userInfo } = useAuthStore();
    return (
        <View className="bg-gray-100 min-h-screen pb-10">
            {/* 用户信息块 */}
            <View className="relative block w-full h-32 bg-gray-950 mb-12">
                <View className="absolute w-[calc(100%-2.5rem)] left-1/2 bottom-[-2rem] -translate-x-1/2">
                    <UserInfo userInfo={userInfo} />
                </View>
            </View>

            {/* 横向菜单块 */}  
            <MenuRow menuList={userCenterRowMenu} />
            {/* 条目菜单块 */}
            <MenuColumn menuList={userCenterColumnMenu} />

            {/* 底部版权信息 */}
            <BottomCopyright
                content="版权所有 © 2025 云铺网络" 
                bottomImg={bottomImg}
            />
        </View>
    );
}

export default UserCenter;