import { View } from '@tarojs/components';
import MenuScrollBox from '@/components/MenuScrollBox';
import { useRvList } from '@/hooks/useRvList';
import { SectionTitle } from '@/components/SectionTitle';

const Sale = () => {

    const { rvList } = useRvList();

    return (
        <View className="bg-black min-h-screen py-5">
            <View>
                <SectionTitle title="新车速递" subtitle="为您展示最新的卫航房车车型" theme='dark' />
                <MenuScrollBox data={rvList} />
            </View>

            <View>
                <SectionTitle title="二手车选购" subtitle="为您推荐最适合的二手房车" theme='dark' />

            </View>
        </View>
    )
}

export default Sale;