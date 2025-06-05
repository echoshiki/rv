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
                    title="新车发布" 
                    subtitle="WEIHANG RV" 
                    theme="dark"
                    type="row"
                />
                <MenuScrollBox data={rvAllData} />
            </View>

            <PageCard
                title="二手车"
                subtitle="为您推荐最适合的二手房车"
                className="pt-0"
                theme="dark"
                type="row"
            >
                <RvList used />
            </PageCard>
        </View>
    )
}

export default Sale;