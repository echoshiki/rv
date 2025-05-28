import { Loading as NutLoading, ConfigProvider } from '@nutui/nutui-react-taro';
import { View } from '@tarojs/components';

const Loading = () => {
    return (
        <View className="flex justify-center bg-transparent shadow-none">
            <ConfigProvider theme={{ nutuiLoadingIconSize: '32px' }}>
                <NutLoading type="circular" />
            </ConfigProvider>
        </View>
    )
}

export default Loading;