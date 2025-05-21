import { View, Image } from "@tarojs/components";

const AspectRatioImage = ({ src, ratio, rounded }: { src: string, ratio: number, rounded?: string }) => {
    return (
        <View className={`relative block h-0 p-0 overflow-hidden`} style={{ paddingBottom: `${ratio * 100}%` }}>
            <Image
                src={src}
                className={`absolute object-cover w-full h-full border-none align-middle ${rounded ? `rounded-${rounded}` : ''}`}
                mode={`aspectFill`}
            />
        </View>
    )
}

export default AspectRatioImage;