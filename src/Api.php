<?php
namespace YaBandPay\Api;

use function time;

class Api
{
    const DOMAIN = 'https://mapi.yabandpay.com/';
    /**
     * @var Account
     */
    private $account;
    /**
     * @var Request
     */
    private $request;

    public function __construct(Account $account, Request $request)
    {
        $this->account = $account;
        $this->request = $request;
    }

    public function payment($pay_method, $order_id, $amount, $currency, $description, $redirect_url, $notify_url, $timeout = 0, $demo = '', $email = '')
    {
        $payinfo = $this->request->post(self::paymentsUrl(), array(
            'user' => $this->account->getUser(),
            'time' => time(),
            'method' => 'v3.CreatePayments',
            'data' => array(
                'pay_method' => 'online',
                'sub_pay_method' => $pay_method,
                'order_id' => $order_id,
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
                'timeout' => $timeout,
                'redirect_url' => $redirect_url,
                'notify_url' => $notify_url,
                'ideal_email' =>  $email,
                'post_email' =>  $email,
                'demo' => $demo
            )
        ));
        if(isset($payinfo['url']) && !empty($payinfo['url'])){
            return $payinfo['url'];
        }
        return null;
    }

    public function cancel($trade_id)
    {
        return $this->request->post(self::cancelUrl(), array(
            'user' => $this->account->getUser(),
            'trade_id' => $trade_id
        ));
    }

    public function orderQuery($trade_id)
    {
        return $this->request->post(self::orderQueryUrl(), array(
            'user' => $this->account->getUser(),
            'trade_id' => $trade_id
        ));
    }

    public function refund($trade_id, $description)
    {
        return $this->request->post(self::orderQueryUrl(), array(
            'user' => $this->account->getUser(),
            'trade_id' => $trade_id,
            'description' => $description
        ));
    }

    public function verify()
    {
        $verify = $this->request->post(self::verifyUrl(), array(
            'user' => $this->account->getUser()
        ));
        if(isset($verify['info']) && empty($verify['info'])){
            return true;
        }
        return false;
    }

    private static function generateUrl($action)
    {
        return self::DOMAIN . $action;
    }

    public static function getScanPaymentsUrl($trade_id, $type = Payment::ALIPAY)
    {
        $query_string = \http_build_query(array( 'o' => $trade_id ));
        switch($type){
            case Payment::ALIPAY:
                {
                    return self::generateUrl('a.php?' . $query_string);
                }
            case Payment::WECHAT:
                {
                    return self::generateUrl('w.php?' . $query_string);
                }
            default:
                {
                    throw new \InvalidArgumentException('Invalid payment method.');
                }
        }

    }

    protected static function paymentsUrl()
    {
        return self::generateUrl('Payments');
    }

    protected static function cancelUrl()
    {
        return self::generateUrl('cancel.php');
    }

    public static function orderQueryUrl()
    {
        return self::generateUrl('orderquery.php');
    }

    protected static function refundUrl()
    {
        return self::generateUrl('refund.php');
    }

    protected static function verifyUrl()
    {
        return self::generateUrl('verify.php');
    }
}