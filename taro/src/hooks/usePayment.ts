// import { useState, useCallback, useRef } from 'react';
// import Taro from '@tarojs/taro';

// /**
//  * 支付状态枚举
//  */
// export const PAYMENT_STATUS = {
//   IDLE: 'idle',
//   CREATING_ORDER: 'creating_order',
//   PREPARING_PAYMENT: 'preparing_payment',
//   PROCESSING_PAYMENT: 'processing_payment',
//   SUCCESS: 'success',
//   FAILED: 'failed',
//   CANCELLED: 'cancelled'
// };

// /**
//  * 错误类型枚举
//  */
// export const PAYMENT_ERROR_TYPE = {
//   ORDER_CREATION_FAILED: 'ORDER_CREATION_FAILED',
//   PAYMENT_PARAMS_FAILED: 'PAYMENT_PARAMS_FAILED',
//   PAYMENT_FAILED: 'PAYMENT_FAILED',
//   USER_CANCELLED: 'USER_CANCELLED',
//   NETWORK_ERROR: 'NETWORK_ERROR',
//   UNKNOWN_ERROR: 'UNKNOWN_ERROR'
// };

// /**
//  * 通用支付流程 Hook
//  * @param {object} config - 配置对象
//  * @param {function} config.createOrderFn - 创建业务订单的异步函数
//  * @param {function} config.initiatePaymentFn - 发起支付的异步函数
//  * @param {function} [config.onPaymentSuccess] - 支付成功回调
//  * @param {function} [config.onPaymentError] - 支付失败回调
//  * @param {function} [config.onPaymentCancel] - 支付取消回调
//  * @param {object} [config.messages] - 自定义提示消息
//  * @param {boolean} [config.autoShowToast=true] - 是否自动显示Toast提示
//  */
// export const usePayment = ({
//   createOrderFn,
//   initiatePaymentFn,
//   onPaymentSuccess,
//   onPaymentError,
//   onPaymentCancel,
//   messages = {},
//   autoShowToast = true
// }) => {
//   const [status, setStatus] = useState(PAYMENT_STATUS.IDLE);
//   const [error, setError] = useState(null);
//   const [orderInfo, setOrderInfo] = useState(null);
  
//   // 使用 ref 防止重复请求
//   const isProcessingRef = useRef(false);
  
//   // 默认提示消息
//   const defaultMessages = {
//     creatingOrder: '正在创建订单...',
//     preparingPayment: '正在准备支付...',
//     paymentSuccess: '支付成功！',
//     paymentCancelled: '支付已取消',
//     paymentFailed: '支付失败，请重试',
//     orderCreationFailed: '创建订单失败，请重试',
//     networkError: '网络异常，请检查网络连接'
//   };
  
//   const finalMessages = { ...defaultMessages, ...messages };

//   /**
//    * 创建错误对象
//    */
//   const createError = (type, message, originalError = null) => ({
//     type,
//     message,
//     originalError,
//     timestamp: Date.now()
//   });

//   /**
//    * 显示加载提示
//    */
//   const showLoading = (title) => {
//     Taro.showLoading({ title, mask: true });
//   };

//   /**
//    * 显示Toast提示
//    */
//   const showToast = (title, icon = 'none') => {
//     if (autoShowToast) {
//       Taro.showToast({ title, icon });
//     }
//   };

//   /**
//    * 验证支付参数
//    */
//   const validatePaymentParams = (params) => {
//     const requiredFields = ['timeStamp', 'nonceStr', 'package', 'signType', 'paySign'];
//     return requiredFields.every(field => params?.[field]);
//   };

//   /**
//    * 处理支付成功
//    */
//   const handlePaymentSuccess = useCallback((result) => {
//     setStatus(PAYMENT_STATUS.SUCCESS);
//     showToast(finalMessages.paymentSuccess, 'success');
//     onPaymentSuccess?.(result);
//   }, [onPaymentSuccess, finalMessages.paymentSuccess]);

//   /**
//    * 处理支付错误
//    */
//   const handlePaymentError = useCallback((error) => {
//     setError(error);
//     setStatus(PAYMENT_STATUS.FAILED);
    
//     let message = finalMessages.paymentFailed;
//     if (error.type === PAYMENT_ERROR_TYPE.NETWORK_ERROR) {
//       message = finalMessages.networkError;
//     } else if (error.type === PAYMENT_ERROR_TYPE.ORDER_CREATION_FAILED) {
//       message = finalMessages.orderCreationFailed;
//     }
    
//     showToast(message);
//     onPaymentError?.(error);
//   }, [onPaymentError, finalMessages]);

//   /**
//    * 处理支付取消
//    */
//   const handlePaymentCancel = useCallback(() => {
//     setStatus(PAYMENT_STATUS.CANCELLED);
//     showToast(finalMessages.paymentCancelled);
//     onPaymentCancel?.();
//   }, [onPaymentCancel, finalMessages.paymentCancelled]);

