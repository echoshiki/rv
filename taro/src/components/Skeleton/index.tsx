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

const ArticleItemSkeleton = () => {
	return (
		<View className="flex flex-nowrap items-center space-x-3 py-3 border-b border-gray-300 border-dashed">
			<View className="w-24">
				<Skeleton height={150} round={false} animate={true} />
			</View>
			<View className="flex-1 flex flex-col space-y-2">
				<Skeleton height={32} round={false} animate={true} />
				<Skeleton height={32} round={false} animate={true} />
				<Skeleton width={`50%`} height={32} round={false} animate={true} />
			</View>
		</View>
	)
}

// 文章列表
const ArticleListSkeleton = () => {
	return (
		<View>
			<ArticleItemSkeleton />
			<ArticleItemSkeleton />
			<ArticleItemSkeleton />
			<ArticleItemSkeleton />
		</View>
	)
}

const ArticleDetailSkeleton = () => {
	return (
		<View className="bg-gray-100 min-h-screen pb-5">
            <View className="w-full">
                <Skeleton height={320} round={false} animate={true} />
            </View>
            <View className="w-5/6 px-5 relative mt-[-3rem] mx-auto bg-white py-4">
				<View className="mb-5 flex flex-col space-y-2">
					<Skeleton height={50} round={false} animate={true} />
					<Skeleton width={120} height={50} round={false} animate={true} />
				</View>

				{/* 活动相关信息 */}
				<View>
					<Skeleton height={240} round={false} animate={true} />
				</View>
				
				{/* 内容 */}
				<View className="w-full flex flex-col space-y-2 mt-5">
					<Skeleton height={50} round={false} animate={true} />
					<Skeleton height={50} round={false} animate={true} />
					<Skeleton height={50} round={false} animate={true} />
					<Skeleton height={50} round={false} animate={true} />
					<Skeleton width={`50%`} height={50} round={false} animate={true} />
				</View>
            </View>
        </View>
	)
}

// 首页矩阵图菜单
const MenuMatrixSkeleton = () => {
	return (
		<View className="flex flex-nowrap space-x-2">
			<View className="w-1/2">
				<Skeleton height={300} round={false} animate={true} />
			</View>
			<View className="w-1/2 flex flex-col justify-between">
				<Skeleton height={140} round={false} animate={true} />
				<Skeleton height={140} round={false} animate={true} />
			</View>
		</View>
	)
}

// 用户中心横向菜单
const MenuRowSkeleton = () => {
	return (
		<View className="flex flex-col space-y-2">
			<Skeleton height={150} round={false} animate={true} />
		</View>
	)
}

// 用户中心竖形菜单
const MenuColumnSkeleton = () => {
	return (
		<View className="flex flex-col space-y-3">
			<Skeleton height={80} round={false} animate={true} />
			<Skeleton height={80} round={false} animate={true} />
			<Skeleton height={80} round={false} animate={true} />
			<Skeleton height={80} round={false} animate={true} />
			<Skeleton height={80} round={false} animate={true} />
			<Skeleton height={80} round={false} animate={true} />
		</View>
	)
}

// 轮播图
const BannerSkeleton = () => {
	return (
		<View>
			<Skeleton height={420} round={false} animate={true} />
		</View>
	)
}

const TabSkeleton = () => {
	return (
		<View className="w-full py-3">
			<View className="grid grid-cols-3">
				<Skeleton height={60} width={200} round={false} animate={true} />
				<Skeleton height={60} width={200} round={false} animate={true} />
				<Skeleton height={60} width={200} round={false} animate={true} />
			</View>
			<View>
				<ArticleListSkeleton />
			</View>
		</View>
	)
}

export {
	Skeleton,
	ArticleItemSkeleton,
	ArticleListSkeleton,
	ArticleDetailSkeleton,
	MenuMatrixSkeleton,
	MenuColumnSkeleton,
	MenuRowSkeleton,
	BannerSkeleton,
	TabSkeleton
} 