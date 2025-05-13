import { View, Text } from '@tarojs/components';
import { SectionTitle as SectionTitleProps } from '@/types/ui';

const SectionTitle = ({ title, subtitle }: SectionTitleProps) => {
    return (
        <View className="px-5 py-5 flex flex-col">
            <Text className='text-black font-semibold text-lg'>{title}</Text>
            <Text className='text-gray-400 text-xs'>{subtitle}</Text>
        </View>
    )
}

export {
    SectionTitle 
}