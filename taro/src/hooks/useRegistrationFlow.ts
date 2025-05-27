import { useState, useCallback } from "react";
import { useRegistration } from "./useRegistration";
import { RegistrationFormData } from "@/types/ui";


// 定义步骤枚举
export enum RegistrationStep {
    INITIAL = 'initial',
    FORM = 'form',
    PAYMENT = 'payment',
    SUCCESS = 'success'
}
    
// 流程管理 Hook 配置项
interface UseRegistrationFlowOptions {
    activityId: string;
    requirePayment?: boolean;
    paymentAmount?: number;
}

/**
 * 报名功能的流程管理 Hook
 * @description 管理完整的报名流程状态机，实际是对基础操作的封装
 */
const useRegistrationFlow = ({
    activityId,
    requirePayment = false,
    paymentAmount = 0
}: UseRegistrationFlowOptions) => {
    const [currentStep, setCurrentStep] = useState<RegistrationStep>(RegistrationStep.INITIAL);
    const [completedSteps, setCompletedSteps] = useState<Set<string>>(new Set());

    // 标记步骤完成
    const markStepCompleted = useCallback((step: string) => {
        setCompletedSteps(prev => new Set([...prev, step]));
    }, []);

    // 调用基础操作 hook
    const registrationHook = useRegistration({
        activityId,
        onCheckedSuccess: (statusCode) => {
            if (statusCode === RegistrationStatusCode.SUCCESS) {
                markStepCompleted('check');
                setCurrentStep(RegistrationStep.FORM);
            }
        },
        onSuccess: (_reg) => {
            markStepCompleted('registration');
            if (requirePayment && paymentAmount > 0) {
                setCurrentStep(RegistrationStep.PAYMENT);
            } else {
                setCurrentStep(RegistrationStep.SUCCESS);
            }
        },
        onPaymentSuccess: () => {
            markStepCompleted('payment');
            setCurrentStep(RegistrationStep.SUCCESS);
        }
    });

    // 检测报名资格
    const handleCheckStatus = useCallback(async () => {
        const statusCode = await registrationHook.checkStatus();
        
        // 处理非成功状态
        if (statusCode !== RegistrationStatusCode.SUCCESS) {
            return statusCode;
        }
        
        return statusCode;
    }, [registrationHook.checkStatus]);

    // 处理表单提交
    const handleFormSubmit = useCallback(async (formData: RegistrationFormData) => {
        return await registrationHook.submitRegistration(formData);
    }, [registrationHook.submitRegistration]);

    // 处理支付
    const handlePayment = useCallback(async () => {
        if (!requirePayment) {
            throw new Error('当前活动无需支付');
        }
        
        if (paymentAmount <= 0) {
            throw new Error('支付金额配置错误');
        }
        
        return await registrationHook.initiatePayment(paymentAmount);
    }, [registrationHook.initiatePayment, requirePayment, paymentAmount]);

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
        requirePayment,
        paymentAmount,
        
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
        canPay: currentStep === RegistrationStep.PAYMENT && requirePayment
    };
};

export {
    useRegistrationFlow
}