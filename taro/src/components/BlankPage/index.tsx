import { View, Text } from "@tarojs/components";

interface BlankPageProps {
    title: string,
    description?: string
}

const BlankPage = ({ title, description }: BlankPageProps) => {
    return (
        <View className="bg-gray-100 min-h-screen p-5">
            <View className="flex items-center justify-center h-screen">
                <View className="h-56">
                    <View className="flex flex-col space-y-1 text-center">
                        <Text className="text-2xl font-semibold text-gray-500">{title}</Text>
                        <Text className="text-gray-500">{description}</Text>
                    </View>
                </View>
            </View>
        </View>
    )
}

export default BlankPage;