//   /**
//    * 重置状态
//    */
//   const reset = useCallback(() => {
//     setStatus(PAYMENT_STATUS.IDLE);
//     setError(null);
//     setOrderInfo(null);
//     isProcessingRef.current = false;
//   }, []);

//   /**
//    * 处理支付的核心函数
//    * @param {*} entityId - 业务实体的ID
//    * @param {object} [options] - 额外选项
//    */
//   const handlePayment = useCallback(async (entityId, options = {}) => {
//     // 防止重复请求
//     if (isProcessingRef.current) {
//       console.warn('Payment is already in progress');
//       return { success: false, error: 'Payment already in progress' };
//     }

//     if (!entityId) {
//       const error = createError(PAYMENT_ERROR_TYPE.UNKNOWN_ERROR, '缺少必要参数');
//       handlePaymentError(error);
//       return { success: false, error };
//     }

//     isProcessingRef.current = true;
//     setError(null);

//     try {
//       // 步骤一：创建业务订单
//       setStatus(PAYMENT_STATUS.CREATING_ORDER);
//       showLoading(finalMessages.creatingOrder);
      
//       const orderResponse = await createOrderFn(entityId, options);
//       const orderId = orderResponse?.data?.id || orderResponse?.id;
      
//       if (!orderId) {
//         throw createError(
//           PAYMENT_ERROR_TYPE.ORDER_CREATION_FAILED,
//           '创建订单失败，未获取到订单ID',
//           orderResponse
//         );
//       }

//       // 保存订单信息
//       setOrderInfo({ id: orderId, entityId, ...orderResponse.data });

//       // 步骤二：获取支付参数
//       setStatus(PAYMENT_STATUS.PREPARING_PAYMENT);
//       showLoading(finalMessages.preparingPayment);
      
//       const paymentParamsResponse = await initiatePaymentFn(orderId);
//       const paymentParams = paymentParamsResponse?.data || paymentParamsResponse;
      
//       if (!validatePaymentParams(paymentParams)) {
//         throw createError(
//           PAYMENT_ERROR_TYPE.PAYMENT_PARAMS_FAILED,
//           '获取支付参数失败',
//           paymentParamsResponse
//         );
//       }

//       Taro.hideLoading();

//       // 步骤三：调用微信支付
//       setStatus(PAYMENT_STATUS.PROCESSING_PAYMENT);
      
//       await Taro.requestPayment({
//         timeStamp: paymentParams.timeStamp,
//         nonceStr: paymentParams.nonceStr,
//         package: paymentParams.package,
//         signType: paymentParams.signType,
//         paySign: paymentParams.paySign,
//       });

//       // 支付成功
//       const result = { success: true, orderId, orderInfo: orderInfo };
//       handlePaymentSuccess(result);
//       return result;

//     } catch (err) {
//       Taro.hideLoading();
      
//       // 判断错误类型
//       let error;
//       if (err.type) {
//         // 已经是格式化的错误
//         error = err;
//       } else if (err.errMsg?.includes('cancel')) {
//         // 用户取消支付
//         handlePaymentCancel();
//         return { success: false, cancelled: true };
//       } else if (err.errMsg?.includes('network')) {
//         // 网络错误
//         error = createError(PAYMENT_ERROR_TYPE.NETWORK_ERROR, '网络连接异常', err);
//       } else {
//         // 未知错误
//         error = createError(
//           PAYMENT_ERROR_TYPE.UNKNOWN_ERROR,
//           err.message || '支付过程中发生未知错误',
//           err
//         );
//       }

//       handlePaymentError(error);
//       return { success: false, error };
      
//     } finally {
//       isProcessingRef.current = false;
//     }
//   }, [
//     createOrderFn,
//     initiatePaymentFn,
//     finalMessages,
//     handlePaymentSuccess,
//     handlePaymentError,
//     handlePaymentCancel,
//     orderInfo
//   ]);

//   /**
//    * 重试支付（使用已有订单）
//    */
//   const retryPayment = useCallback(async () => {
//     if (!orderInfo?.id) {
//       const error = createError(PAYMENT_ERROR_TYPE.UNKNOWN_ERROR, '没有可重试的订单');
//       handlePaymentError(error);
//       return { success: false, error };
//     }

//     return handlePayment(orderInfo.entityId);
//   }, [orderInfo, handlePayment]);

//   return {
//     // 核心方法
//     handlePayment,
//     retryPayment,
//     reset,
    
//     // 状态
//     status,
//     isLoading: [
//       PAYMENT_STATUS.CREATING_ORDER,
//       PAYMENT_STATUS.PREPARING_PAYMENT,
//       PAYMENT_STATUS.PROCESSING_PAYMENT
//     ].includes(status),
//     isIdle: status === PAYMENT_STATUS.IDLE,
//     isSuccess: status === PAYMENT_STATUS.SUCCESS,
//     isFailed: status === PAYMENT_STATUS.FAILED,
//     isCancelled: status === PAYMENT_STATUS.CANCELLED,
    
//     // 数据
//     error,
//     orderInfo,
    
//     // 常量
//     PAYMENT_STATUS,
//     PAYMENT_ERROR_TYPE
//   };
// };