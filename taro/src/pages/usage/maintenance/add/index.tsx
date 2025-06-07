import { View, Button } from "@tarojs/components";
import { useState, useRef } from 'react';
import { Form, Input, FormInstance } from '@nutui/nutui-react-taro';
import AreaPicker from '@/components/AreaPicker';
import PageCard from '@/components/PageCard';
import maintenanceApi from '@/api/maintenance';
import { showToast } from "@tarojs/taro";
import { mapsBack, parseAddress } from "@/utils/common";


const AddMaintenance = () => {

    const [loading, setLoading] = useState<boolean>(false);
    const formRef = useRef<FormInstance>(null);

    // 地区组件
    const [areaPickerState, setAreaPickerState] = useState({
        visible: false,
        area: [] as string[]
    });

    // 地区确认
    const handleAreaConfirm = (area: string[]) => {
        setAreaPickerState({ visible: false, area });
        formRef.current?.setFieldsValue({
            address: area.join(' ')
        });
    }

    // 提交表单
    const onSubmit = async (formData) => {
        setLoading(true);
        try {
            // 处理省份城市为空的情况
            const { province, city } = formData.address ? parseAddress(formData.address) : {
                province: '',
                city: ''
            };

            // 构造提交数据
            const submissionData = {
                name: formData.name.trim(),
                phone: formData.phone.trim(),
                issues: formData.issues.trim(),
                province,
                city
            };
            const response = await maintenanceApi.create(submissionData);

            if (response.success) {
                showToast({
                    icon: 'success',
                    title: '预约成功'
                });
                setTimeout(() => {
                    mapsBack();
                }, 1000);
            } else {
                throw new Error(response.message);
            }
        } catch (error) {
            console.error('预约失败:', error);
            showToast({
                icon: 'none',
                title: error.data.message
            });
        } finally {
            setLoading(false);
        }
    }

    return (
        <PageCard title="维保预约" subtitle="请仔细填写以下信息来创建您的预约">
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
                    label="所在城市"
                    name="address"
                    rules={[{ required: true, message: '请选择所在城市' }]}
                >
                    <Input
                        placeholder="请选择地址"
                        readOnly
                        onClick={() => setAreaPickerState(prev => ({ ...prev, visible: true }))}
                        style={{ caretColor: 'transparent' }}
                    />
                </Form.Item>

                <Form.Item
                    label="预约事项"
                    name="issues"
                    rules={[{ required: true, message: '请输入预约事项' }]}
                >
                    <Input placeholder="简短描述您的维保需求" type="text" />
                </Form.Item>

                <View className="mt-5">
                    <Button
                        type="primary"
                        formType="submit"
                        className="text-sm leading-[3] w-full !bg-black"
                        loading={loading}
                    >
                        提交预约
                    </Button>
                </View>
            </Form>

            <AreaPicker
                visible={areaPickerState.visible}
                value={areaPickerState.area}
                onConfirm={handleAreaConfirm}
                onClose={() => setAreaPickerState(prev => ({ ...prev, visible: false }))}
            />
        </PageCard>
    )
}

export default AddMaintenance;