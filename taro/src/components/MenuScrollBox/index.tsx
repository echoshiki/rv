import { useState, useEffect, useRef } from 'react';
import { RvItem } from '@/types/ui';
import { View, Text, ScrollView } from '@tarojs/components';
import AspectRatioImage from '@/components/AspectRatioImage';

const MenuScrollBox = ({ data }: { data: RvItem[] }) => {
    // 存储当前高亮菜单项 ID
    const [activeId, setActiveId] = useState<string>('');
    // 用于控制滚动到右侧指定 ID 的位置
    const [scrollIntoView, setScrollIntoView] = useState<string>('');
    // 用于判断是否是菜单项触发的滚动
    const isScrollingFromMenu = useRef(false);

    // 处理左侧菜单的点击
    const handleMenuClick = (id: string) => {
        isScrollingFromMenu.current = true; // 设置标志，表示由菜单点击触发
        setActiveId(id); // 更新激活状态，点亮左侧菜单
        setScrollIntoView(`item-${id}`); // 设置右侧滚动到对应元素

        // 延迟重置标志位，避免在滚动动画期间被 handleContentScroll 再次触发
        setTimeout(() => {
            isScrollingFromMenu.current = false;
        }, 500);
    };

    // 处理右侧内容区域的滑动
    const handleContentScroll = (e: any) => {
        // 如果是左侧菜单点击触发的滚动，则不处理
        if (isScrollingFromMenu.current) return;

        const { scrollTop } = e.detail; // 获取滚动条距离顶部的距离

        // 根据滚动位置估算当前应该激活的菜单项
        // 这里简化处理，实际项目中可能需要更精确的计算
        const itemHeight = 320; // 假设每个商品卡片高度大约为 300px
        const itemsPerRow = 1; // 假设每行1个商品
        const currentIndex = Math.floor(scrollTop / (itemHeight / itemsPerRow));

        if (data[currentIndex] && data[currentIndex].id !== activeId) {
            setActiveId(data[currentIndex].id); // 更新点亮激活的菜单项
        }
    };

    // 初始化默认激活第一个菜单
    useEffect(() => {
        if (data.length > 0 && !activeId) {
            setActiveId(data[0].id);
        }
    }, [data, activeId]);

    return (
        <View className="flex h-96 bg-black">
            {/* 左侧菜单 */}
            <View className="w-24">
                <ScrollView
                    scrollY
                    className="h-full"
                >
                    <View className="py-4 px-2">
                        <View className="space-y-2">
                            {data.map((item) => (
                                <View key={item.id} className="mb-2">
                                    <View
                                        onClick={() => handleMenuClick(item.id)}
                                        className={`w-full py-2 px-4 rounded-lg transition-colors duration-200 ${activeId === item.id
                                                ? 'bg-[#3c3c3c] text-white'
                                                : 'text-[#6c6c6c] hover:bg-gray-100'
                                            }`}
                                    >
                                        <Text className="font-bold">{item.name}</Text>
                                    </View>
                                </View>
                            ))}
                        </View>
                    </View>
                </ScrollView>
            </View>

            {/* 右侧内容区域 */}
            <View className="flex-1">
                <ScrollView
                    scrollY
                    scrollIntoView={scrollIntoView}
                    onScroll={handleContentScroll}
                    className="h-full"
                    scrollWithAnimation
                >
                    <View className="flex flex-col space-y-8 p-2 pb-5">
                        {data.map((item) => (
                            <View
                                key={item.id}
                                id={`item-${item.id}`}
                                className="bg-white rounded-lg shadow-md h-[22.5rem] relative overflow-hidden"
                            >
                                <AspectRatioImage
                                    src={item.cover}
                                    ratio={1.5}
                                    rounded="lg"
                                />

                                <View className="absolute right-5 bottom-5 text-white flex flex-col">
                                    <View>
                                        <Text className="font-light text-base">¥{item.price} 起</Text>
                                    </View>
                                    <View className="text-right">
                                        <Text className="font-bold text-xs">点击查看详情</Text>
                                    </View>
                                </View>
                            </View>
                        ))}
                    </View>
                </ScrollView>
            </View>
        </View>
    )
};

// 默认导出组件实例
export default MenuScrollBox;