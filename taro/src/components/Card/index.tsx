import { View } from '@tarojs/components'

interface CardProps {
	children: React.ReactNode;
	className?: string;
}
/**
 * 通用卡片容器
 * @param param0 
 * @returns 
 */
const Card = ({ children, className = '' }: CardProps) => (
	<View className={`bg-white rounded-md p-4 ${className}`}>
		{children}
	</View>
)

export default Card;
