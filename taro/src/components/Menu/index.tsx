import { View, Text, Image } from "@tarojs/components";
import { mapsTo } from "@/utils/common";
import { MenuItem, MenuList } from '@/types/ui';
import RightArrowIcon from '@/assets/icons/right-arrow.svg';
import { FixedNav, FixedNavItem } from '@nutui/nutui-react-taro';
import { useState } from 'react';
import Card from '@/components/Card';
import AspectRatioImage from '@/components/AspectRatioImage';
import { MenuMatrixSkeleton } from '@/components/Skeleton';

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
        <Card className="pt-3">
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
        </Card>
    )
}

/**
 * 横向带图标的菜单
 * @param menuList 菜单数据
 */
const MenuRow = ({ menuList }: MenuList) => {
    return (
        <Card className="!px-3">
            <View className="grid grid-cols-4 gap-y-3">
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
        </Card>
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
                    className="flex flex-row items-center justify-between bg-white rounded-md px-3 py-4"
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

/**
 * 悬浮菜单
 * @param menuList 菜单数据
 */
const MenuFloat = ({ menuList }: MenuList) => {
    // 将菜单数据格式化成 NutUI 需要的格式
    const fixedMenu = menuList.map((item, index) => {
        return {
            ...item,
            id: index,
            text: item.title
        }
    })

    // 控制折叠
    const [visible, setVisible] = useState(false);
    const change = (value: boolean) => {
        setVisible(value)
    }

    // 点选触发
    const selected = (
        item: FixedNavItem,
        _event: React.MouseEvent<Element, MouseEvent>
    ) => {
        const menuItem = menuList[item.id as number];
        handleItemClick(menuItem);
    }

    return (
        <View>
            <FixedNav
                list={fixedMenu}
                activeText="贴心服务"
                overlay
                position={{ bottom: '50px' }}
                onChange={change}
                visible={visible}
                onSelect={selected}
            />
        </View>
    )
}

/**
 * 首页图片矩阵菜单
 * @param menuList 菜单数据
 */
const MenuMatrix = ({ menuList, isLoading }: MenuList) => {

    // 渲染左侧矩形图片菜单
    const renderLeftMenu = (item: MenuItem) => {
        return (
            <View onClick={() => handleItemClick(item)}>
                <AspectRatioImage src={item.icon} ratio={1}>
                    <View className="absolute w-full h-full flex justify-center items-center">
                        <Text className="text-xl text-white text-opacity-75 font-semibold">{item.title}</Text>
                    </View>
                </AspectRatioImage>
            </View>
        )
    }

    // 渲染右侧长方形菜单
    const renderRightMenu = (item: MenuItem) => {
        return (
            <View onClick={() => handleItemClick(item)}>
                <AspectRatioImage src={item.icon} ratio={.48}>
                    <View className="absolute w-full h-full flex items-center justify-center">
                        <Text className="text-base text-white text-opacity-75 font-bold">{item.title}</Text>
                    </View>
                </AspectRatioImage>
            </View>
        )
    }

    if (isLoading) {
        return (
            <MenuMatrixSkeleton />
        )
    }

    return (
        <>
            <View className="flex flex-nowrap space-x-2">
                <View className="w-1/2">
                    {menuList[0] && renderLeftMenu(menuList[0])}
                </View>
                <View className="w-1/2 flex flex-col justify-between">
                    {menuList[1] && renderRightMenu(menuList[1])}
                    {menuList[2] && renderRightMenu(menuList[2])}
                </View>
            </View>
        </>   
    )
}

export {
    MenuColumn,
    MenuRow,
    MenuPage,
    MenuFloat,
    MenuMatrix
};