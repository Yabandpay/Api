<?php
namespace YaBandPay\Api;

use YaBandPay\Api\Exception\RequestErrorException;
use function var_export;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class Request
{
    const SUCCESS = true;
    /**
     * @var Cryptography
     */
    private $cryptography;

    private $callback = null;

    public function __construct(Cryptography $cryptography, $callback = null)
    {
        $this->cryptography = $cryptography;
        $this->callback = $callback;
    }

    public function get($url, $data)
    {

    }

    public function post($url, $data)
    {
        $data = $this->completion($data);
        if(\is_callable($this->callback)){
            $result = \call_user_func($this->callback, [ $url, $data ]);
        }else{
            $result = $this->curlPost($url, $data);
        }
        if(!isset($result) || empty($result)){
            throw new RequestErrorException('post request error, response data is empty');
        }
        $response = \json_decode($result, true);
        if($response === null || $response === false || empty($response) || \json_last_error()){
            throw new RequestErrorException('request response json format error,' . \json_last_error_msg() . '<br/>' . $result);
        }
        if(!isset($response['status'])){
            throw new RequestErrorException('post request response error');
        }
        if($response['status'] === self::SUCCESS && isset($response['data']) && !empty($response['data'])){
            return $response['data'];
        }else{
            throw new RequestErrorException($response['message']);
        }
    }

    public function completion($param)
    {
        $sign_param = \array_merge($param, $param['data']);
        unset($sign_param['data']);
        $sign = $this->cryptography->encrypt($sign_param, true);
        $param['sign'] = $sign;
        return \json_encode($param, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function curlPost($url, $data)
    {
        $curl = \curl_init();
        \curl_setopt($curl, CURLOPT_URL, $url);
        \curl_setopt($curl, CURLOPT_HEADER, 0);
        \curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($curl, CURLOPT_POST, 1);
        \curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        \curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = \curl_exec($curl);
        if(\curl_errno($curl)){
            $error = \curl_error($curl);
        }
        \curl_close($curl);
        if(isset($error) && !empty($error)){
            throw new RequestErrorException($error);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param mixed $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }


}