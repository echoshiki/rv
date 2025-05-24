import { Loading as NutLoading, Cell, ConfigProvider } from '@nutui/nutui-react-taro'

const Loading = () => {
    return (
        <Cell className="flex justify-center">
            <ConfigProvider theme={{ nutuiLoadingIconSize: '32px' }}>
                <NutLoading type="circular" />
            </ConfigProvider>
        </Cell>
    )
}

export default Loading;