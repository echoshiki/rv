import { useState, useEffect, useRef } from 'react';
import { RvAllData } from '@/types/ui';
import { View, Text, ScrollView } from '@tarojs/components';
import AspectRatioImage from '@/components/AspectRatioImage';
import { mapsTo } from '@/utils/common';
import { getWindowInfo } from '@tarojs/taro';

const MenuScrollBox = ({ data }: { data: RvAllData[] }) => {
    // 存储当前高亮菜单项 ID
    const [activeCategoryId, setActiveCategoryId] = useState<string>('');
    // 用于控制滚动到右侧指定 ID 的位置
    const [scrollIntoView, setScrollIntoView] = useState<string>('');
    // 用于判断是否是菜单项触发的滚动
    const isScrollingFromMenu = useRef(false);
    // 存储容器高度
    const [containerHeight, setContainerHeight] = useState<number>(0);

    // 处理左侧菜单的点击
    const handleMenuClick = (categoryId: string) => {
        isScrollingFromMenu.current = true; // 设置标志，表示由菜单点击触发
        setActiveCategoryId(categoryId); // 更新激活状态，点亮左侧菜单
        setScrollIntoView(`category-${categoryId}`); // 设置右侧滚动到对应元素

        // 延迟重置标志位，避免在滚动动画期间被 handleContentScroll 再次触发
        setTimeout(() => {
            isScrollingFromMenu.current = false;
        }, 500);
    };

    // 获取系统信息，用于rem转px的计算和动态高度设置
    const getSystemInfo = () => {
        try {
            const systemInfo = getWindowInfo();
            const screenWidth = systemInfo.screenWidth;
            const screenHeight = systemInfo.screenHeight;
            const windowHeight = systemInfo.windowHeight;
            
            // 通常设计稿按750px宽度，1rem = screenWidth/20 px
            const remToPx = screenWidth / 20;
            
            // 动态计算容器高度，确保在不同屏幕尺寸下都有合适的高度
            // 使用可视区域高度的一定比例，避免固定高度带来的问题
            const dynamicHeight = Math.min(windowHeight * 0.65, screenHeight * 0.5);
            setContainerHeight(dynamicHeight);
            
            return remToPx;
        } catch (error) {
            // 如果获取失败，使用默认值（以iPhone为基准）
            setContainerHeight(520); // 默认高度 26rem * 20 = 520px
            return 18.75; // 375/20 = 18.75px per rem
        }
    };

    // 处理右侧内容区域的滑动
    const handleContentScroll = (e: any) => {
        // 如果是左侧菜单点击触发的滚动，则不处理
        if (isScrollingFromMenu.current) return;

        const { scrollTop } = e.detail; // 获取滚动条距离顶部的距离

        // 计算当前滚动位置对应的分类
        let currentCategoryId = '';
        let accumulatedHeight = 0;

        // 获取rem转px的比例
        const remToPx = getSystemInfo();

        for (const item of data) {
            // 使用相对单位计算，让高度更加灵活
            const productCardHeight = containerHeight * 0.98; // 占容器高度的40%
            const productSpacing = remToPx * 1.25;
            const singleProductHeight = productCardHeight + productSpacing;

            // 计算出右侧整个分类数据的高度
            const categoryHeight = item.rvs.list.length * singleProductHeight;
            // 判断当前滚动位置是否在当前分类范围内
            if (scrollTop >= accumulatedHeight && scrollTop < accumulatedHeight + categoryHeight) {
                currentCategoryId = item.category.id;
                break;
            }
            accumulatedHeight += categoryHeight;
        }

        if (currentCategoryId && currentCategoryId !== activeCategoryId) {
            setActiveCategoryId(currentCategoryId);
        }
    };

    // 初始化默认激活第一个菜单和获取系统信息
    useEffect(() => {
        getSystemInfo(); // 初始化时获取系统信息
        if (data.length > 0 && !activeCategoryId) {
            setActiveCategoryId(data[0].category.id);
        }
    }, [data, activeCategoryId]);

    // 动态计算容器样式
    const containerStyle = {
        height: containerHeight ? `${containerHeight}px` : '26rem'
    };

    // 动态计算产品卡片高度
    const getProductCardHeight = () => {
        return containerHeight ? containerHeight * 0.98 : '10.4rem'; // 默认26rem * 0.4
    };

    return (
        <View className="flex bg-black" style={containerStyle}>
            {/* 左侧菜单 */}
            <View className="w-[7.2rem] pl-2 pr-1 flex-shrink-0">
                <ScrollView
                    scrollY
                    className="h-full"
                    showScrollbar={false}
                >
                    <View className="">
                        {data.map((item) => (
                            <View
                                key={item.category.id}
                                className={`w-full py-2 px-2 mb-[2px] rounded-md transition-colors duration-200 ${activeCategoryId === item.category.id
                                    ? 'bg-[#3c3c3c] text-white'
                                    : 'text-[#6c6c6c] hover:bg-gray-100'
                                    }`}
                                onClick={() => handleMenuClick(item.category.id)}
                            >
                                <Text className="font-bold text-sm leading-tight">
                                    {item.category.title}
                                </Text>
                            </View>
                        ))}
                    </View>
                </ScrollView>
            </View>

            {/* 右侧内容区域 */}
            <View className="flex-1 min-w-0">
                <ScrollView
                    scrollY
                    scrollIntoView={scrollIntoView}
                    onScroll={handleContentScroll}
                    className="h-full"
                    scrollWithAnimation
                    showScrollbar={false}
                    enhanced={true}
                    bounces={false}
                >
                    <View className="flex flex-col space-y-5 px-1 pb-5">
                        {data.map((item) => (
                            <View
                                key={item.category.id}
                                id={`category-${item.category.id}`}
                                className="flex flex-col space-y-5"
                            >
                                {item.rvs.list.map((rv) => (
                                    <View
                                        key={rv.id}
                                        onClick={() => mapsTo(`/pages/sale/detail/index?id=${rv.id}`)}
                                        className="bg-white rounded-md shadow-md relative overflow-hidden"
                                        style={{ 
                                            height: typeof getProductCardHeight() === 'string' 
                                                ? getProductCardHeight() 
                                                : `${getProductCardHeight()}px` 
                                        }}
                                    >
                                        <AspectRatioImage
                                            src={rv.cover}
                                            ratio={1.8}
                                        />
                                        <View className="absolute right-5 bottom-5 text-white flex flex-row justify-end items-center space-x-2 opacity-90">
                                            <View>
                                                <Text className="font-semibold text-lg opacity-85">{rv.name}</Text>
                                            </View>
                                            <View className="w-[1px] h-3 bg-gray-300"></View>
                                            <View className="flex flex-col items-end font-light text-xs">
                                                <Text><Text className="font-semibold">¥{rv.price}</Text> 起</Text>
                                                <Text>点击查看详情</Text>
                                            </View>
                                        </View>
                                    </View>
                                ))}
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