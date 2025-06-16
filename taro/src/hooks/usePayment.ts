import { useState, useCallback } from 'react';
import Taro from '@tarojs/taro';
import paymentApi from '@/api/payment';

/**
 * 轮询支付状态
 * @param outTradeNo 商户订单号
 * @param maxAttempts 最大轮询次数
 * @param interval 轮询间隔时间
 * @returns 支付状态
 */
const pollPaymentStatus = (outTradeNo: string, maxAttempts = 10, interval = 2000): Promise<boolean> => {
    // 轮询次数
    let attempts = 0;
    return new Promise((resolve, reject) => {
        const executePoll = async () => {
            // 轮询次数递增
            attempts++;
            try {
                const { data } = await paymentApi.checkPaymentStatus(outTradeNo);
                if (data.status === 'paid') {
                    return resolve(true); // 支付成功，停止轮询
                } else if (attempts >= maxAttempts) {
                    return reject(new Error('支付结果确认超时')); // 达到最大次数
                } else {
                    setTimeout(executePoll, interval); // 继续轮询
                }
            } catch (error) {
                return reject(error); // API查询失败
            }
        };
        executePoll();
    });
};

interface PaymentOptions {
    orderId: string;
    orderType: 'rv' | 'activity';
}

interface PaymentResult {
    success: boolean;
    orderId?: string;
}

interface UsePaymentReturn {
    isPaying: boolean;
    paymentError: string | null;
    // 返回一个最终结果的 Promise
    startPayment: (options: PaymentOptions) => Promise<PaymentResult>;
}

const usePayment = (): UsePaymentReturn => {
    const [isPaying, setIsPaying] = useState(false);
    const [paymentError, setPaymentError] = useState<string | null>(null);

    // 开始支付流程
    const startPayment = useCallback(async (options: PaymentOptions): Promise<PaymentResult> => {
        setIsPaying(true);
        setPaymentError(null);

        Taro.showLoading({ title: '正在准备支付...', mask: true });

        try {
            // 根据订单类型构建支付请求
            const paymentParamsResponse = await (options.orderType === 'rv'
                ? paymentApi.initiateRvOrderPayment(options.orderId)
                : paymentApi.initiateActivityPayment(options.orderId));
            
            const paymentParams = paymentParamsResponse.data;
            if (!paymentParams?.paySign) throw new Error('获取支付参数失败');
                
            Taro.hideLoading();

            // 拉起微信支付
            await Taro.requestPayment({
                timeStamp: paymentParams.timestamp,
                nonceStr: paymentParams.nonceStr,
                package: paymentParams.package,
                signType: paymentParams.signType,
                paySign: paymentParams.paySign,
                fail: function (res) {
                    console.log(res);
                },
                success: function (res) {
                    console.log(res);
                }
            });

            // 内置轮询，确认后端支付状态
            Taro.showLoading({ title: '正在确认支付结果...', mask: true });

            const outTradeNo = paymentParams.out_trade_no;
            if (!outTradeNo) throw new Error('支付参数缺少商户订单号');

            // 轮询支付状态
            await pollPaymentStatus(outTradeNo);

            setIsPaying(false);
            Taro.hideLoading();
            return { success: true, orderId: options.orderId };

        } catch (error) {
            setIsPaying(false);
            Taro.hideLoading();
            setPaymentError(error);

            // 统一处理所有错误（API错误、用户取消、轮询超时）
            const isCancel = error.errMsg?.includes('cancel');
            if (!isCancel) {
                Taro.showToast({ title: error.message || '支付失败', icon: 'none' });
            } else {
                Taro.showToast({ title: '支付已取消', icon: 'none' });
            }

            return { success: false, orderId: options.orderId };
        } 
    }, []);

    return {
        isPaying,
        paymentError,
        startPayment
    };
};

export default usePayment;