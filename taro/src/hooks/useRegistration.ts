import { useState, useCallback } from "react";
import { ActivityDetail, RegistrationItem, RegistrationFormData } from "@/types/ui";
import registrationApi from "@/api/registration";
import { parseAddress } from "@/utils/common";
import usePayment from "@/hooks/usePayment";

/**
 * 报名状态码
 * @description 用于表示报名状态的检查结果
 * @param SUCCESS - 活动报名正常，可以报名
 * @param ALREADY_REGISTERED - 已经报名
 * @param PENDING_PAYMENT - 存在尚未付款的报名信息
 * @param NOT_ELIGIBLE - 不符合报名条件
 * @param ACTIVITY_NOT_STARTED - 活动报名未开始
 * @param ACTIVITY_ENDED - 活动报名已结束
 * @param ACTIVITY_FULL - 活动名额已满
 */
export enum RegistrationStatusCode {
    SUCCESS = 'success',
    ALREADY_REGISTERED = 'already_registered',
    PENDING_PAYMENT = 'pending_payment',
    NOT_ELIGIBLE = 'not_eligible',
    ACTIVITY_NOT_STARTED = 'activity_not_started', 
    ACTIVITY_ENDED = 'activity_ended',      
    ACTIVITY_FULL = 'activity_full'
}

/**
 * 报名步骤枚举
 * @description 用于表示报名流程的当前步骤，UI 层渲染不一样的界面
 * @param INITIAL - 初始阶段，渲染立即报名按钮
 * @param FORM - 填写报名表单，渲染报名表单
 * @param PAYMENT - 支付报名费用，渲染带有支付按钮的落地信息
 * @param SUCCESS - 报名成功，渲染报名成功页面
 */
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
    canceling: boolean;
}

interface UseRegistrationOptions {
    activityDetail: ActivityDetail;
    onCheckedSuccess?: (statusCode: RegistrationStatusCode) => void;
    onSuccess?: (registration: RegistrationItem, message?: string) => void;
    onError?: (error: any, context?: { action: string; message?: string }) => void;
}

/**
 * 报名功能的基础操作 Hook
 * @description 处理单个报名操作（检测、提交、支付、取消等）
 * @param activityDetail - 活动详情
 * @param onCheckedSuccess - 检测成功后的回调
 * @param onSuccess - 报名成功后的回调
 * @param onError - 发生错误后的回调    
 */
