import { View, Text, Image } from "@tarojs/components";
import BottomCopyright from "@/components/Copyright";
import { MenuColumn, MenuRow } from "@/components/Menu";
import useAuthStore from "@/stores/auth";
import { UserInfo } from "@/types/ui";
import { useMenu } from "@/hooks/useMenu";
import { checkLoginBeforeNavigate } from "@/utils/auth";
import avatarRightImg from '@/assets/images/avatar-right-img.png';
import bottomImg from '@/assets/images/center-bottom-img.svg';
import { usePullDownRefresh, stopPullDownRefresh } from "@tarojs/taro";
import { LevelBadge } from '@/components/CustomBadge';
import { Tag } from '@nutui/nutui-react-taro';

/**
 * 包含昵称、头像以及右边图片的用户信息块
 * @param userInfo 用户信息
 */
const UserInfoArea = ({ userInfo }: {
    userInfo: UserInfo | null
}) => {
    return (
        <View className="max-w-screen-md mx-auto rounded-md shadow-sm flex flex-nowrap p-4 justify-between items-center bg-white">
            <View className="flex flex-nowrap space-x-3">
                {userInfo ? (
                <View className="flex flex-col justify-center space-y-1">
                    <View className="text-lg font-semibold leading-tight">
                        {userInfo.name}
                    </View>
                    <View className="text-xs font-light  text-gray-600">
                        {userInfo.phone}
                    </View>
                    <View>
                        <View className="flex items-center space-x-2">
                            <LevelBadge level={userInfo.level} />
                            <Tag plain>{userInfo.points} 积分</Tag>
                        </View>
                    </View>
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
    const { userInfo, syncUserInfo, isLoading, isLoggedIn } = useAuthStore();

    // 原始菜单数据
    const { rawMenuItems: userRowMenu, loading: userRowLoading } = useMenu('user_row_menu');
    const { rawMenuItems: userColumnMenu, loading: userColumnLoading } = useMenu('user_column_menu');

    usePullDownRefresh(async () => {
        // 未登录和加载中时不触发刷新
        if (!isLoading && isLoggedIn()) { 
            await syncUserInfo();
        }
        stopPullDownRefresh();
    });

    return (
        <View className="bg-gray-100 min-h-screen pb-10">
            {/* 用户信息块 */}
            <View className="relative block w-full h-32 bg-gray-950 mb-12">
                <View className="absolute w-[calc(100%-2.5rem)] left-1/2 bottom-[-2rem] -translate-x-1/2">
                    <UserInfoArea userInfo={userInfo} />
                </View>
            </View>

            {/* 横向菜单块 */} 
            <View className="px-5 mt-4">
                <MenuRow 
                    menuList={userRowMenu} 
                    isLoading={userRowLoading}
                />
            </View> 

            {/* 条目菜单块 */}
            <View className="px-5 mt-4">
                <MenuColumn 
                    menuList={userColumnMenu} 
                    isLoading={userColumnLoading}
                />
            </View>

            {/* 底部版权信息 */}
            <BottomCopyright
                content="版权所有 © 2025 云铺网络" 
                bottomImg={bottomImg}
            />
        </View>
    );
}

export default UserCenter;