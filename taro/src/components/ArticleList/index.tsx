import { View, Text } from '@tarojs/components';
import DefaultCover from '@/assets/images/cover.jpg';
import { ArticleItem as ArticleItemProps } from '@/types/ui';
import { ArticleListQueryParams } from '@/types/query';
import { mapsTo } from '@/utils/common';
import { useArticleList } from '@/hooks/useArticleList';
import Taro from '@tarojs/taro';
import AspectRatioImage from '@/components/AspectRatioImage';
import { ArticleListSkeleton } from '@/components/Skeleton';

const ArticleItem = ({ item }: { item: ArticleItemProps }) => {
    return (
        <View
            className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed"
            onClick={() => mapsTo(`/pages/article/detail/index?id=${item.id}`)}
        >
            <View className="w-24">
                <AspectRatioImage
                    src={item.cover ? item.cover : DefaultCover}
                    ratio={.8}
                />
            </View>
            <View className="flex-1 flex flex-col space-y-2">
                <View className="text-sm text-ellipsis overflow-hidden line-clamp-2">
                    <Text>{item.title}</Text>
                </View>
                <View>
                    <View>
                        <Text className="text-gray-400 text-xs">
                            {item.published_at}
                        </Text>
                    </View>
                </View>
            </View>
        </View>
    )
}

interface ArticleListProps {
    queryParams: ArticleListQueryParams;
    isPullDownRefresh?: boolean;
    isReachBottomRefresh?: boolean;
    changePageTitle?: boolean;
}

const ArticleList = ({ 
    queryParams,
    isPullDownRefresh = false,
    isReachBottomRefresh = false,
    changePageTitle = false
}: ArticleListProps) => {
    const {
        articleList,
        loading,
        refresh,
        loadMore,
        hasMore,
    } = useArticleList(queryParams, {
        // 分类加载完成后的回调，设置页面标题
        onCategoryLoaded: changePageTitle ? (category) => {
            if (category?.title) {
                Taro.setNavigationBarTitle({
                    title: category.title
                });
            }
        } : undefined
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
    if (isPullDownRefresh) {
        Taro.usePullDownRefresh(handlePullDownRefresh);
    }

    if (isReachBottomRefresh) {
        Taro.useReachBottom(handleReachBottom);
    }

    if (loading) {
        return (
            <ArticleListSkeleton />
        )
    }

    return (
        <View>
            {articleList.map(item => (
                <ArticleItem key={item.id} item={item} />
            ))}

            {articleList.length === 0 && !loading && (
                <View className="flex justify-center items-center h-64">
                    <Text className="text-gray-500">该分类下还没有文章</Text>
                </View>
            )}

            {!hasMore && articleList.length > 0 && (
                <View className="text-center text-gray-500 text-sm py-4">
                    没有更多数据了
                </View>
            )}
        </View>
    )
}

export default ArticleList;