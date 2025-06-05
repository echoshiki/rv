import { View } from '@tarojs/components';
import MenuScrollBox from '@/components/MenuScrollBox';
import { useRvAllData } from '@/hooks/useRvList';
import { SectionTitle } from '@/components/SectionTitle';
import RvList from '@/components/RvList';
import PageCard from '@/components/PageCard';

const Sale = () => {

    const { rvAllData } = useRvAllData();

    return (
        <View className="bg-black min-h-screen pt-2 pb-5">
            <View>
                <SectionTitle 
                    title="卫航新车" 
                    subtitle="为您展示最新的卫航房车车型" 
                    theme="dark"
                />
                <MenuScrollBox data={rvAllData} />
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