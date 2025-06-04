import { View } from '@tarojs/components'
import CustomSwiper from '@/components/CustomSwiper';
import { useBanner } from '@/hooks/useBanner';
import { useMenu } from '@/hooks/useMenu';
import { MenuMatrix } from '@/components/Menu';
import { useMemo } from 'react';
import { Category } from '@/types/ui';
import CustomTabs from '@/components/CustomTabs';
import CustomTabPanel from '@/components/CustomTabs/CustomTabPanel';

const Index = () => {
	const { banners, loading: bannerLoading } = useBanner('home');
	const { rawMenuItems: matrixMenuItems, loading: menuLoading } = useMenu('home_matrix_menu');
	const { rawMenuItems: tabMenuItems, loading: tabLoading } = useMenu('home_tab_menu');
	
	const tabCategories = useMemo(() => tabMenuItems.map(item => ({
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
	
	return (
		<View className='bg-gray-100 min-h-screen pb-10'>
			{/* 首页轮播图 */}
			<View>
				<CustomSwiper 
					imageList={banners} 
					imageRatio={1.8}
					useScreenWidth={true}
					isLoading={bannerLoading}
				/>
			</View>

			{/* 图片导航矩阵 */}
			<View className="px-5 mt-3">
				<MenuMatrix menuList={matrixMenuItems} isLoading={menuLoading}/>
			</View>

			{/* 首页列表模块 */}
			<View className="w-full px-5 mt-2">
                <CustomTabs 
                    items={tabCategories}
                    renderTabContent={renderTabPanel}
					isLoading={tabLoading}
                />
            </View>
		</View>
	)
}

export default Index;