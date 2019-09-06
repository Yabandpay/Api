<?php
namespace YaBandPay\Api;

class Currency
{
    const EUR = 'EUR';

    const CNY = 'CNY';

    const CURRENCY_EXCHANGE = 'CURRENCY_EXCHANGE';

    public static function isSupported($currency)
    {
        $currency = \strtoupper(\trim($currency));
        return $currency === self::EUR || $currency === self::CNY;
    }

    public static function calculateToEur($amount, $exchange_rate)
    {
        return \strval(\round($amount / $exchange_rate, 2));
    }
}
