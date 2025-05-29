import { View } from '@tarojs/components';
import { SectionTitle } from '@/components/SectionTitle';
import Card from '@/components/Card';

interface PageCardProps {
    title?: string,
    subtitle?: string,
    link?: string,
    children: React.ReactNode,
    className?: string
}

const PageCard = ({ title, subtitle, link, children, className = '' }: PageCardProps) => {
    return (
        <View className={`bg-gray-100 min-h-screen py-5 ${className}`}>
            {title && (
                <SectionTitle
                    title={title}
                    subtitle={subtitle ?? ''}
                    link={link}
                />
            )}
            <View className="px-5">
                <Card>
                    {children}
                </Card>
            </View>
        </View>
    )
}

export default PageCard;