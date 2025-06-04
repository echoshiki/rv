import { View } from '@tarojs/components';
import CustomSwiper from '@/components/CustomSwiper';
import CustomTabs from '@/components/CustomTabs';
import { useActivityBanner } from '@/hooks/useBanner';
import { useMenu } from '@/hooks/useMenu';
import { useMemo } from 'react';
import { Category } from '@/types/ui';
import CustomTabPanel from '@/components/CustomTabs/CustomTabPanel';

const Activity = () => {
    const { banners, loading: bannerLoading } = useActivityBanner();
    const { rawMenuItems: tabMenuItems, loading: tabsLoading } = useMenu('activity_tab_menu');

    // 格式化菜单项为分类项
    const categories = useMemo(() => tabMenuItems.map(item => ({
        id: item.link,
        title: item.title,
        description: '',
        code: '',
        channel: item.link.split('|')[1]
    })), [tabMenuItems]);

    // 根据分类渲染对应的列表UI
	const renderTabPanel = (item: Category) => {
		return <CustomTabPanel item={item} />
	}

    // 合并载入状态
    const isLoading = bannerLoading || tabsLoading;

    return (
        <View className="bg-gray-100 min-h-screen py-3">
            {/* 活动频道轮播图 */}
            <View className="px-5">
                <CustomSwiper
                    isLoading={isLoading}
                    imageList={banners}
                    imageRatio={1.8}
                    rounded="lg"
                />
            </View>

            {/* 活动频道标签页 */}
            <View className="w-full px-5 mt-2">
                <CustomTabs
                    theme={{
                        '--nutui-tabs-titles-font-size': '18px',
                    }}
                    items={categories}
                    renderTabContent={renderTabPanel}
                    isLoading={isLoading}
                />
            </View>
        </View>
    )
}

export default Activity;