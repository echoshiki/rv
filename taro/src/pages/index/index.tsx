import { View } from '@tarojs/components'
import CustomSwiper from '@/components/CustomSwiper';
import { useBanner } from '@/hooks/useBanner';
import Loading from '@/components/Loading';

const Index = () => {

	const { banners, loading } = useBanner('home');

	return (
		<View>
			{loading ? <Loading /> : (
				<CustomSwiper 
					imageList={banners} 
					imageRatio={2}
					useScreenWidth={true}
				/>
			)}
		</View>
	)
}

export default Index;