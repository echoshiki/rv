import { View, Text, Image } from '@tarojs/components';
import { mapsTo } from '@/utils/common';
import RightArrowIcon from '@/assets/icons/right-arrow.svg';

interface SectionTitleProps {
    title: string,
    subtitle?: string,
    link?: string,
    theme?: 'light' | 'dark',
    type?: 'default' | 'row',
    className?: string
}

/**
 * 页面卡片区域标题组件参数
 * @param title 标题
 * @param subtitle 副标题
 * @param link 链接
 * @param theme 主题
 * @param type 类型
 * @param className 类名
 */
const SectionTitle = ({ 
    title, 
    subtitle, 
    link, 
    theme = 'light',
    type = 'default',
    className 
}: SectionTitleProps) => {
    // 根据主题设置文本颜色类名
    const titleTextColorClass = theme === 'dark' ? 'text-white' : 'text-black';
    const subtitleTextColorClass = theme === 'dark' ? 'text-gray-400' : 'text-gray-400'; // 副标题在深色模式下可能也需要调整

    // 对于图标，如果图标本身是黑色的，在 dark 模式下可能需要反色
    // 注意：filter 属性在某些小程序平台可能有兼容性问题，测试后使用
    const iconFilterStyle = theme === 'dark' ? { filter: 'invert(1)' } : {};

    const renderTitle = () => {
        if (type === 'row') {
            return (
                <View className="flex flex-row items-center space-x-3">
                    <Text className={`${titleTextColorClass} font-semibold text-lg`}>{title}</Text>
                    <Text className={`${subtitleTextColorClass} font-light`}>{subtitle}</Text>
                </View>
            );
        }

        return (
            <View className="flex flex-col">
                <Text className={`${titleTextColorClass} font-semibold text-lg`}>{title}</Text>
                <Text className={`${subtitleTextColorClass} text-xs`}>{subtitle}</Text>
            </View>
        );
    }

    return (
        <View className={`p-5 flex justify-between items-center bg-transparent ${className}`}>
            {renderTitle()}
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