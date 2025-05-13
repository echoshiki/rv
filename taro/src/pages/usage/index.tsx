import { View, Button, Image } from '@tarojs/components';
import MyCarBg from '@/assets/images/mycar.jpg';
import { MenuRow } from '@/components/Menu';
import { ArticleList } from '@/components/ArticleList';
import { SectionTitle } from '@/components/SectionTitle';
import { useMenu } from '@/hooks/useMenu';
import { transformApiMenuItem } from '@/utils/apiTransformers';
import { articleList } from '@/config/menu.config';

/**
 * 用车频道
 * @returns 
 */
const Usage = () => {

    // 原始菜单数据
    const { rawMenuItems } = useMenu('usage_row_menu');

    // 菜单数据转换
    const usageRowMenu = rawMenuItems.map(transformApiMenuItem);

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
            <MenuRow menuList={usageRowMenu} />

            <SectionTitle
                title={`用车常识`}
                subtitle={`在使用房车时的小知识`}
            />

            {/* 用车常识 */}
            <ArticleList list={articleList} /> 
        </View>
    )
}

export default Usage;