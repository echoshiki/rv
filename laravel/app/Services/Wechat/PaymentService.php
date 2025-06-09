<?php

namespace App\Services\Wechat;

use App\Services\Wechat\Support\BaseWechatService;
use EasyWeChat\Pay\Application;
use Psr\Http\Message\ServerRequestInterface;
use EasyWeChat\Pay\Message;

/**
 * 微信支付网关服务
 * 封装所有与 EasyWechat Pay SDK 的交互。
 */
class PaymentService extends BaseWechatService
{
    public function __construct()
    {
        // 通过基类的初始化方法加载了 wechat.pay 的配置
        parent::__construct('pay');
    }

    /**
     * 实现父类的抽象方法
     * 初始化 EasyWechat 微信支付实例
     */
    public function initApp(): void
    {
        $this->appInstance = new Application($this->config);
    }

    /**
     * 创建 JSAPI 预支付订单
     * @param string $outTradeNo 我们自己生成的支付单号
     * @param init $amountInCents 支付金额｜单位:分
     * @param string $description 支付描述
     * @param string $openid 用户在小程序的 openid
     * @return array 用于前端 wx.requestPayment 调起支付弹窗的 JSSDK 配置
     */
    public function createJsApiTransaction(
        string $outTradeNo,
        int $amountInCents,
        string $description,
        string $openid
    ): array 
    {
        return $this->callApi(
            function (Application $app) use (
                $outTradeNo,
                $amountInCents,
                $description,
                $openid
            ) {
            // 通过支付应用实例，调用 easywechat 方法创建预支付订单
            $response = $app->getClient()->postJson('/v3/pay/transactions/jsapi', [
                // Arr::get($this->config, 'app_id')
                'appid' => $app->getConfig()->app_id,
                'mchid' => $app->getConfig()->mch_id,
                'description' => $description,
                'out_trade_no' => $outTradeNo,
                'notify_url' => $app->getConfig()->notify_url,
                'amount' => [
                    'total' => $amountInCents
                ],
                'payer' => [
                    'openid' => $openid
                ]
            ]);

            // 获取到 prepay_id
            $prepayId = $response->toArray(false)['prepay_id'];
            
            // 使用 Bridge 生成前端 JSSDK 配置
            return $app->getUtils()->buildSdkConfig($prepayId, $app->getConfig()->app_id);

        }, 'v3/pay/transactions/jsapi');
    }

    /**
     * 主动查询微信订单状态
     * 安全性增强
     * @param $outTradeNo 商户订单号
     * @return array 微信返回的权威订单状态数据
     */
    public function queryByOutTradeNo(string $outTradeNo): array
    {
        return $this->callApi(
            function (Application $app) use ($outTradeNo) {
                $response = $app->getClient()->get(
                    "/v3/pay/transactions/out-trade-no/{$outTradeNo}", 
                    [
                        'query'=>[
                            // 使用 getMerchant()->getMerchantId() 动态获取商户号，更健壮
                            'mchid' => $app->getMerchant()->getMerchantId()
                        ]
                    ]
                );
                
                // toArray(false) 返回纯数组，便于上层服务处理
                return $response->toArray(false);

            }, 'v3/pay/transactions/queryByOutTradeNo');
    }

    /**
     * 提供一个获取 Server 实例的方法供控制器使用
     * 
     * @return \EasyWeChat\Pay\Server
     */
    public function getServer(): \EasyWeChat\Pay\Server
    {
        return $this->appInstance->getServer();
    }

}