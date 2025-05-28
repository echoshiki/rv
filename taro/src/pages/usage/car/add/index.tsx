import { View, Button } from "@tarojs/components";
import { useState, useRef } from 'react';
import { DatePicker, Form, Input, FormInstance } from '@nutui/nutui-react-taro';
import AreaPicker from '@/components/AreaPicker'
import Card from '@/components/Card';
import { SectionTitle } from '@/components/SectionTitle';
import myCarApi from '@/api/car';
import { navigateBack, showToast } from "@tarojs/taro";
import { parseAddress } from "@/utils/common";

const AddMyCar = () => {

    // 我的爱车
    const [loading, setLoading] = useState<boolean>(false);
    
    const formRef = useRef<FormInstance>(null);

    // 日期组件
    const [datePickerState, setDatePickerState] = useState({
        visible: false,
        date: new Date() as Date
    });

    // 地区组件
    const [areaPickerState, setAreaPickerState] = useState({
        visible: false,
        area: [] as string[]
    });

    // 日期确认
    const handleDateConfirm = (_options, values) => {
        formRef.current?.setFieldsValue({
            listing_at: values.join('/')
        });
    }

    const handleAreaConfirm = (area: string[]) => {
        setAreaPickerState({ visible: false, area });
        formRef.current?.setFieldsValue({
            address: area.join(' ')
        });
    }

    const onSubmit = async (formData) => {
        setLoading(true);
        try {
            const { province, city } = parseAddress(formData.address);

            const submissionData = {
                name: formData.name.trim(),
                phone: formData.phone.trim(),
                brand: formData.brand.trim(),
                vin: formData.vin.trim(),
                licence_plate: formData.licence_plate.trim(),
                listing_at: formData.listing_at,
                province,
                city,
                address: formData.address_info.trim(),
            };

            const response = await myCarApi.create(submissionData);
            console.log('response', response);

            if (response.success) {
                showToast({
                    icon: 'success',
                    title: '添加成功！'
                });
                setTimeout(() => {
                    navigateBack();
                }, 1000);
            } else {         
                showToast({
                    icon: 'none',
                    title: response.message
                });
            }
        } catch (error) {
            console.error('添加失败:', error);
            showToast({
                icon: 'none',
                title: error.message
            });
        } finally {
            setLoading(false);
        }
    }

    return (
        <View className="bg-gray-100 min-h-screen py-5">
            <SectionTitle
                title={`添加爱车`}
                subtitle={`请认真填写以下信息来绑定你的爱车`}
            />
            <View className="px-5">
                <Card>
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
                            label="车型"
                            name="brand"
                            rules={[{ required: true, message: '请输入车型' }]}
                        >
                            <Input placeholder="请输入车型" type="text" />
                        </Form.Item>

                        <Form.Item
                            label="车架号"
                            name="vin"
                            rules={[{ required: true, pattern: /^[A-Za-z0-9]{17}$/, message: '请输入17位的车架号' }]}
                        >
                            <Input placeholder="请输入称呼" type="text" />
                        </Form.Item>

                        <Form.Item
                            label="车牌号"
                            name="licence_plate"
                            rules={[{ required: true, message: '请输入车牌号' }]}
                        >
                            <Input placeholder="请输入称呼" type="text" />
                        </Form.Item>

                        <Form.Item
                            label="上牌日期"
                            name="listing_at"
                        >
                            <Input
                                placeholder="请选择日期"
                                readOnly
                                onClick={() => setDatePickerState(prev => ({ ...prev, visible: true }))}
                                style={{ caretColor: 'transparent' }}
                            />
                        </Form.Item>

                        <Form.Item
                            label="所在城市"
                            name="address"
                        >
                            <Input
                                placeholder="请选择地址"
                                readOnly
                                onClick={() => setAreaPickerState(prev => ({ ...prev, visible: true }))}
                                style={{ caretColor: 'transparent' }}
                            />
                        </Form.Item>

                        <Form.Item
                            label="详细地址"
                            name="address_info"
                        >
                            <Input placeholder="请输入详细的住宅" type="text" />
                        </Form.Item>

                        <View className="mt-5">
                            <Button
                                type="primary"
                                formType="submit"
                                className="text-[.8rem] w-full !bg-black"
                                loading={loading}
                            >
                                提交报名信息
                            </Button>
                        </View>
                    </Form>

                    <AreaPicker
                        visible={areaPickerState.visible}
                        value={areaPickerState.area}
                        onConfirm={handleAreaConfirm}
                        onClose={() => setAreaPickerState(prev => ({ ...prev, visible: false }))}
                    />

                    <DatePicker
                        title="日期选择"
                        visible={datePickerState.visible}
                        defaultValue={new Date(datePickerState.date)}
                        showChinese
                        onConfirm={handleDateConfirm}
                        onClose={() => setDatePickerState(prev => ({ ...prev, visible: false }))}
                    />
                </Card>
            </View>

        </View>
    )
}

export default AddMyCar;