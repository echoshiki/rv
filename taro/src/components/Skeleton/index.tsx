import React from 'react'
import { View } from '@tarojs/components'

interface SkeletonProps {
	/** 宽度，支持数字(rpx)或字符串 */
	width?: number | string
	/** 高度，支持数字(rpx)或字符串 */
	height?: number | string
	/** 是否为圆形 */
	round?: boolean
	/** 是否显示动画 */
	animate?: boolean
	/** 自定义类名 */
	className?: string
	/** 自定义样式 */
	style?: React.CSSProperties
}

const Skeleton: React.FC<SkeletonProps> = ({
	width = '100%',
	height = 32,
	round = false,
	animate = true,
	className = '',
	style = {}
}) => {
	const getSize = (size: number | string) => {
		if (typeof size === 'number') {
			return `${size}rpx`
		}
		return size;
	}

	const skeletonStyle: React.CSSProperties = {
		width: getSize(width),
		height: getSize(height),
		borderRadius: round ? '50%' : '8rpx',
		...style
	}

	const baseClasses = 'bg-gray-200 relative overflow-hidden'
	const animateClasses = animate ? 'animate-pulse' : ''
	const roundClasses = round ? 'rounded-full' : 'rounded-md'

	const classes = [
		baseClasses,
		animateClasses,
		roundClasses,
		className
	].filter(Boolean).join(' ')

	return (
		<View
			className={classes}
			style={skeletonStyle}
		>
			{animate && (
				<View
					className="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent animate-shimmer"
					style={{
						animation: 'shimmer 1.5s ease-in-out infinite'
					}}
				/>
			)}
		</View>
	)
}

export default Skeleton