import { useState, useCallback } from "react";
import { ActivityDetail, RegistrationItem, RegistrationFormData } from "@/types/ui";
import { PaymentResult } from "@/types/api";
import registrationApi from "@/api/registration";
import { requestPayment } from "@tarojs/taro";

// 定义明确的状态码
export enum RegistrationStatusCode {
    SUCCESS = 'success',
    ALREADY_REGISTERED = 'already_registered',
    PENDING_PAYMENT = 'pending_payment',
    NOT_ELIGIBLE = 'not_eligible',
    ACTIVITY_NOT_STARTED = 'activity_not_started', 
    ACTIVITY_ENDED = 'activity_ended',      
    ACTIVITY_FULL = 'activity_full'
}

// 定义步骤枚举
export enum RegistrationStep {
    INITIAL = 'initial',
    FORM = 'form',
    PAYMENT = 'payment',
    SUCCESS = 'success'
}

// 统一的加载状态
interface LoadingState {
    checking: boolean;
    submitting: boolean;
    paying: boolean;
    canceling: boolean;
}

// Hook 配置项
interface UseRegistrationOptions {
    activityDetail: ActivityDetail;
    onCheckedSuccess?: (statusCode: RegistrationStatusCode) => void;
    onSuccess?: (registration: RegistrationItem, message?: string) => void;
    onPaymentSuccess?: (payment: PaymentResult, message?: string) => void;
    onError?: (error: any, context?: { action: string; message?: string }) => void;
}

/**
 * 报名功能的基础操作 Hook
 * @description 处理单个报名操作（提交、支付、取消等），纯粹的 API 调用和状态管理
 */
