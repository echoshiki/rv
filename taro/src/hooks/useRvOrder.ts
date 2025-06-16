import { useState, useCallback } from 'react';
import Taro from '@tarojs/taro';
import { createRvOrder } from '@/api/rv'; // 引入API
import { RvOrderItem } from '@/types/api'; // 假设您有订单的类型定义
import usePayment from '@/hooks/usePayment';
import { checkLogin } from '@/utils/auth';

interface UseRvOrderReturn {
    isCreating: boolean;
    createOrder: (rvId: string) => Promise<{ success: boolean; order?: RvOrderItem }>;
}

const useRvOrder = (): UseRvOrderReturn => {
    const [isCreating, setIsCreating] = useState(false);

    const createOrder = useCallback(async (rvId: string) => {
        setIsCreating(true);
        Taro.showLoading({ title: '正在创建订单...', mask: true });

        try {
            const response = await createRvOrder(rvId);
            if (response.success && response.data) {
                Taro.hideLoading();
                return { success: true, order: response.data };
            }
            throw new Error(response.message || '创建订单失败');
        } catch (error) {
            Taro.hideLoading();
            Taro.showToast({ title: error.message || '创建订单出错', icon: 'none' });
            return { success: false };
        } finally {
            setIsCreating(false);
        }
    }, []);

    return { isCreating, createOrder };
};

interface UseBookingFlowReturn {
    isProcessing: boolean; // 一个统一的加载状态
    startBookingFlow: (rvId: string) => Promise<void>;
}

const useBookingFlow = (): UseBookingFlowReturn => {
    const { isCreating, createOrder } = useRvOrder();
    const { isPaying, startPayment } = usePayment();

    const startBookingFlow = useCallback(async (rvId: string) => {
        // 检测是否登录
        if (!checkLogin()) return;

        // 步骤一：创建业务订单
        const orderResult = await createOrder(rvId);

        // 如果订单创建成功，并且有订单ID，则继续支付
        if (orderResult.success && orderResult.order?.id) {
            // 步骤二：调用支付Hook
            const paymentResult = await startPayment({
                orderId: orderResult.order.id,
                orderType: 'rv',
            });

            // 步骤三：根据最终支付结果进行后续操作
            if (paymentResult.success) {
                Taro.showToast({ title: '预订成功！', icon: 'success', duration: 1500 });
                setTimeout(() => {
                    Taro.redirectTo({
                        url: `/pages/order-detail/index?id=${paymentResult.orderId}`
                    });
                }, 1500);
            }
        }
        // 所有的错误提示都已在各自的底层Hook中处理，这里无需再写 catch
    }, [createOrder, startPayment]);

    return {
        // 将两个加载状态合并为一个，方便UI使用
        isProcessing: isCreating || isPaying,
        startBookingFlow,
    };
};

export {
    useRvOrder,
    useBookingFlow
};
