import { useState, useCallback } from "react";
import { useRegistration } from "./useRegistration";
import { RegistrationFormData } from "@/types/ui";

type RegistrationFlowStep = 
    | 'initial'                // 任何检查之前的初始状态
    | 'checkingStatus'         // 正在积极检查报名状态
    | 'statusCheckError'       // 状态检查过程中发生错误
    | 'form'                   // 用户可以填写报名表单的阶段
    | 'submittingForm'         // 表单提交中 (可选，可由 useRegistration 的 `submitting` 状态覆盖)
    | 'payment'                // 支付阶段 (如果需要)
    | 'processingPayment'      // 支付处理中 (可选，可由 useRegistration 的 `paying` 状态覆盖)
    | 'success'                // 报名 (及支付，如有) 成功
    | 'alreadyRegistered'      // 用户已报名该活动
    | 'pendingExistingPayment';// 用户已有报名信息待支付
    
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
    const [currentStep, setCurrentStep] = useState<'initial' | 'form' | 'payment' | 'success'>('initial');
    // 完成阶段，追踪已完成的阶段，注册、支付、成功等
    const [completedSteps, setCompletedSteps] = useState<Set<string>>(new Set());
    // 用于向用户显示的状态消息 (例如错误提示、成功提示等)
    const [statusMessage, setStatusMessage] = useState<string>("");

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