<?php
namespace YaBandPay\Api;

class Helper
{
    public static function isWeixin()
    {
        if(\strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
            return true;
        }
        return false;
    }

    public static function isAlipay()
    {
        if(\strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false){
            return true;
        }
        return false;
    }

    public static function randStr($length = 8)
    {
        $chars = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
            't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
            'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
        $keys = \array_rand($chars, $length);
        $password = '';
        for($i = 0;$i < $length;$i++){
            $password .= $chars[$keys[$i]];
        }
        return $password;
    }
}