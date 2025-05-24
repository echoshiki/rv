import { View, Text } from '@tarojs/components'
import CustomSwiper from '@/components/CustomSwiper';
import { useBanner } from '@/hooks/useBanner';
import Loading from '@/components/Loading';
import { useMenu } from '@/hooks/useMenu';
import { MenuMatrix } from '@/components/Menu';
import { useMemo } from 'react';
import { Category } from '@/types/ui';
import CustomTabs from '@/components/CustomTabs';
import ArticleList from '@/components/ArticleList';
import ActivityList from '@/components/ActivityList';

const Index = () => {
	const { banners, loading: bannerLoading } = useBanner('home');
	const { rawMenuItems: matrixMenuItems } = useMenu('home_matrix_menu');
	const { rawMenuItems: tabMenuItems, loading: tabLoading } = useMenu('home_tab_menu');
	
	const tabCategories = useMemo(() => tabMenuItems.map(item => ({
		id: item.link,
		title: item.title,
		description: '',
		code: '',
		channel: item.link.split('|')[1]
	})), [tabMenuItems]);

	const renderTabPanel = (item: Category) => {
		const category_id = item.id.split('|')[0];
		const channel = item.id.split('|')[1];
		const queryParams = {
			filter: {
				category_id: category_id
			},
			limit: 5
		};

		if (channel === 'article') {
			return <ArticleList queryParams={queryParams} />
		}

		if (channel === 'activity') {
			return <ActivityList queryParams={queryParams} />
		}

		return (
			<View className="flex justify-center items-center h-64">
				<Text>暂无数据</Text>
			</View>
		);
	}
	
	return (
		<View className='bg-gray-100 min-h-screen pb-10'>
			{/* 首页轮播图 */}
			<View>
				<CustomSwiper 
					imageList={banners} 
					imageRatio={1.8}
					useScreenWidth={true}
				/>
				{bannerLoading && <Loading />}
			</View>

			{/* 图片导航矩阵 */}
			<View className="px-5 mt-3">
				<MenuMatrix menuList={matrixMenuItems} />
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