import { View, Button } from "@tarojs/components";
import { useState, useRef } from 'react';
import { Form, Input, TextArea, FormInstance } from '@nutui/nutui-react-taro';
import PageCard from '@/components/PageCard';
import suggestApi from '@/api/suggest';
import { showToast } from "@tarojs/taro";
import { mapsBack } from "@/utils/common";

const AddSuggest = () => {

    const [loading, setLoading] = useState<boolean>(false);
    const formRef = useRef<FormInstance>(null);

    // 提交表单
    const onSubmit = async (formData) => {
        setLoading(true);
        try {
            // 构造提交数据
            const submissionData = {
                name: formData.name.trim(),
                content: formData.content.trim()
            };
            const response = await suggestApi.create(submissionData);

            if (response.success) {
                showToast({
                    icon: 'success',
                    title: '提交成功'
                });
                setTimeout(() => {
                    mapsBack();
                }, 1000);
            } else {
                throw new Error(response.message);
            }
        } catch (error) {
            console.error('提交失败:', error);
            showToast({
                icon: 'none',
                title: error.data.message
            });
        } finally {
            setLoading(false);
        }
    }

    return (
        <PageCard title="用户建议" subtitle="诚挚的接纳您的意见">
            <Form
                ref={formRef}
                divider
                labelPosition="left"
                onFinish={onSubmit}
            >
                <Form.Item
                    label=""
                    name="name"
                    rules={[{ required: true, message: '请输入称呼' }]}
                >
                    <Input placeholder="请输入称呼" type="text" />
                </Form.Item>

                <Form.Item
                    label=""
                    name="content"
                    rules={[{ required: true, message: '请输入建议内容' }]}
                >
                    <TextArea placeholder="请输入您的建议" />
                </Form.Item>

                <View className="mt-5">
                    <Button
                        type="primary"
                        formType="submit"
                        className="text-sm leading-[3] w-full !bg-black"
                        loading={loading}
                    >
                        提交建议
                    </Button>
                </View>
            </Form>
        </PageCard>
    )
}

export default AddSuggest;