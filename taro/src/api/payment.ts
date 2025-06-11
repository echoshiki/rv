import { http } from "@/utils/request";
import {
    ApiResponse,
    PaymentParam,
    PaymentStatus,
    PaymentDetail
} from "@/types/api";

const PAYMENT_API = '/api/v1/payments';

const paymentApi = {
    // 支付状态查询
    checkPaymentStatus: (outTradeNo: string): Promise<ApiResponse<PaymentStatus>> => {
        return http.get(`${PAYMENT_API}/status`, {
            params: { out_trade_no: outTradeNo }
        });
    },

    // 房车订单支付
    // 返回前端参数: appId、timeStamp、nonceStr、package、signType、paySign
    initiateRvOrderPayment: (orderId: string): Promise<ApiResponse<PaymentParam>> => {
        return http.post(`${PAYMENT_API}/rv-orders/${orderId}/pay`);
    },

    // 活动报名支付
    initiateActivityPayment: (orderId: string): Promise<ApiResponse<PaymentParam>> => {
        return http.post(`${PAYMENT_API}/registrations/${orderId}/pay`);
    },

    // 获取支付详情
    getPaymentDetail: (paymentId: string): Promise<ApiResponse<PaymentDetail>> => {
        return http.get(`${PAYMENT_API}/${paymentId}`);
    }
}

export default paymentApi;