import { View } from '@tarojs/components';
import { Tabs, ConfigProvider } from '@nutui/nutui-react-taro';
import { useState, useEffect, ReactNode, useMemo } from 'react';
import Loading from '@/components/Loading'; // 确保路径正确

// 默认的 NutUI Tabs 主题配置 (请确保您的主题配置在这里)
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
    isLoading?: boolean; // 用于指示分类数据本身是否在加载
    theme?: Record<string, string>;
    onTabChange?: (tabId: string | number) => void;
    className?: string;
}

const CustomTabs = ({
    items,
    renderTabContent,
    initialTabId,
    isLoading = false,
    theme,
    onTabChange,
    className
}: CustomTabsProps) => {
    const [activeTabId, setActiveTabId] = useState<string | number | undefined>(() => {
        // 初始化 activeTabId
        if (initialTabId !== undefined && items.some(item => item.id === initialTabId)) {
            return initialTabId;
        }
        return items.length > 0 ? items[0].id : undefined;
    });

    useEffect(() => {
        // 当 items 或 initialTabId 变化时，同步 activeTabId
        let newActiveIdCandidate: string | number | undefined = undefined;

        const initialTabExists = initialTabId !== undefined && items.some(item => item.id === initialTabId);

        if (initialTabExists) {
            newActiveIdCandidate = initialTabId;
        } else if (items.length > 0) {
            const currentActiveStillExists = activeTabId !== undefined && items.some(item => item.id === activeTabId);
            if (!currentActiveStillExists) { // 如果当前 activeId 无效了（比如items变了），或者从未设置过
                newActiveIdCandidate = items[0].id;
            } else {
                newActiveIdCandidate = activeTabId; // 保持当前，如果它仍然有效且没有 initialTabId 覆盖
            }
        } else {
            newActiveIdCandidate = undefined; // 没有 items，就没有 active tab
        }
        
        // 只有当计算出的 newActiveIdCandidate 和当前的 activeTabId 不同时才更新，避免不必要的重渲染
        if (newActiveIdCandidate !== activeTabId) {
            setActiveTabId(newActiveIdCandidate);
        }

    }, [items, initialTabId, activeTabId]); // activeTabId 加入依赖，以便在它自身变无效时能重新计算


    const handleTabChangeInternal = (newIdValueFromNut: string) => {
        // 从 items 中找到原始的 id (可能是 number 类型)
        const originalId = items.find(item => String(item.id) === newIdValueFromNut)?.id;

        if (originalId !== undefined) {
            setActiveTabId(originalId);
            if (onTabChange) {
                onTabChange(originalId);
            }
        }
    };

    const finalTheme = { ...defaultTabsTheme, ...theme };

    // NutUI Tabs 的 value 属性通常需要字符串，并且不能为空字符串（如果items为空时）
    // 使用 useMemo 避免不必要的计算
    const nutTabValue = useMemo(() => {
        if (activeTabId !== undefined) return String(activeTabId);
        if (items.length > 0) return String(items[0].id);
        return ""; // 或者一个在 items 中不存在的虚拟值，如果 NutUI 不接受空字符串
    }, [activeTabId, items]);

    if (isLoading && items.length === 0) { // 分类数据加载中
        return <Loading />;
    }

    if (!items || items.length === 0) {
        return null; // 没有分类数据则不渲染 Tabs
    }

    return (
        <View className={className}>
            {/* isLoading 用于表示分类列表本身的加载状态 */}
            {isLoading && <Loading />}
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
                                <View style={{ display: 'none' }} />
                                {item.id === activeTabId ? renderTabContent(item) : null}
                            </Tabs.TabPane>
                        ))}
                    </Tabs>
                </ConfigProvider>
            )}
        </View>
    );
};

export default CustomTabs;