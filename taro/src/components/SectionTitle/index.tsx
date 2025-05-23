import { View, Text, Image } from '@tarojs/components';
import { mapsTo } from '@/utils/common';
import { SectionTitle as SectionTitleProps } from '@/types/ui'; // 假设你的类型定义在这个路径
import RightArrowIcon from '@/assets/icons/right-arrow.svg'; // 假设你的图标路径

interface ThemedSectionTitleProps extends SectionTitleProps {
    theme?: 'light' | 'dark'; 
}

const SectionTitle = ({ title, subtitle, link, theme = 'light' }: ThemedSectionTitleProps) => {
    // 根据主题设置文本颜色类名
    const titleTextColorClass = theme === 'dark' ? 'text-white' : 'text-black';
    const subtitleTextColorClass = theme === 'dark' ? 'text-gray-400' : 'text-gray-400'; // 副标题在深色模式下可能也需要调整

    // 对于图标，如果图标本身是黑色的，在 dark 模式下可能需要反色
    // 注意：filter 属性在某些小程序平台可能有兼容性问题，测试后使用
    const iconFilterStyle = theme === 'dark' ? { filter: 'invert(1)' } : {};

    return (
        <View className={`px-5 py-5 flex justify-between items-center bg-transparent`}>
            <View className="flex flex-col">
                <Text className={`${titleTextColorClass} font-semibold text-lg`}>{title}</Text>
                <Text className={`${subtitleTextColorClass} text-xs`}>{subtitle}</Text>
            </View>
            {link && (
                <View onClick={() => mapsTo(link)} className="flex items-center">
                    <Image
                        src={RightArrowIcon}
                        className="w-5 h-5"
                        style={iconFilterStyle} // 应用条件滤镜样式
                    />
                </View>
            )}
        </View>
    );
};

export {
    SectionTitle
};