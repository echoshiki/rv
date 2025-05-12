import { View, Text, Button, Image } from '@tarojs/components';
import MyCarBg from '@/assets/images/mycar.jpg';
import { MenuRow } from '@/components/Menu';
import { ArticleList } from '@/components/ArticleList';
import { usageRowMenu, articleList } from '@/config/menu.config';

/**
 * 
 * @returns 
 */
const Usage = () => {
    return (
        <View className="bg-gray-100 min-h-screen">
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

            {/* 用车常识 */}
            <ArticleList 
                title="用车常识"
                subtitle="一些房车使用时候的日常小知识"
                list={articleList}
            /> 
        </View>
    )
}

export default Usage;