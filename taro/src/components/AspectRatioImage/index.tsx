import { View, Image } from "@tarojs/components";

interface AspectRatioImageProps {
    src: string;
    ratio: number;
    rounded?: string;
    children?: React.ReactNode;
}

const AspectRatioImage = ({ src, ratio, rounded, children }: AspectRatioImageProps) => {
    return (
        <View className={`relative block h-0 p-0 overflow-hidden`} style={{ paddingBottom: `${ratio * 100}%` }}>
            <Image
                src={src}
                lazyLoad
                className={`absolute object-cover w-full h-full border-none align-middle ${rounded ? `rounded-${rounded}` : 'rounded-md'}`}
                mode={`aspectFill`}
            />
            {children}
        </View>
    )
}

export default AspectRatioImage;