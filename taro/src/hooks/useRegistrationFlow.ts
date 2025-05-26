import { useState, useCallback } from "react";
import { useRegistration } from "./useRegistration";
import { RegistrationFormData } from "@/types/ui";

interface UseRegistrationFlowOptions {
    activityId: string,
    // 是否需要支付
    requirePayment?: boolean,
    // 支付金额
    paymentAmount?: number
}

/**
 * 报名功能的流程管理 Hook
 * @description 管理完整的报名流程（表单 → 支付 → 成功），实际是对基础操作的一层封装，记录整个流程的完成阶段和当前阶段
 * @param options - 配置项
 */
const useRegistrationFlow = ({
    activityId,
    requirePayment = false,
    paymentAmount = 0
}: UseRegistrationFlowOptions) => {
    // 当前阶段，默认值表单阶段
    const [currentStep, setCurrentStep] = useState<'form' | 'payment' | 'success'>('form');
    // 完成阶段，追踪已完成的阶段，注册、支付、成功等
    const [completedSteps, setCompletedSteps] = useState<Set<string>>(new Set());

    // 调用基础操作 hook，传入活动ID、各阶段回调函数
    const {
        submitting,
        paying,
        registration,
        submitRegistration,
        initiatePayment
    } = useRegistration({
        activityId,
        onSuccess: (_reg) => {
            // 完成阶段：信息提交
            setCompletedSteps(prev => new Set([...prev, 'registration']));
            if (requirePayment) {
                // 需要支付，当前阶段：支付
                setCurrentStep('payment');
            } else {
                // 不需要支付，当前阶段：报名成功
                setCurrentStep('success');
            }
        },
        onPaymentSuccess: () => {
            // 完成阶段：支付
            setCompletedSteps(prev => new Set([...prev, 'payment']));
            // 当前阶段：报名成功
            setCurrentStep('success');
        }
    });

    // 处理表单提交
    const handleFormSubmit = useCallback(async (formData: RegistrationFormData) => {
        await submitRegistration(formData);
    }, [submitRegistration]);

    // 处理支付
    const handlePayment = useCallback(async () => {
        if (paymentAmount > 0) {
            await initiatePayment(paymentAmount);
        }
    }, [initiatePayment, paymentAmount]);

    // 重置流程
    const resetFlow = useCallback(() => {
        setCurrentStep('form');
        setCompletedSteps(new Set());
    }, []);

    return {
        // 状态
        currentStep,
        completedSteps,
        submitting,
        paying,
        registration,
        requirePayment,
        paymentAmount,
        
        // 方法
        handleFormSubmit,
        handlePayment,
        resetFlow,
        
        // 计算属性
        isFormCompleted: completedSteps.has('registration'),
        isPaymentCompleted: completedSteps.has('payment'),
        isFlowCompleted: currentStep === 'success'
    };
}

export {
    useRegistrationFlow
}