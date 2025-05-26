import { http } from "@/utils/request";
import { 
    ApiResponse, 
    RegistrationList, 
    RegistrationItem, 
    RegistrationSubmission,
    RegistrationStatus,
    PaymentData,
    PaymentResult,
} from "@/types/api";

const REGISTRATION_API = `/api/v1/registrations/`;
const PAYMENT_API = '/payments';

const registrationApi = {
    /**
     * 获取当前用户的报名列表
     * @returns 
     */
    list: (): Promise<ApiResponse<RegistrationList>> => {
        return http.get(`${REGISTRATION_API}my`);
    },

    /**
     * 提交报名信息
     * @param data - 报名信息
     * @returns 
     */
    store: (data: RegistrationSubmission): Promise<ApiResponse<RegistrationItem>> => {
        return http.post(`${REGISTRATION_API}`, data);
    },

    /**
     * 获取报名详情
     * @param id - 报名ID
     * @returns 
     */
    get: (id: string): Promise<ApiResponse<RegistrationItem>> => {
        return http.get(`${REGISTRATION_API}${id}`);
    },

    /**
     * 获取当前用户的活动报名状态
     * @param activityId - 活动ID
     * @returns 
     */
    status: (activityId: string): Promise<ApiResponse<RegistrationStatus | null>> => {
        return http.get(`${REGISTRATION_API}${activityId}/status`);
    },

    // 取消报名
    cancel: (id: string): Promise<ApiResponse<void>> => {
        return http.post(`${REGISTRATION_API}${id}/cancel`);
    },

    // 发起支付
    createPayment: (data: PaymentData): Promise<ApiResponse<PaymentResult>> => {
        return http.post(`${PAYMENT_API}`, data);
    },

    // 查询支付状态
    getPaymentStatus: (paymentId: string): Promise<ApiResponse<PaymentResult>> => {
        return http.get(`${PAYMENT_API}${paymentId}/status`);
    }
}

export default registrationApi;