import { useState, useCallback } from 'react';
import Taro from '@tarojs/taro';
import { http } from '@/utils/request';

interface PaymentOptions {
  orderId: string | number;
  orderType: 'rv' | 'activity'; // 订单类型
  amount: number;
  description?: string;
}

interface PaymentStatus {
  status: 'pending' | 'paid' | 'failed';
  paidAt?: string;
  transactionId?: string;
}

interface UsePaymentReturn {
  isPaying: boolean;
  paymentError: string | null;
  startPayment: (options: PaymentOptions) => Promise<void>;
  checkPaymentStatus: (outTradeNo: string) => Promise<PaymentStatus>;
}

export const usePayment = (): UsePaymentReturn => {
  const [isPaying, setIsPaying] = useState(false);
  const [paymentError, setPaymentError] = useState<string | null>(null);

  // 开始支付流程
  const startPayment = useCallback(async (options: PaymentOptions) => {
    try {
      setIsPaying(true);
      setPaymentError(null);

      // 根据订单类型构建支付请求
      const endpoint = options.orderType === 'rv' 
        ? `/api/v1/rv-orders/${options.orderId}/pay`
        : `/api/v1/activities/${options.orderId}/pay`;

      // 获取支付参数
      const { data: paymentParams } = await http.post(endpoint);

      // 调用微信支付
      await Taro.requestPayment({
        ...paymentParams,
        success: () => {
          // 支付成功后的处理
          Taro.showToast({
            title: '支付成功',
            icon: 'success',
            duration: 2000
          });
        },
        fail: (err) => {
          // 支付失败的处理
          setPaymentError(err.errMsg || '支付失败');
          Taro.showToast({
            title: '支付失败',
            icon: 'error',
            duration: 2000
          });
        }
      });
    } catch (error) {
      setPaymentError(error.message || '支付过程发生错误');
      Taro.showToast({
        title: '支付失败',
        icon: 'error',
        duration: 2000
      });
    } finally {
      setIsPaying(false);
    }
  }, []);

  // 轮询检查支付状态
  const checkPaymentStatus = useCallback(async (outTradeNo: string): Promise<PaymentStatus> => {
    try {
      const { data } = await http.get('/api/v1/payments/status', {
        params: { out_trade_no: outTradeNo }
      });
      return data;
    } catch (error) {
      throw new Error('查询支付状态失败');
    }
  }, []);

  return {
    isPaying,
    paymentError,
    startPayment,
    checkPaymentStatus
  };
};

// 使用示例：
/*
const PaymentComponent = () => {
  const { isPaying, paymentError, startPayment, checkPaymentStatus } = usePayment();

  const handlePayment = async () => {
    try {
      await startPayment({
        orderId: '123',
        orderType: 'rv',
        amount: 1000,
        description: '房车预订定金'
      });

      // 支付成功后，可以开始轮询检查支付状态
      const status = await checkPaymentStatus('out_trade_no');
      if (status.status === 'paid') {
        // 处理支付成功后的业务逻辑
      }
    } catch (error) {
      console.error('支付失败:', error);
    }
  };

  return (
    <View>
      <Button loading={isPaying} onClick={handlePayment}>
        立即支付
      </Button>
      {paymentError && <Text>{paymentError}</Text>}
    </View>
  );
};
*/