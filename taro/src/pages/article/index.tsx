import { View, Text } from "@tarojs/components";
import Taro from "@tarojs/taro";
import { ArticleList } from '@/components/ArticleList';
import { useArticleList } from '@/hooks/useArticleList';
import { useEffect, useMemo, useCallback } from "react";
import { type ArticleListQueryParams } from "@/api/article";

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
            params.filter = {
                category_code: router.params.code
            }
        }

        if (router?.params?.id) {
            params.filter = {
                category_id: router.params.id
            }
        }

        return params;
    }, [router]);

    useEffect(() => {
        if (!queryParams.filter?.category_id && !queryParams.filter?.category_code) {
            Taro.navigateTo({
                url: '/pages/404/index'
            })
            return;
        }
    }, [queryParams]);

    const { 
        articleList, 
        articleCategory, 
        loading,
        refresh,
        loadMore,
        hasMore
    } = useArticleList(queryParams);

    const onPullDownRefresh = useCallback(async () => {
        try {
            await refresh();
        } finally {
            // 停止下拉刷新动画
            Taro.stopPullDownRefresh();
        }
    }, [refresh]);

    // 处理触底加载
    const onReachBottom = useCallback(async () => {
        if (hasMore && !loading) {
            await loadMore();
        }
    }, [hasMore, loading, loadMore]);

    // 监听下拉刷新事件
    Taro.usePullDownRefresh(() => {
        Taro.showToast({
            title: '刷新中',
            icon: 'loading',
            duration: 1000
        });
        onPullDownRefresh();
    });

    // 监听触底事件
    Taro.useReachBottom(() => {
        Taro.showToast({
            title: '加载中',
            icon: 'loading',
            duration: 1000
        });
        onReachBottom();
    });

    useEffect(() => {
        // 修改页面标题
        if (articleCategory && articleCategory.title) {
            Taro.setNavigationBarTitle({
                title: articleCategory.title
            })
        }
    }, [articleCategory, loading, queryParams.filter]);

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