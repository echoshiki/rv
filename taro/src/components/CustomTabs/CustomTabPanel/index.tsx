import { View, Text } from '@tarojs/components';
import Card from '@/components/Card';
import ArticleList from '@/components/ArticleList';
import ActivityList from '@/components/ActivityList';
import RvList from '@/components/RvList';
import { Category } from '@/types/ui'; // 确保路径正确

interface CustomTabPanelContentProps {
  item: Category;
}

const CustomTabPanel = ({ item }: CustomTabPanelContentProps) => {
    const category_id = item.id.split('|')[0];
    const channel = item.id.split('|')[1];
    const queryParams = {
        filter: {
            category_id: category_id
        },
        limit: 5
    };

    if (channel === 'article') {
        return (
            <Card>
                <ArticleList 
                    queryParams={queryParams}
                    isReachBottomRefresh={true}
                />
            </Card>
        );
    }

    if (channel === 'activity') {
        return (
            <Card>
                <ActivityList 
                    queryParams={queryParams}
                    isReachBottomRefresh={true}
                />
            </Card>
        );
    }

    if (channel === 'used_rv') {
        return (
            <Card>
                <RvList used />
            </Card>
        );
    }

    return (
        <Card>
            <View className="flex justify-center items-center h-64">
                <Text>暂无数据</Text>
            </View>
        </Card>
    );
};

export default CustomTabPanel;