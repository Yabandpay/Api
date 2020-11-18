<?php
namespace YaBandPay\Api;

use function strlen;
use function substr;

class Notify
{
    const KEY = 'resultData=';
    /**
     * @var Cryptography
     */
    private $cryptography;

    public function __construct(Cryptography $cryptography)
    {
        $this->cryptography = $cryptography;
    }

    protected function input()
    {
        $result = \file_get_contents('php://input');
        if(!empty($result)){
            $result = \urldecode($result);
        }else{
            if(!isset($_POST['resultData']) || empty($_POST['resultData'])){
                throw new \InvalidArgumentException('Receiver Data Is Empty');
            }else{
                $result = $_POST['resultData'];
            }
        }

        if(\strpos($result, self::KEY) === 0){
            $result = substr($result, strlen(self::KEY));
        }
        if(empty($result)){
            throw new \InvalidArgumentException('Receiver Data Is Empty');
        }
        $result = \json_decode($result, true);
        if(\json_last_error()){
            throw new \InvalidArgumentException('Receiver Data Is Error, ' . \json_last_error_msg());
        }
        if(!isset($result['sign']) || empty($result['sign']) || !isset($result['data']) || empty($result['data'])){
            throw new \InvalidArgumentException('Receiver Data Is Field Error');
        }
        return $result;
    }

    public function getOrderInfo()
    {
        $data = $this->input();
        if($this->cryptography->verify($data) === false){
            throw new \InvalidArgumentException('Receiver Data Signature Verification Failed');
        }
        return $data['data'];
    }

    /**
     * @return Cryptography
     */
    public function getCryptography()
    {
        return $this->cryptography;
    }

    /**
     * @param Cryptography $cryptography
     */
    public function setCryptography(Cryptography $cryptography)
    {
        $this->cryptography = $cryptography;
    }
}