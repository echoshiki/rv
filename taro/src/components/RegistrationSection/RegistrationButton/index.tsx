import { View, Button } from "@tarojs/components";

// 立即登录按钮
const RegistrationButton = ({ disabled, visible, loading, onClick }: {
    disabled: boolean,
    visible: boolean,
    loading: boolean,
    onClick: () => void
}) => {
    // 隐藏按钮
    if (!visible) {
        return null;
    }

    return (
        <View className="text-center">
            <Button
                type="primary"
                className="!bg-black text-sm leading-[3] w-full"
                disabled={disabled}
                loading={loading}
                onClick={onClick}
            >
                立即报名
            </Button>
        </View> 
    )
}

export default RegistrationButton;