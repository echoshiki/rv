import { View, Text, Image } from '@tarojs/components';
import { mapsTo } from '@/utils/common';
import { SectionTitle as SectionTitleProps } from '@/types/ui';
import RightArrowIcon from '@/assets/icons/right-arrow.svg';

const SectionTitle = ({ title, subtitle, link }: SectionTitleProps) => {
    return (
        <View className="px-5 py-5 flex justify-between items-center">
            <View className="flex flex-col">
                <Text className='text-black font-semibold text-lg'>{title}</Text>
                <Text className='text-gray-400 text-xs'>{subtitle}</Text>
            </View>
            {link && (
                <View onClick={() => mapsTo(link)}>
                    <Image src={RightArrowIcon} className="w-5 h-5" />
                </View>
            )}
        </View>
    )
}

export {
    SectionTitle 
}