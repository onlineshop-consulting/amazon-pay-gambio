<?php
declare(strict_types=1);

namespace OncoAmazonPay;

class Utils
{
    public static function autoDecode($str)
    {
        if (strtolower($_SESSION["language_charset"]) == 'utf-8') {
            return self::autoEncode($str);
        } elseif (self::isUTF8($str)) {
            return utf8_decode($str);
        }

        return $str;
    }

    public static function autoEncode($str)
    {
        if (self::isUTF8($str)) {
            return $str;
        }

        return utf8_encode($str);
    }

    public static function isUTF8($str)
    {
        if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
            return true;
        } else {
            return false;
        }
    }

    public static function formatDate($amazonApiDate)
    {
        return date('Y-m-d H:i:s', strtotime($amazonApiDate));
    }

    public static function isAmazonPayCheckout()
    {
        return !empty($_SESSION['amazonCheckoutSessionId'])
            && !empty($_SESSION['payment'])
            && $_SESSION['payment'] === 'amazon_pay';
    }

}