const useRegistration = ({
    activityDetail,
    onCheckedSuccess,
    onSuccess,
    onError
}: UseRegistrationOptions) => {
    // 加载状态集合
    const [loadingState, setLoadingState] = useState<LoadingState>({
        checking: false,
        submitting: false,
        canceling: false
    });
    
    // 接口返回的报名信息
    const [registration, setRegistration] = useState<RegistrationItem | null>(null);

    // 更新单个加载状态的工具函数
    const updateLoadingState = useCallback((key: keyof LoadingState, value: boolean) => {
        setLoadingState(prev => ({ ...prev, [key]: value }));
    }, []);

    // 基于活动信息检查状态，返回状态码
    const checkActivityStatus = useCallback((): { 
        statusCode: RegistrationStatusCode,
        canRegister: boolean 
    } => {
        const now = new Date();
        // 活动报名开始和结束时间
        const startTime = new Date(activityDetail.registration_start_at);
        const endTime = new Date(activityDetail.registration_end_at);

        // 活动尚未开始
        if (startTime && now < startTime) {
            return {
                statusCode: RegistrationStatusCode.ACTIVITY_NOT_STARTED,
                canRegister: false
            };
        }

        // 活动已经结束
        if (endTime && endTime.getTime() > 0 && now > endTime) {
            return {
                statusCode: RegistrationStatusCode.ACTIVITY_ENDED,
                canRegister: false
            };
        }

        // 活动已经满员
        if (
            activityDetail.max_participants > 0 && 
            activityDetail.current_participants >= activityDetail.max_participants
        ) {
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
            // 1. 先检查活动状态，返回一个报名资格的布尔值和状态码
            const activityCheck = checkActivityStatus();

            if (!activityCheck.canRegister) {
                // 无法报名，直接返回原因状态码
                return activityCheck.statusCode;
            }
                
            // 2. 活动状态正常，检查用户报名单状态
            const { data } = await registrationApi.status(activityDetail.id);

            let statusCode: RegistrationStatusCode;

            if (data?.value === 'approved') {
                statusCode = RegistrationStatusCode.ALREADY_REGISTERED;
            } else if (data?.value === 'pending') {
                statusCode = RegistrationStatusCode.PENDING_PAYMENT;
            } else if (data?.value === 'rejected' || data?.value === 'cancelled') {
                statusCode = RegistrationStatusCode.NOT_ELIGIBLE;
            } else {
                statusCode = RegistrationStatusCode.SUCCESS;
            }

            // 只有成功状态才执行检查通过的回调
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
            // 格式化出地址的相关参数
            const { province, city, full_address } = parseAddress(formData.address);
            // 构造提交到接口的参数
            // 2025.06.30 添加备注字段
            const submissionData = {
                activity_id: activityDetail.id,
                name: formData.name.trim(),
                phone: formData.phone.trim(),
                province,
                city,
                remarks: formData.remarks.trim(),
                form_data: {
                    full_address,
                    submit_time: new Date().toISOString()
                }
            };

            // 提交数据
            const response = await registrationApi.store(submissionData);
            if (response.success) {
                // 成功后将返回的报名信息设置到状态数据内
                setRegistration(response.data);
                // 执行成功的回调方法
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
 * @param activityDetail - 活动详情
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

    // 实例化支付 hook
    const {
        startPayment,
        isPaying,
        paymentError
    } = usePayment();

    // 实例化基础操作 hook
    const {
        registration,
        checkStatus,
        submitRegistration,
        checking: isChecking,
        submitting: isSubmitting
    } = useRegistration({
        activityDetail,
        // 检测成功后执行
        onCheckedSuccess: (statusCode) => {
            if (statusCode === RegistrationStatusCode.SUCCESS) {
                // 完成检测阶段
                markStepCompleted('check');
                // 检测通过，进入表单阶段
                setCurrentStep(RegistrationStep.FORM);
            }
        },
        // 报名成功后执行
        onSuccess: (_reg) => {
            // 完成提交阶段
            markStepCompleted('registration');
            if (parseFloat(activityDetail.registration_fee) > 0) {
                // 进入支付阶段
                setCurrentStep(RegistrationStep.PAYMENT);
            } else {
                // 直接报名成功
                markStepCompleted('payment');
                setCurrentStep(RegistrationStep.SUCCESS);
            }
        }
    });

    // 检测报名资格
    const handleCheckStatus = useCallback(async () => {
        const statusCode = await checkStatus();
        return statusCode;
    }, [checkStatus]);

    // 处理表单提交
    const handleFormSubmit = useCallback(async (formData: RegistrationFormData) => {
        return await submitRegistration(formData);
    }, [submitRegistration]);

    // 处理支付
    const handlePayment = useCallback(async () => {
        // 验证报名信息
        if (!registration?.id) {
            throw new Error('未找到报名信息，无法支付');
        }

        // 调用通用支付 Hook 的核心方法
        const result = await startPayment({
            orderId: registration.id,
            orderType: 'activity'
        });

        if (result.success) {
            markStepCompleted('payment');
            setCurrentStep(RegistrationStep.SUCCESS);
        } else {
            // 错误处理已在 usePayment Hook 内部完成（如 toast 提示）
            // 这里可以根据需要做一些额外的UI处理，比如不清空支付页面
        }
    }, [startPayment, registration, markStepCompleted, setCurrentStep]);

    // 重置流程
    const resetFlow = useCallback(() => {
        setCurrentStep(RegistrationStep.INITIAL);
        setCompletedSteps(new Set());
    }, []);

    return {
        // 流程状态
        currentStep,
        completedSteps,

        // 报名相关
        registration,
        isChecking,
        isSubmitting,

        // 支付相关
        isPaying,
        paymentError,

        // 支付相关
        requirePayment: parseFloat(activityDetail.registration_fee) > 0,
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
        canPay: currentStep === RegistrationStep.PAYMENT && parseFloat(activityDetail.registration_fee) > 0
    };
};

export {
    useRegistration,
    useRegistrationFlow
};