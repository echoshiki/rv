import { useState, useEffect } from 'react'
import Taro from '@tarojs/taro'
import { View, Image, Swiper, SwiperItem } from '@tarojs/components'
import { BannerItem } from '@/types/api';

/**
 * 轮播图参数类型
 * @param imageList 轮播图片列表
 * @param indicatorColor 指示器颜色
 * @param indicatorActiveColor 当前选中的指示器颜色
 * @param indicatorPosition 指示器位置
 * @param autoplay 是否自动播放
 * @param interval 自动轮播间隔时间(ms)
 * @param imageRatio 图片比例
 * @param useScreenWidth 是否使用屏幕宽度
 * @param rounded 圆角
 * @param onClick 点击图片回调
 */
interface CustomSwiperProps {
    imageList: BannerItem[];
    indicatorColor?: string;
    indicatorActiveColor?: string;
    indicatorPosition?: 'left' | 'right' | 'center';
    autoplay?: boolean;
    interval?: number;
    imageRatio?: number;
    useScreenWidth?: boolean;
    rounded?: 'none' | 'sm' | 'md' | 'lg' | 'xl';
    onClick?: (item: any) => void;
}

const CustomSwiper: React.FC<CustomSwiperProps> = ({
    imageList = [],
    indicatorColor = 'rgba(255, 255, 255, 0.6)',
    indicatorActiveColor = '#ffffff',
    indicatorPosition = 'center',
    autoplay = true,
    interval = 3000,
    imageRatio = 2,
    useScreenWidth = false,
    rounded = 'none',
    onClick
}) => {
    const [current, setCurrent] = useState(0);
    const [swiperHeight, setSwiperHeight] = useState(0);

    // 计算图片高度，保持宽高比例
    const calculateImageHeight = () => {
        if (imageList.length === 0) return;
        if (useScreenWidth) {  
            // 使用屏幕宽度计算高度   
            const systemInfo = Taro.getSystemInfoSync();
            const screenWidth = systemInfo.windowWidth;
            const height = screenWidth / imageRatio;
            setSwiperHeight(height);
        } else {
            // 设置延迟以确保组件已经挂载
            setTimeout(() => { 
                const query = Taro.createSelectorQuery();
                // 调用 Taro 的 API 获取组件宽度
                query.select('.swiper-self').boundingClientRect();
                query.exec((res) => {
                    const { width } = res[0];
                    const height = width / imageRatio;
                    setSwiperHeight(height);
                });
            }, 50);
        }
    };

    // 组件挂载后计算高度
    useEffect(() => {
        calculateImageHeight();
    }, [imageList]);

    // 监听滑动事件
    const handleChange = (e) => {
        setCurrent(e.detail.current);
    };

    // 点击轮播图
    const handleClick = (item) => {
        if (onClick) onClick(item);
    };

    // 根据指示器位置返回样式类名
    const getIndicatorPositionClass = () => {
        switch (indicatorPosition) {
            case 'left':
                return 'left-5';
            case 'right':
                return 'right-5';
            case 'center':
            default:
                return 'left-1/2 transform -translate-x-1/2';
        }
    };

    return (
        <View className={`relative w-full z-0 swiper-self rounded-${rounded} overflow-hidden`}>
            <Swiper
                className="w-full"
                style={{ 
                    height: `${swiperHeight}px`
                }}
                indicatorDots={false}
                autoplay={autoplay}
                interval={interval}
                circular
                onChange={handleChange}
            >
                {imageList.map(item => (
                    <SwiperItem key={item.id} onClick={() => handleClick(item)}>
                        <Image
                            src={item.image}
                            className="w-full h-auto"
                            mode="widthFix"
                            lazyLoad
                        />
                    </SwiperItem>
                ))}
            </Swiper>

            {/* 自定义右下角指示器 */}
            <View className={`absolute bottom-5 ${getIndicatorPositionClass()} flex flex-row items-center z-10`}>
                {imageList.map((_, index) => (
                    <View
                        key={index}
                        className={`h-1 mx-1 rounded-full transition-all duration-300 ${current === index ? 'w-3' : 'w-2'}`}
                        style={{
                            backgroundColor: current === index ? indicatorActiveColor : indicatorColor
                        }}
                    />
                ))}
            </View>
        </View>
    );
};

export default CustomSwiper;