const useRegistration = ({
    activityDetail,
    onCheckedSuccess,
    onSuccess,
    onPaymentSuccess,
    onError
}: UseRegistrationOptions) => {
    const [loadingState, setLoadingState] = useState<LoadingState>({
        checking: false,
        submitting: false,
        paying: false,
        canceling: false
    });
    
    const [registration, setRegistration] = useState<RegistrationItem | null>(null);

    // 更新单个加载状态的工具函数
    const updateLoadingState = useCallback((key: keyof LoadingState, value: boolean) => {
        setLoadingState(prev => ({ ...prev, [key]: value }));
    }, []);

    // 解析地址工具函数
    const parseAddress = useCallback((address: string) => {
        const parts = address.split(' ').filter(Boolean);
        return {
            province: parts[0] || '',
            city: parts[1] || '',
            full_address: address
        };
    }, []);

    // 基于活动信息检查状态
    const checkActivityStatus = useCallback((): { 
        statusCode: RegistrationStatusCode,
        canRegister: boolean 
    } => {
        const now = new Date();
        // 活动报名开始和结束时间
        const startTime = new Date(activityDetail.registration_start_at);
        const endTime = new Date(activityDetail.registration_end_at);

        if (startTime && now < startTime) {
            return {
                statusCode: RegistrationStatusCode.ACTIVITY_NOT_STARTED,
                canRegister: false
            };
        }

        if (endTime && now > endTime) {
            return {
                statusCode: RegistrationStatusCode.ACTIVITY_ENDED,
                canRegister: false
            };
        }

        // 检查名额
        if (activityDetail.current_participants >= activityDetail.max_participants) {
            return {
                statusCode: RegistrationStatusCode.ACTIVITY_FULL,
                canRegister: false
            };
        }

        return {
            statusCode: RegistrationStatusCode.SUCCESS,
            canRegister: true
        };
    }, [activityDetail]);

    // 查询报名状态
    const checkStatus = useCallback(async (): Promise<RegistrationStatusCode> => {
        updateLoadingState('checking', true);

        try {
            // 1. 先检查活动状态
            const activityCheck = checkActivityStatus();
            if (!activityCheck.canRegister)
                return activityCheck.statusCode;

            // 2. 活动状态正常，检查用户是否已报名
            const { data } = await registrationApi.status(activityDetail.id);

            let statusCode: RegistrationStatusCode;
            if (data?.value === 'approved') {
                statusCode = RegistrationStatusCode.ALREADY_REGISTERED;
            } else if (data?.value === 'pending') {
                statusCode = RegistrationStatusCode.PENDING_PAYMENT;
            } else {
                statusCode = RegistrationStatusCode.SUCCESS;
            }

            // 只有成功状态才执行成功回调
            if (statusCode === RegistrationStatusCode.SUCCESS) {
                onCheckedSuccess?.(statusCode);
            }

            return statusCode;
        } catch (error) {
            onError?.(error, { action: 'checkStatus' });
            throw error;
        } finally {
            updateLoadingState('checking', false);
        }
    }, [activityDetail, onCheckedSuccess, onError, updateLoadingState]);

    // 提交报名信息
    const submitRegistration = useCallback(async (formData: RegistrationFormData) => {
        updateLoadingState('submitting', true);

        try {
            const { province, city, full_address } = parseAddress(formData.address);
            const submissionData = {
                activity_id: activityDetail.id,
                name: formData.name.trim(),
                phone: formData.phone.trim(),
                province,
                city,
                form_data: {
                    full_address,
                    submit_time: new Date().toISOString()
                }
            };

            const response = await registrationApi.store(submissionData);

            if (response.success) {
                setRegistration(response.data);
                onSuccess?.(response.data, '报名成功！');
                return response.data;
            } else {
                throw new Error(response.message || '报名失败');
            }
        } catch (error: any) {
            const context = {
                action: 'submitRegistration',
                message: error?.statusCode === 422 ? '请检查填写信息' : '报名失败，请重试'
            };
            onError?.(error, context);
            throw error;
        } finally {
            updateLoadingState('submitting', false);
        }
    }, [activityDetail, parseAddress, onSuccess, onError, updateLoadingState]);

    // 发起支付
    const initiatePayment = useCallback(async (amount: number) => {
        if (!registration?.id) {
            throw new Error('请先完成报名');
        }

        if (amount <= 0) {
            throw new Error('支付金额必须大于0');
        }

        updateLoadingState('paying', true);

        try {
            const paymentData = {
                registration_id: registration.id,
                amount
            };

            const response = await registrationApi.createPayment(paymentData);

            if (response.success) {
                const paymentResult = response.data;
                await handleWechatPay(paymentResult);
                return paymentResult;
            } else {
                throw new Error(response.message || '支付发起失败');
            }

        } catch (error: any) {
            onError?.(error, { action: 'initiatePayment', message: '支付失败，请重试' });
            throw error;
        } finally {
            updateLoadingState('paying', false);
        }
    }, [registration, onError, updateLoadingState]);

    // 调用微信支付
    const handleWechatPay = useCallback(async (paymentResult: PaymentResult) => {
        try {
            // 类型安全的支付参数提取
            const paymentParams = paymentResult as any;
            
            // 基本的参数验证
            if (!paymentParams.timeStamp || !paymentParams.nonceStr) {
                throw new Error('支付参数不完整');
            }

            await requestPayment({
                timeStamp: paymentParams.timeStamp,
                nonceStr: paymentParams.nonceStr,
                package: paymentParams.package,
                signType: paymentParams.signType,
                paySign: paymentParams.paySign
            });

            onPaymentSuccess?.(paymentResult, '支付成功！');
        } catch (error: any) {
            const isCanceled = error.errMsg?.includes('cancel');
            const message = isCanceled ? '支付已取消' : '支付失败';
            
            if (!isCanceled) {
                onError?.(error, { action: 'wechatPay', message });
            }
            throw error;
        }
    }, [onPaymentSuccess, onError]);

    // 查询支付状态
    const checkPaymentStatus = useCallback(async (paymentId: string) => {
        updateLoadingState('checking', true);
        try {
            const response = await registrationApi.getPaymentStatus(paymentId);
            if (response.success) {
                return response.data;
            }
            throw new Error(response.message || '查询支付状态失败');
        } catch (error) {
            onError?.(error, { action: 'checkPaymentStatus' });
            throw error;
        } finally {
            updateLoadingState('checking', false);
        }
    }, [onError, updateLoadingState]);

    // 取消报名
    const cancelRegistration = useCallback(async () => {
        if (!registration?.id) {
            throw new Error('没有找到报名记录');
        }

        updateLoadingState('canceling', true);
        try {
            const response = await registrationApi.cancel(registration.id);
            if (response.success) {
                setRegistration(null);
                return true;
            } else {
                throw new Error(response.message || '取消报名失败');
            }
        } catch (error) {
            onError?.(error, { action: 'cancelRegistration' });
            throw error;
        } finally {
            updateLoadingState('canceling', false);
        }
    }, [registration, onError, updateLoadingState]);

    return {
        // 状态
        ...loadingState,
        registration,

        // 方法
        checkStatus,
        submitRegistration,
        initiatePayment,
        checkPaymentStatus,
        cancelRegistration,

        // 工具方法
        parseAddress
    };
};

