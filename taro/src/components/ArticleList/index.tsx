import { View, Text, Image } from '@tarojs/components';
import DefaultCover from '@/assets/images/cover.jpg';
import { ArticleItem as ArticleItemProps, ArticleList as ArticleListProps } from '@/types/ui';
import { mapsTo } from '@/utils/common';

const ArticleItem = ({ id, title, date, cover }: ArticleItemProps) => {
    return (
        <View className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed" onClick={() => mapsTo('pages/')}>
            <View className="w-24">
                <View className="relative block h-0 p-0 overflow-hidden pb-[80%] rounded-xl">
                    <Image
                        src={cover ? cover : DefaultCover}
                        className="absolute object-cover w-full h-full border-none align-middle" 
                    />
                </View>     
            </View>
            <View className="flex-1 flex flex-col">
                <View className=" text-ellipsis overflow-hidden line-clamp-2">
                    <Text>{title}</Text>
                </View>
                <View>
                    <View>
                        <Text className="text-gray-400 text-xs">
                            {date}
                        </Text>
                    </View>
                </View>
            </View>
        </View>
    )
}

const ArticleList = ({ title, subtitle, list }: ArticleListProps) => {
    return (
        <View className="px-5 mt-4">
            <View className="w-full py-5 rounded-xl bg-white px-3">
                <View className="flex flex-col border-l-8 border-black border-solid indent-2 mb-3">
                    <Text className='text-black font-semibold text-lg'>{title}</Text>
                    <Text className='text-gray-400 text-xs'>{subtitle}</Text>
                </View>
                <View>
                    {list.map(item => (
                        <ArticleItem 
                            id={item.id}
                            title={item.title}
                            cover={item.cover}
                            date={item.date}
                        />
                    ))}
                </View>
            </View>
        </View>
    )
}

export {
    ArticleItem,
    ArticleList
}