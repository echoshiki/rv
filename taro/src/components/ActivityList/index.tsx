import { View, Text, Image } from '@tarojs/components';
import DefaultCover from '@/assets/images/cover.jpg';
import { ActivityItem as ActivityItemProps, ActivityList as ActivityListProps } from '@/types/ui';
import { mapsTo } from '@/utils/common';
import { Tag } from '@nutui/nutui-react-taro';

const ActivityItem = ({ item }: { item: ActivityItemProps }) => {
    return (
        <View 
            className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed" 
            onClick={() => mapsTo(`/pages/activity/detail/index?id=${item.id}`)}
        >
            <View className="w-24">
                <View className="relative block h-0 p-0 overflow-hidden pb-[80%] rounded-xl">
                    <Image
                        src={item.cover ? item.cover : DefaultCover}
                        className="absolute object-cover w-full h-full border-none align-middle" 
                        mode={`aspectFill`}
                    />
                </View>
            </View>
            <View className="flex-1 flex flex-col space-y-2">
                <View className="text-sm text-ellipsis overflow-hidden line-clamp-2 h-10">
                    <Text>{item.title}</Text>
                </View>
                <View className="flex flex-row justify-between items-center">
                    <View>
                        <Text className="text-gray-400 text-xs">
                            报名开始: {item.registration_start_at}
                        </Text>
                    </View>
                    <View>
                        {item.registration_fee === '0.00' && (
                            <Tag type="success">免费</Tag>
                        )}
                    </View>
                </View>
            </View>
        </View>
    )
}

const ActivityList = ({ list }: ActivityListProps) => {
    return (
        <View className="">
            <View className="w-full p-3 rounded-xl bg-white pb-5">
                <View>
                    {list.map(item => (
                        <ActivityItem item={item} />
                    ))}
                </View>
            </View>
        </View>
    )
}

export {
    ActivityItem,
    ActivityList
}