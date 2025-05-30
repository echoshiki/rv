import { View } from '@tarojs/components';
import { Tabs, ConfigProvider } from '@nutui/nutui-react-taro';
import { useState, useEffect, ReactNode, useMemo, useRef } from 'react';
import { TabSkeleton } from '@/components/Skeleton';

// 默认的 NutUI Tabs 主题配置
const defaultTabsTheme = {
    '--nutui-tabs-tabpane-padding': '0px',
    '--nutui-tabs-titles-background-color': 'transparent',
    '--nutui-tabs-tab-line-width': '32px',
    '--nutui-tabs-titles-item-active-color': '#000',
    '--nutui-tabs-titles-item-active-font-weight': 'bold',
    '--nutui-tabs-line-bottom': '8%',
    '--nutui-tabs-tab-line-color': '#000',
};

export interface TabItem {
    id: string | number;
    title: string;
    [key: string]: any;
}

interface CustomTabsProps {
    items: TabItem[];
    renderTabContent: (item: TabItem) => ReactNode;
    initialTabId?: string | number;
    isLoading?: boolean;
    theme?: Record<string, string>;
    onTabChange?: (tabId: string | number) => void;
    className?: string;
    enableSwipe?: boolean; // 是否启用滑动手势
    swipeThreshold?: number; // 滑动阈值，默认50px
}

/**
 * 带滑动手势的自定义标签页组件
 * @param items 标签页数据
 * @param renderTabContent 渲染标签页内容函数
 * @param initialTabId 初始选中标签页
 * @param isLoading 是否加载中
 * @param theme 自定义主题
 * @param onTabChange 标签页切换回调
 * @param className 自定义类名
 * @param enableSwipe 是否启用滑动手势，默认true
 * @param swipeThreshold 滑动阈值，默认50px
 */
const CustomTabs = ({
    items,
    renderTabContent,
    initialTabId,
    isLoading = false,
    theme,
    onTabChange,
    className,
    enableSwipe = true,
    swipeThreshold = 50
}: CustomTabsProps) => {
    const [activeTabId, setActiveTabId] = useState<string | number | undefined>(() => {
        if (initialTabId !== undefined && items.some(item => item.id === initialTabId)) {
            return initialTabId;
        }
        return items.length > 0 ? items[0].id : undefined;
    });

    // 滑动相关的状态
    const touchStartX = useRef<number>(0);
    const touchStartY = useRef<number>(0);
    const isSwiping = useRef<boolean>(false);

    useEffect(() => {
        let newActiveIdCandidate: string | number | undefined = undefined;

        const initialTabExists = initialTabId !== undefined && items.some(item => item.id === initialTabId);

        if (initialTabExists) {
            newActiveIdCandidate = initialTabId;
        } else if (items.length > 0) {
            const currentActiveStillExists = activeTabId !== undefined && items.some(item => item.id === activeTabId);
            if (!currentActiveStillExists) {
                newActiveIdCandidate = items[0].id;
            } else {
                newActiveIdCandidate = activeTabId;
            }
        } else {
            newActiveIdCandidate = undefined;
        }
        
        if (newActiveIdCandidate !== activeTabId) {
            setActiveTabId(newActiveIdCandidate);
        }
    }, [items, initialTabId, activeTabId]);

    const handleTabChangeInternal = (newIdValueFromNut: string) => {
        const originalId = items.find(item => String(item.id) === newIdValueFromNut)?.id;

        if (originalId !== undefined) {
            setActiveTabId(originalId);
            if (onTabChange) {
                onTabChange(originalId);
            }
        }
    };

    // 切换到指定标签页
    const switchToTab = (targetId: string | number) => {
        if (targetId !== activeTabId) {
            setActiveTabId(targetId);
            if (onTabChange) {
                onTabChange(targetId);
            }
        }
    };

    // 切换到下一个标签页
    const switchToNextTab = () => {
        if (items.length <= 1) return;
        
        const currentIndex = items.findIndex(item => item.id === activeTabId);
        if (currentIndex >= 0 && currentIndex < items.length - 1) {
            switchToTab(items[currentIndex + 1].id);
        }
    };

    // 切换到上一个标签页
    const switchToPrevTab = () => {
        if (items.length <= 1) return;
        
        const currentIndex = items.findIndex(item => item.id === activeTabId);
        if (currentIndex > 0) {
            switchToTab(items[currentIndex - 1].id);
        }
    };

    // 处理触摸开始
    const handleTouchStart = (e: any) => {
        if (!enableSwipe) return;
        
        const touch = e.touches[0];
        touchStartX.current = touch.clientX;
        touchStartY.current = touch.clientY;
        isSwiping.current = false;
    };

    // 处理触摸移动
    const handleTouchMove = (e: any) => {
        if (!enableSwipe) return;
        
        const touch = e.touches[0];
        const deltaX = Math.abs(touch.clientX - touchStartX.current);
        const deltaY = Math.abs(touch.clientY - touchStartY.current);
        
        // 如果水平滑动距离大于垂直滑动距离，认为是在进行水平滑动
        if (deltaX > deltaY && deltaX > 10) {
            isSwiping.current = true;
            // 阻止默认的滚动行为
            e.preventDefault();
        }
    };

    // 处理触摸结束
    const handleTouchEnd = (e: any) => {
        if (!enableSwipe || !isSwiping.current) return;
        
        const touch = e.changedTouches[0];
        const deltaX = touch.clientX - touchStartX.current;
        const deltaY = Math.abs(touch.clientY - touchStartY.current);
        
        // 确保这是一个水平滑动手势（水平距离大于垂直距离）
        if (Math.abs(deltaX) > deltaY && Math.abs(deltaX) > swipeThreshold) {
            if (deltaX > 0) {
                // 右滑，切换到上一个标签
                switchToPrevTab();
            } else {
                // 左滑，切换到下一个标签
                switchToNextTab();
            }
        }
        
        isSwiping.current = false;
    };

    const finalTheme = { ...defaultTabsTheme, ...theme };

    const nutTabValue = useMemo(() => {
        if (activeTabId !== undefined) return String(activeTabId);
        if (items.length > 0) return String(items[0].id);
        return "";
    }, [activeTabId, items]);

    if (isLoading) {
        return (
            <View className={className}>
                <TabSkeleton />
            </View>
        );
    }

    return (
        <View className={className}>
            {!isLoading && items.length > 0 && activeTabId !== undefined && (
                <ConfigProvider theme={finalTheme}>
                    <Tabs
                        value={nutTabValue}
                        onChange={handleTabChangeInternal}
                    >
                        {items.map((item) => (
                            <Tabs.TabPane
                                value={String(item.id)}
                                title={item.title}
                                key={item.id}
                                className="!bg-gray-100 !py-2"
                            >
                                <View 
                                    style={{ display: 'none' }} 
                                />
                                {item.id === activeTabId ? (
                                    <View
                                        onTouchStart={handleTouchStart}
                                        onTouchMove={handleTouchMove}
                                        onTouchEnd={handleTouchEnd}
                                        style={{ 
                                            minHeight: '200px', // 确保有足够的触摸区域
                                            touchAction: isSwiping.current ? 'none' : 'auto'
                                        }}
                                    >
                                        {renderTabContent(item)}
                                    </View>
                                ) : null}
                            </Tabs.TabPane>
                        ))}
                    </Tabs>
                </ConfigProvider>
            )}
        </View>
    );
};

export default CustomTabs;