// 流程管理 Hook 配置项
interface UseRegistrationFlowOptions {
    activityDetail: ActivityDetail;
}

/**
 * 报名功能的流程管理 Hook
 * @description 管理完整的报名流程状态机，实际是对基础操作的封装
 */
const useRegistrationFlow = ({ activityDetail }: UseRegistrationFlowOptions) => {
    // 当前阶段
    const [currentStep, setCurrentStep] = useState<RegistrationStep>(RegistrationStep.INITIAL);
    // 已完成步骤
    const [completedSteps, setCompletedSteps] = useState<Set<string>>(new Set());

    // 标记步骤完成
    const markStepCompleted = useCallback((step: string) => {
        setCompletedSteps(prev => new Set([...prev, step]));
    }, []);

    // 调用基础操作 hook
    const registrationHook = useRegistration({
        activityDetail,
        // 检测成功后执行
        onCheckedSuccess: (statusCode) => {
            if (statusCode === RegistrationStatusCode.SUCCESS) {
                markStepCompleted('check');
                setCurrentStep(RegistrationStep.FORM);
            }
        },
        // 报名成功后执行
        onSuccess: (_reg) => {
            markStepCompleted('registration');
            // 金额大于零调用支付
            if (activityDetail.registration_fee > 0) {
                setCurrentStep(RegistrationStep.PAYMENT);
            } else {
                setCurrentStep(RegistrationStep.SUCCESS);
            }
        },
        // 支付成功后执行
        onPaymentSuccess: () => {
            markStepCompleted('payment');
            setCurrentStep(RegistrationStep.SUCCESS);
        }
    });

    // 检测报名资格
    const handleCheckStatus = useCallback(async () => {
        const statusCode = await registrationHook.checkStatus();
        return statusCode;
    }, [registrationHook.checkStatus]);

    // 处理表单提交
    const handleFormSubmit = useCallback(async (formData: RegistrationFormData) => {
        return await registrationHook.submitRegistration(formData);
    }, [registrationHook.submitRegistration]);

    // 处理支付
    const handlePayment = useCallback(async () => {
        if (activityDetail.registration_fee === 0) {
            throw new Error('当前活动无需支付');
        }
        
        if (activityDetail.registration_fee <= 0) {
            throw new Error('支付金额配置错误');
        }
        
        return await registrationHook.initiatePayment(activityDetail.registration_fee);
    }, [registrationHook.initiatePayment, activityDetail.registration_fee]);

    // 重置流程
    const resetFlow = useCallback(() => {
        setCurrentStep(RegistrationStep.INITIAL);
        setCompletedSteps(new Set());
    }, []);

    return {
        // 状态
        currentStep,
        completedSteps,
        ...registrationHook,
        requirePayment: activityDetail.registration_fee > 0,
        paymentAmount: activityDetail.registration_fee,
        
        // 方法
        handleCheckStatus,
        handleFormSubmit,
        handlePayment,
        resetFlow,
        
        // 计算属性
        isFormCompleted: completedSteps.has('registration'),
        isPaymentCompleted: completedSteps.has('payment'),
        isFlowCompleted: currentStep === RegistrationStep.SUCCESS,
        canSubmitForm: currentStep === RegistrationStep.FORM,
        canPay: currentStep === RegistrationStep.PAYMENT && activityDetail.registration_fee > 0
    };
};

export {
    useRegistration,
    useRegistrationFlow
};