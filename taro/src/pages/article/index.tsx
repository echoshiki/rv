import { View, Text } from "@tarojs/components";
import Taro from "@tarojs/taro";
import { ArticleList } from '@/components/ArticleList';
import { useArticleList } from '@/hooks/useArticleList';
import { useMemo } from "react";
import { type ArticleListQueryParams } from "@/api/article";
import Loading from "@/components/Loading";

const Articles = () => {
    const { router } = Taro.getCurrentInstance();

    // 构造查询参数对象
    const queryParams = useMemo(() => {
        // 初始化查询参数
        const params: ArticleListQueryParams = {
            filter: {},
            page: 1,
            limit: 8
        };

        if (router?.params?.code) {
            params.filter.category_code = router.params.code
        }

        if (router?.params?.id) {
            params.filter.category_id = router.params.id
        }

        return params;
    }, [router?.params?.code, router?.params?.id]);

    // 检查是否有必要的查询参数
    const hasValidParams = useMemo(() => {
        return !!(queryParams.filter?.category_id || queryParams.filter?.category_code);
    }, [queryParams.filter?.category_id, queryParams.filter?.category_code]);

    const { 
        articleList,
        loading,
        refresh,
        loadMore,
        hasMore,
    } = useArticleList(queryParams, {
        // 分类加载完成后的回调，设置页面标题
        onCategoryLoaded: (category) => {
            if (category?.title) {
                Taro.setNavigationBarTitle({
                    title: category.title
                });
            }
        }
    });

    Taro.useDidShow(() => {
        if (!hasValidParams) {
            Taro.navigateTo({
                url: '/pages/404/index'
            });
        }
    });

    // 处理下拉刷新
    const handlePullDownRefresh = async () => {
        console.log('下拉刷新');
        try {
            await refresh();
        } finally {
            Taro.stopPullDownRefresh();
        }
    };

    // 处理触底加载
    const handleReachBottom = async () => {
        console.log('触底加载');
        if (hasMore && !loading) {
            await loadMore();
        }
    };

    // 注册下拉刷新与触底加载
    Taro.usePullDownRefresh(handlePullDownRefresh);
    Taro.useReachBottom(handleReachBottom);

    const renderContent = () => {
        if (articleList.length === 0 && !loading) {
            return (
                <View className="flex justify-center items-center h-64">
                    <Text className="text-gray-500">该分类下还没有文章</Text>
                </View>
            );
        }
        return <ArticleList list={articleList} />;
    };

    return (
        <View className="bg-gray-100 min-h-screen py-5">
            {/* 渲染内容 */}
            {renderContent()}

            {/* 加载状态展示 */}
            {loading && (
                <View className="flex justify-center py-4">
                    <Loading />
                </View>
            )}

            {/* 加载完成提示 */}
            {!hasMore && articleList.length > 0 && (
                <View className="text-center text-gray-500 text-sm py-4">
                    没有更多数据了
                </View>
            )}
        </View>
    );
}

export default Articles;