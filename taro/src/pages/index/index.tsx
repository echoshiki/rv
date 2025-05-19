import { View } from '@tarojs/components'
import CustomSwiper from '@/components/CustomSwiper';
import { useBanner } from '@/hooks/useBanner';
import Loading from '@/components/Loading';
import { useMenu } from '@/hooks/useMenu';
import { MenuMatrix, MenuTab } from '@/components/Menu';

const Index = () => {

	const { banners, loading: bannerLoading } = useBanner('home');
	const { rawMenuItems: matrixMenuItems } = useMenu('home_matrix_menu');
	const { rawMenuItems: tabMenuItems } = useMenu('home_tab_menu');

	return (
		<View>
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
			<View>
				<MenuTab menuList={tabMenuItems} />
			</View>

		</View>
	)
}

export default Index;