import { View } from '@tarojs/components';
import { SectionTitle } from '@/components/SectionTitle';
import Card from '@/components/Card';

interface PageCardProps {
    title?: string,
    subtitle?: string,
    link?: string,
    children: React.ReactNode,
    theme?: 'light' | 'dark',
    type?: 'default' | 'row',
    className?: string
}

const PageCard = ({ 
    title, 
    subtitle, 
    link, 
    children, 
    theme = 'light', 
    type = 'default',
    className = '' 
}: PageCardProps) => {
    return (
        <View className={`${theme === 'light' ? 'bg-gray-100' : 'bg-black'} min-h-screen py-5 ${className}`}>
            {title && (
                <SectionTitle
                    title={title}
                    subtitle={subtitle ?? ''}
                    link={link}
                    theme={theme}
                    type={type}
                />
            )}
            <View className="px-5">
                <Card className={`${theme === 'dark' && '!bg-[#3c3c3c] text-white'}`}>
                    {children}
                </Card>
            </View>
        </View>
    )
}

export default PageCard;