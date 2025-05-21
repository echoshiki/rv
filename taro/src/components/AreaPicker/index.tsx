import useRegionPicker from '@/hooks/useRegionPicker'
import { Picker } from '@nutui/nutui-react-taro'

interface AreaPickerProps {
    visible: boolean,
    value: string[],
    onConfirm: (value: string[]) => void,
    onClose: () => void
}

/**
 * 地址选择组件
 * @param visible 是否打开地址选择
 * @param value 初始地址选择打开/关闭状态
 * @param onChangeArea 地址选择变化时的回调
 * @param onClose 地址选择关闭时的回调
 */
const AreaPicker = ({ 
    visible, 
    value, 
    onConfirm, 
    onClose 
}: AreaPickerProps) => {
    const { options, setSelected } = useRegionPicker();
    return (
        <Picker
            visible={visible}
            options={options}
            value={value}
            onClose={onClose}
            // 确认时触发
            onConfirm={(options) => {
                // 格式化传给页面的数据，显示 text 而不是 code
                onConfirm(options.map(option => option.text.toString()));
            }}
            // 更新选中的地址触发
            onChange={(options) => {
                setSelected(options);
            }}
        />
    )
}
export default AreaPicker
