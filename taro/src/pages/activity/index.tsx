import { View } from '@tarojs/components';
import CustomSwiper from '@/components/CustomSwiper';
import CustomTabs from '@/components/CustomTabs';
import { useActivityCategoryList } from '@/hooks/useActivityCategoryList';
import ActivityList from '@/components/ActivityList';
import Card from '@/components/Card';
import { useActivityBanner } from '@/hooks/useBanner';

const Activity = () => {
    const { banners, loading: bannerLoading } = useActivityBanner();
    const { categories, loading: tabsLoading } = useActivityCategoryList();
    const isLoading = bannerLoading || tabsLoading;

    return (
        <View className="bg-gray-100 min-h-screen py-3">
            {/* 活动频道轮播图 */}
            <View className="px-5">
                <CustomSwiper
                    isLoading={isLoading}
                    imageList={banners}
                    imageRatio={1.8}
                    rounded="lg"
                />
            </View>

            {/* 活动频道标签页 */}
            <View className="w-full px-5 mt-2">
                <CustomTabs
                    items={categories}
                    renderTabContent={(item) => (
                        <Card>
                            <ActivityList
                                queryParams={{
                                    filter: {
                                        category_id: item.id
                                    },
                                    limit: 5
                                }}
                                isPullDownRefresh={true}
                                isReachBottomRefresh={true}
                            />
                        </Card>
                    )}
                    isLoading={isLoading}
                />
            </View>
        </View>
    )
}

export default Activity;