import { View } from '@tarojs/components';
import MyCarSection from '@/components/MyCarSection';
import { MenuRow, MenuFloat } from '@/components/Menu';
import ArticleList from '@/components/ArticleList';
import { useMenu } from '@/hooks/useMenu';
import PageCard from '@/components/PageCard';

/**
 * 用车频道
 * @returns 
 */
const Usage = () => {

    // 菜单数据
    const { rawMenuItems, loading: rowLoading } = useMenu('usage_row_menu');
    const { rawMenuItems: floatMenuItems } = useMenu('usage_float_menu');

    // 用车常识列表
    const queryParams = {
        filter: {
            category_code: 'common_sense'
        }
    };

    return (
        <View className="bg-gray-100 min-h-screen pb-5">
            {/* 添加爱车 */}
            <MyCarSection />

            {/* 宫格菜单 */}
            <View className="px-5 mt-4">
                <MenuRow menuList={rawMenuItems} isLoading={rowLoading} />
            </View>
            
            {/* 用车常识 */}
            <PageCard 
                title="使用说明" 
                subtitle="在使用房车时的小知识" 
                link="/pages/article/index?code=common_sense"
                className="pt-0"
            >
                <ArticleList
                    queryParams={queryParams}
                />
            </PageCard>

            {/* 悬浮导航 */}
            <MenuFloat menuList={floatMenuItems} />
        </View>
    )
}

export default Usage;