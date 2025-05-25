import { View, Text } from '@tarojs/components';
import { Tag, Price } from '@nutui/nutui-react-taro';

/**
 * 活动信息摘要
 * @param param0 
 * @returns 
 */
const ActivityNote = ({ activityDetail }) => {
    if (!activityDetail) return null;

    return (
        <View className="flex flex-col space-y-2">
            {activityDetail?.registration_start_at && activityDetail?.registration_end_at && (
                <View className="flex flex-nowrap items-center space-x-2">
                    <Text className="text-xs font-light">报名时间</Text>
                    <Text className="text-xs font-light">
                        {activityDetail?.registration_start_at ? activityDetail?.registration_start_at : '即日起'}
                        {' - '}
                        {activityDetail?.registration_end_at ? activityDetail?.registration_end_at : '不定期'}
                    </Text>
                </View>
            )}

            {activityDetail?.started_at && activityDetail?.ended_at && (
                <View className="flex flex-nowrap items-center space-x-2">
                    <Text className="text-xs font-light">活动时间</Text>
                    <Text className="text-xs font-light">
                        {activityDetail?.started_at ? activityDetail?.started_at : '即日起'}
                        {' - '}
                        {activityDetail?.ended_at ? activityDetail?.ended_at : '不定期'}
                    </Text>
                </View>
            )}

            {activityDetail?.registration_fee && (
                <View className="flex flex-nowrap items-center space-x-2">
                    <Text className="text-xs font-light">报名费用</Text>
                    {activityDetail?.registration_fee === '0.00' ? (
                        <Tag type="success">免费</Tag>
                    ) : (
                        <Price size="normal" price={activityDetail?.registration_fee} />
                    )}
                </View>
            )}
        </View>
    )
}

export default ActivityNote;