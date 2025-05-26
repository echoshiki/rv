import { useState, useCallback } from "react";
import { RegistrationItem, RegistrationFormData } from "@/types/ui";
import { PaymentResult } from "@/types/api";
import registrationApi from "@/api/registration";
import { showToast, requestPayment } from "@tarojs/taro";

// 配置项
interface UseRegistrationOptions {
    activityId: string;
    // 报名成功后的回调
    onSuccess?: (registration: RegistrationItem) => void;
    // 支付成功后的回调
    onPaymentSuccess?: (payment: PaymentResult) => void;
    // 错误处理
    onError?: (error: any) => void;
}

/**
 * 报名功能的基础操作 Hook
 * @description 处理单个报名操作（提交、支付、取消等）
 * @param options - 配置项 
 * @returns 
 */
const useRegistration = ({
    activityId,
    onSuccess,
    onPaymentSuccess,
    onError
}: UseRegistrationOptions) => {

    const [loading, setLoading] = useState(false);
    const [submitting, setSubmitting] = useState(false);
    const [paying, setPaying] = useState(false);
    const [registration, setRegistration] = useState<RegistrationItem | null>(null);

    // 解析地址工具函数
    const parseAddress = useCallback((address: string) => {
        const parts = address.split(' ').filter(Boolean);
        return {
            province: parts[0] || '',
            city: parts[1] || '',
            full_address: address
        };
    }, []);

    // 处理提交报名信息
    const submitRegistration = useCallback(async (formData: RegistrationFormData) => {
        if (!activityId) {
            throw new Error('活动ID不能为空');
        }

        // 开始提交
        setSubmitting(true);

        try {
            const { province, city, full_address } = parseAddress(formData.address);
            // 构造提交数据
            const submissionData = {
                activity_id: activityId,
                name: formData.name.trim(),
                phone: formData.phone.trim(),
                province,
                city,
                form_data: {
                    full_address,
                    submit_time: new Date().toISOString()
                }
            };
            // 提交到 API 接口
            const response = await registrationApi.store(submissionData);

            if (response.success) {
                // 赋值状态
                setRegistration(response.data);
                // 执行成功后的回调函数
                onSuccess?.(response.data);
                showToast({
                    icon: 'success',
                    title: '报名成功！'
                });
                // 返回报名信息
                return response.data;
            } else {
                throw new Error(response.message || '报名失败');
            }
        } catch (error: any) {
            const errorMessage = error?.data?.message || error?.message || '报名失败，请重试';
            // 错误分类处理
            if (error?.statusCode === 422) {
                // 表单验证错误
                showToast({
                    icon: 'error',
                    title: '请检查填写信息'
                });
            } else {
                showToast({
                    icon: 'error',
                    title: errorMessage,
                    duration: 3000
                });
            } 
            onError?.(error);
            throw error;
        } finally {
            setSubmitting(false);
        }
    }, [activityId, parseAddress, onSuccess, onError]);

    // 发起支付
    const initiatePayment = useCallback(async (amount: number) => {
        if (!registration?.id) {
            throw new Error('请先完成报名');
        }

        // 开始发起支付
        setPaying(true);

        try {
            // 构造发起支付的参数
            const paymentData = {
                registration_id: registration.id,
                amount
            };

            // 提交到接口
            const response = await registrationApi.createPayment(paymentData);

            // 返回信息
            if (response.success) {
                const paymentResult = response.data;

                // 调用微信支付，传入支付结果在支付成功后调用
                await handleWechatPay(paymentResult);
                
                return paymentResult;
            } else {
                throw new Error(response.message || '支付发起失败');
            }

        } catch (error: any) {
            // 发起支付失败
            const errorMessage = error?.message || '支付失败，请重试';
            showToast({
                icon: 'error',
                title: errorMessage
            });
            
            onError?.(error);
            throw error;
        } finally {
            setPaying(false);
        }
    }, [registration, onError]);

    // 调用微信支付
    const handleWechatPay = useCallback(async (paymentResult: PaymentResult) => {
        try {
            // 这里需要根据后端返回的支付参数调用微信支付
            // 假设后端返回了微信支付所需的参数
            const paymentParams = paymentResult as any;

            // 调用微信支付
            await requestPayment({
                timeStamp: paymentParams.timeStamp,
                nonceStr: paymentParams.nonceStr,
                package: paymentParams.package,
                signType: paymentParams.signType,
                paySign: paymentParams.paySign
            });

            // 支付成功
            showToast({
                icon: 'success',
                title: '支付成功！'
            });

            // 执行支付成功后的回调函数
            onPaymentSuccess?.(paymentResult);
        } catch (error: any) {
            if (error.errMsg?.includes('cancel')) {
                showToast({
                    icon: 'none',
                    title: '支付已取消'
                });
            } else {
                showToast({
                    icon: 'error',
                    title: '支付失败'
                });
            }
            throw error;
        }
    }, [onPaymentSuccess]);

    // 查询支付状态
    const checkPaymentStatus = useCallback(async (paymentId: string) => {
        // 开始查询
        setLoading(true);
        try {
            const response = await registrationApi.getPaymentStatus(paymentId);
            if (response.success) {
                return response.data;
            }
            throw new Error(response.message || '查询支付状态失败');
        } catch (error) {
            onError?.(error);
            throw error;
        } finally {
            setLoading(false);
        }
    }, [onError]);

    // 取消报名
    const cancelRegistration = useCallback(async () => {
        if (!registration?.id) {
            throw new Error('没有找到报名记录');
        }

        setLoading(true);
        try {
            const response = await registrationApi.cancel(registration.id);
            if (response.success) {
                setRegistration(null);
                showToast({
                    icon: 'success',
                    title: '取消报名成功'
                });
            } else {
                throw new Error(response.message || '取消报名失败');
            }
        } catch (error) {
            onError?.(error);
            throw error;
        } finally {
            setLoading(false);
        }
    }, [registration, onError]);

    return {
        // 状态
        loading,
        submitting,
        paying,
        registration,

        // 方法
        submitRegistration,
        initiatePayment,
        handleWechatPay,
        checkPaymentStatus,
        cancelRegistration,

        // 工具方法
        parseAddress
    }
}

export {
    useRegistration
};