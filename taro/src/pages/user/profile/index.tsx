import { View, Button } from "@tarojs/components";
import { useState, useRef } from 'react';
import { DatePicker, Form, Input, Radio, FormInstance } from '@nutui/nutui-react-taro';
import AreaPicker from '@/components/AreaPicker';
import PageCard from '@/components/PageCard';
import useAuthStore from '@/stores/auth';
import { showToast } from "@tarojs/taro";
import { parseAddress } from "@/utils/common";

const UserProfile = () => {

    const formRef = useRef<FormInstance>(null);
    const { userInfo, updateUserInfo, isLoading } = useAuthStore();

    // 日期组件
    const [datePickerState, setDatePickerState] = useState({
        visible: false,
        date: userInfo?.birthday ? new Date(userInfo.birthday) : new Date() as Date
    });

    // 地区组件
    const [areaPickerState, setAreaPickerState] = useState({
        visible: false,
        area: [] as string[]
    });

    const handleDateConfirm = (_options, values) => {
        formRef.current?.setFieldsValue({
            birthday: values.join('/')
        });
    }

    const handleAreaConfirm = (area: string[]) => {
        setAreaPickerState({ visible: false, area });
        formRef.current?.setFieldsValue({
            address: area.join(' ')
        });
    }

    const onSubmit = async (formData) => {
        try {
            // 处理省份城市为空的情况
            const { province, city } = formData.address ? parseAddress(formData.address) : {
                province: '',
                city: ''
            };

            // 构造提交数据
            const submissionData = {
                name: formData.name.trim(),
                sex: formData.sex,
                birthday: formData.birthday,
                province,
                city,
                address: formData.address_info?.trim(),
            };

            await updateUserInfo(submissionData);

            showToast({
                icon: 'success',
                title: '修改成功！'
            });

        } catch (error) {
            console.error('添加失败:', error);
            showToast({
                icon: 'none',
                title: error.data.message
            });
        }
    }

    return (
        <PageCard title="个人资料" subtitle="修改您的个人信息让我们为你更好的服务。">
            <Form
                ref={formRef}
                divider
                labelPosition="left"
                onFinish={onSubmit}
            >
                <Form.Item
                    label="昵称"
                    name="name"
                    initialValue={userInfo?.name}
                    rules={[{ required: true, message: '请输昵称' }]}
                >
                    <Input placeholder="请输入昵称" type="text" />
                </Form.Item>

                <Form.Item
                    label="性别"
                    name="sex"
                    initialValue={userInfo?.sex}
                >
                    <Radio.Group direction="horizontal">
                        <Radio value={1}>男</Radio>
                        <Radio value={2}>女</Radio>
                    </Radio.Group>
                </Form.Item>

                <Form.Item
                    label="生日"
                    name="birthday"
                    initialValue={userInfo?.birthday}
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
                    initialValue={`${userInfo?.province || ''} ${userInfo?.city || ''}`}
                >
                    <Input
                        placeholder="请选择所在城市"
                        readOnly
                        onClick={() => setAreaPickerState(prev => ({ ...prev, visible: true }))}
                        style={{ caretColor: 'transparent' }}
                    />
                </Form.Item>

                <Form.Item
                    label="详细地址"
                    name="address_info"
                    initialValue={userInfo?.address}
                >
                    <Input placeholder="请输入详细的地址" type="text" />
                </Form.Item>

                <View className="mt-5">
                    <Button
                        type="primary"
                        formType="submit"
                        className="text-[.8rem] w-full !bg-black"
                        loading={isLoading}
                    >
                        修改资料
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
                startDate={new Date(1949, 0, 1)}
                showChinese
                onConfirm={handleDateConfirm}
                onClose={() => setDatePickerState(prev => ({ ...prev, visible: false }))}
            />
        </PageCard>
    )
}

export default UserProfile;