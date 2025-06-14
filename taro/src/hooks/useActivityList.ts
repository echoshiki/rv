import { ActivityItem, Category } from "@/types/ui";
import { getActivityList } from "@/api/activity";
import { ActivityListQueryParams } from "@/types/query";
import { useResourceList } from "@/hooks/base/useResourceList";

// 从列表里提取出分类
const activityCategoryExtractor = (list: any[], params: ActivityListQueryParams): Category | null => {
    if (params.filter?.category_id !== undefined) {
        if (list && list.length > 0 && list[0].category) {
            return list[0].category;
        }
    }
    return null;
};

const useActivityList = (
    initialParams: ActivityListQueryParams,
    options: any = {}
) => {
    const resourceResult = useResourceList<ActivityItem, any, ActivityListQueryParams>(
        getActivityList,
        initialParams,
        {
            ...options,
            categoryExtractor: activityCategoryExtractor,
        }
    );

    return {
        ...resourceResult,
        activityList: resourceResult.list,
        activityCategory: resourceResult.category,
    };
};

export {
    useActivityList
}