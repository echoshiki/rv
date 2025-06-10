import { http } from "@/utils/request";
import {
    ApiResponse,
    PaymentParam,
    PaymentStatus,
    PaymentDetail
} from "@/types/api";

const PAYMENT_API = '/api/v1/payments';
const RV_ORDER_API = '/api/v1/rv-orders';
const REGISTRATION_API = '/api/v1/registrations';

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
        return http.post(`${RV_ORDER_API}/${orderId}/pay`);
    },

    // 活动报名支付
    initiateActivityPayment: (orderId: string): Promise<ApiResponse<PaymentParam>> => {
        return http.post(`${REGISTRATION_API}/${orderId}/pay`);
    },

    // 获取支付详情
    getPaymentDetail: (paymentId: string): Promise<ApiResponse<PaymentDetail>> => {
        return http.get(`${PAYMENT_API}/${paymentId}`);
    }
}

export default paymentApi;