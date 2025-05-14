import { View, Text } from "@tarojs/components";
import Taro from "@tarojs/taro";
import { ArticleList } from '@/components/ArticleList';
import { useArticleList } from '@/hooks/useArticleList';
import { useEffect } from "react";

const Articles = () => {
    const { router } = Taro.getCurrentInstance();
    const code = router?.params?.code;

    useEffect(() => {
        if (!code) {
            Taro.navigateTo({
                url: '/pages/404/index'
            })
            return;
        }
    }, [code]);

    const { articleList } = useArticleList({
        filter: {
            category_code: code
        }
    });

    return (
        <View className="bg-gray-100 min-h-screen py-5">
            {articleList.length > 0 ? (
                <ArticleList list={articleList} />
            ) : (
                <View className="flex justify-center items-center h-full">
                    <Text>该分类下还没有文章</Text>
                </View>
            )}
        </View>
    );
}

export default Articles;