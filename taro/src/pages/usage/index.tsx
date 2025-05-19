import { View, Button, Image } from '@tarojs/components';
import MyCarBg from '@/assets/images/mycar.jpg';
import { MenuRow, MenuFloat } from '@/components/Menu';
import ArticleList from '@/components/ArticleList';
import { SectionTitle } from '@/components/SectionTitle';
import { useMenu } from '@/hooks/useMenu';

/**
 * 用车频道
 * @returns 
 */
const Usage = () => {

    // 菜单数据
    const { rawMenuItems } = useMenu('usage_row_menu');
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
            <View className="w-full relative flex justify-center">
                <Image
                    src={MyCarBg}
                    mode={'widthFix'}
                    className="w-full" 
                />
                <Button className="w-52 absolute bottom-16 border border-solid border-white text-white bg-transparent">
                    添加爱车
                </Button>
            </View>

            {/* 宫格菜单 */}
            <MenuRow menuList={rawMenuItems} />

            <SectionTitle
                title={`用车常识`}
                subtitle={`在使用房车时的小知识`}
                link={'/pages/article/index?code=circuit_use'}
            />

            {/* 用车常识 */}
            <ArticleList 
                queryParams={queryParams} 
                isPullDownRefresh
                isReachBottomRefresh
                changePageTitle
            /> 

            {/* 悬浮导航 */}
            <MenuFloat menuList={floatMenuItems} />
        </View>
    )
}

export default Usage;