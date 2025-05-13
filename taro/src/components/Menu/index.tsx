import { View, Text, Image } from "@tarojs/components";
import { mapsTo } from "@/utils/common";
import { MenuItem, MenuList } from '@/types/ui';
import RightArrowIcon from '@/assets/icons/right-arrow.svg'

// 处理菜单项的点击
const handleItemClick = (item: MenuItem) => {
    if (item.onClick) {
        item.onClick(item);
        return;
    }
    if (item.link) mapsTo(item.link);
}

/**
 * 用户中心竖向菜单
 * @param menuList 菜单数据
 */
const MenuColumn = ({ menuList }: MenuList) => {
    return (
        <View className="px-5 mt-4">
            <View className="bg-white rounded-xl">
                {menuList.map((item, index) => (
                    <View 
                        key={index} 
                        className="flex items-center justify-between p-4 border-b border-gray-100" 
                        onClick={() => handleItemClick(item)}
                    >
                        <View className="flex items-center">
                            <Image src={item.icon} className="w-5 h-5" />
                            <Text className="text-gray-800 ml-3">{item.title}</Text>
                        </View>
                        <Image src={RightArrowIcon} className="w-4 h-4" />
                    </View>
                ))}
            </View>
        </View>
    )
}

/**
 * 横向带图标的菜单
 * @param menuList 菜单数据
 */
const MenuRow = ({ menuList }: MenuList) => {
    return (
        <View className="px-5 mt-4">
            <View className="w-full py-5 rounded-xl bg-white grid grid-cols-4 gap-y-3">
                {menuList.map((item, index) => (
                    <View 
                        key={index} 
                        className="flex flex-col items-center justify-center"
                        onClick={() => handleItemClick(item)}
                    >
                        <Image src={item.icon} className="w-6 h-6" />
                        <Text className="text-xs text-gray-800 mt-2">{item.title}</Text>
                    </View>                    
                ))}
            </View>
        </View>
    )
}

/**
 * 频道页竖向菜单
 * @param menuList 菜单数据
 */
const MenuPage = ({ menuList }: MenuList) => {
    return (
        <View className="flex flex-col space-y-3">
            {menuList.map((item, index) => (
                <View 
                    key={index}
                    className="flex flex-row items-center justify-between bg-white rounded-xl px-3 py-4" 
                    onClick={() => handleItemClick(item)}
                >
                    <View className="flex flex-row items-center">
                        <View className="flex flex-row items-center mr-5">
                            <Image src={item.icon} className="w-12 h-12" />
                        </View>
                        <View className="leading-none">
                            <Text className="text-base font-bold block">{item.title}</Text>
                            <Text className="text-xs text-gray-500">{item.description}</Text>
                        </View>
                    </View>
                    <View className="flex flex-row items-center">
                        <Image src={RightArrowIcon} className="w-5 h-5" />
                    </View>
                </View>                    
            ))}
        </View>
    )
}

export {
    MenuColumn,
    MenuRow,
    MenuPage
};