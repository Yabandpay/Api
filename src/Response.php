<?php
namespace YaBandPay\Api;


class Response
{
    const OK = 'ok';

    const BAD = 'bad';

    public static function ajaxReturnSuccess($content = 'ok')
    {
        echo $content;
        exit;
    }

    public static function ajaxReturnFailure($content = 'bad')
    {
        echo $content;
        exit;
    }
}