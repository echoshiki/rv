import { View, Button } from "@tarojs/components";
import { Form, Input, FormInstance } from '@nutui/nutui-react-taro';
import AreaPicker from '@/components/AreaPicker'
import { useState, useRef } from 'react';
import { RegistrationFormData } from '@/types/ui';

interface RegistrationFormProps {
    onSubmit: (data: RegistrationFormData) => void,
    loading: boolean,
    isVisible: boolean
}

const RegistrationForm = ({ onSubmit, loading = false, isVisible = false }: RegistrationFormProps) => {

    if (!isVisible) {
        return null;
    }

    const formRef = useRef<FormInstance>(null);
    const [pickerState, setPickerState] = useState({
        visible: false,
        area: [] as string[]
    })

    const handleConfirm = (area: string[]) => {
        setPickerState({ visible: false, area })
        formRef.current?.setFieldsValue({
            address: area.join(' ')
        })
    }

    return (
        <>
            <Form
                ref={formRef}
                divider
                labelPosition="left"
                onFinish={onSubmit}
            >
                <Form.Item
                    label="称呼"
                    name="name"
                    rules={[{ required: true, message: '请输入称呼' }]}
                >
                    <Input placeholder="请输入称呼" type="text" />
                </Form.Item>

                <Form.Item
                    label="手机号"
                    name="phone"
                    rules={[
                        { required: true, message: '请输入手机号' },
                        { pattern: /^1[3-9]\d{9}$/, message: '手机号格式不正确' }
                    ]}
                >
                    <Input placeholder="请输入手机号" type="number" maxLength={11} />
                </Form.Item>

                <Form.Item
                    label="地址"
                    name="address"
                    rules={[{ required: true, message: '请选择地址' }]}
                >
                    <Input
                        placeholder="请选择地址"
                        readOnly
                        onClick={() => setPickerState(prev => ({ ...prev, visible: true }))}
                        style={{ caretColor: 'transparent' }}
                    />
                </Form.Item>

                <View className="mt-5">
                    <Button 
                        type="primary" 
                        formType="submit" 
                        className="!border border-solid !border-black !bg-transparent !text-black"
                        loading={loading}
                    >
                        提交报名信息
                    </Button>
                </View>
            </Form>

            <AreaPicker
                visible={pickerState.visible}
                value={pickerState.area}
                onConfirm={handleConfirm}
                onClose={() => setPickerState(prev => ({ ...prev, visible: false }))}
            />
        </>
    )
}

export default RegistrationForm