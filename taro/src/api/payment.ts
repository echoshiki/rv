import { http } from "@/utils/request";
import { 
    ApiResponse, 
    PaymentParam, 
    PaymentStatus,
    PaymentHistory,
    PaymentDetail
} from "@/types/api";

const PAYMENT_API = '/api/v1/payments';
const RV_ORDER_API = '/api/v1/rv-orders';
const ACTIVITY_API = '/api/v1/activities';

// 支付状态查询
const checkPaymentStatus = (outTradeNo: string): Promise<ApiResponse<PaymentStatus>> => {
  return http.get(`${PAYMENT_API}/status`, {
    params: { out_trade_no: outTradeNo }
  });
};

// 房车订单支付
const initiateRvOrderPayment = (orderId: string): Promise<ApiResponse<PaymentParam>> => {
  return http.post(`${RV_ORDER_API}/${orderId}/pay`);
};

// 活动报名支付
const initiateActivityPayment = (orderId: string): Promise<ApiResponse<PaymentParam>> => {
  return http.post(`${ACTIVITY_API}/${orderId}/pay`);
};

// 获取
const getPaymentHistory = (): Promise<ApiResponse<PaymentHistory[]>> => {
  return http.get(`${PAYMENT_API}/history`);
};

const getPaymentDetail = (paymentId: string): Promise<ApiResponse<PaymentDetail>> => {
  return http.get(`${PAYMENT_API}/${paymentId}`);
};

export {
    checkPaymentStatus,
    initiateRvOrderPayment,
    initiateActivityPayment,
    getPaymentHistory,
    getPaymentDetail
}