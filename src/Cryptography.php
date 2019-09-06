<?php
namespace YaBandPay\Api;

use function array_filter;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Class Cryptography
 *
 * @package YaBandPay
 * @description
 * @version 1.0.0
 */
class Cryptography
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $algorithm;
    /**
     * @var string
     */
    private $algo;

    public function __construct($token, $algorithm = 'hash_hmac', $algo = 'sha256')
    {
        $this->token = $token;
        $this->algorithm = $algorithm;
        $this->algo = $algo;
    }

    public function encrypt(array $data, $sort = false)
    {
        switch($this->algorithm){
            case 'hash_hmac':
                {
                    switch($this->algo){
                        case 'sha256':
                            {
                                if(\function_exists('hash_hmac') === false){
                                    throw new \InvalidArgumentException('hash_hmac not found .');
                                }
                                if($sort === true){
                                    \ksort($data);
                                }
                                return \hash_hmac('sha256', \is_array($data) ? self::serialize($data) : $data, $this->token);
                            }
                    }
                }
        }
        throw new \InvalidArgumentException('Please choose a normal encryption algorithm .');
    }

    protected static function serialize(array $data = [])
    {
        $serialize = [];
        foreach($data as $key => $val){
            $serialize[] = $key . '=' . $val;
        }
        return implode('&', $serialize);
    }

    /**
     * verify
     *
     * @param $data
     * @return bool
     */
    public function verify($data, $sort = true)
    {
        $sign = $this->encrypt($data['data'], $sort);
        return $data['sign'] === $sign;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @param string $algorithm
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @return string
     */
    public function getAlgo()
    {
        return $this->algo;
    }

    /**
     * @param string $algo
     */
    public function setAlgo($algo)
    {
        $this->algo = $algo;
    }
}