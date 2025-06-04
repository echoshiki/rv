import { View } from '@tarojs/components';
import MenuScrollBox from '@/components/MenuScrollBox';
import { useRvList } from '@/hooks/useRvList';
import { SectionTitle } from '@/components/SectionTitle';
import RvList from '@/components/RvList';
import PageCard from '@/components/PageCard';

const Sale = () => {

    const { rvList } = useRvList();
    return (
        <View className="bg-black min-h-screen py-5">
            <View>
                <SectionTitle title="新车速递" subtitle="为您展示最新的卫航房车车型" theme='dark' />
                <MenuScrollBox data={rvList} />
            </View>

            <PageCard 
                title="二手车" 
                subtitle="为您推荐最适合的二手房车"
                className="pt-0"
                theme="dark"
            >
                <RvList used />
            </PageCard>
        </View>
    )
}

export default Sale;