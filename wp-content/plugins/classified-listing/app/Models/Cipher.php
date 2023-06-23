<?php

namespace Rtcl\Models;

class Cipher
{
    private static $salt;
    private static $text;

    /**
     * @param $text
     * @param $salt
     *
     * @return string
     */
    static function encrypt($text, $salt) {
        if (empty($text)) return '';
        self::$salt = $salt;
        self::$text = $text;
        $encoded = '';
        foreach (self::textToChars(self::$text) as $charCode) {
            $saltCharCode = self::applySaltToChar($charCode);
            $byteHex = self::byteHex($saltCharCode);
            $encoded .= $byteHex;
        }

        return $encoded;
    }

    /**
     * @param $encoded
     * @param $salt
     *
     * @return string
     */
    static function decrypt($encoded, $salt) {
        if (empty($encoded)) return '';
        self::$salt = $salt;
        self::$text = $encoded;

        preg_match_all("/.{1,2}/", self::$text, $matches);
        $characters = '';
        if (!empty($matches[0]) && is_array($matches[0])) {
            foreach ($matches[0] as $match) {
                $hex = hexdec($match);
                $charCode = self::applySaltToChar($hex);
                $characters .= chr($charCode);
            }
        }
        return $characters;
    }

    private static function textToChars($__text) {
        return !empty($__text) ? array_map(function ($ch) {
            return ord($ch);
        }, str_split($__text)) : [];
    }

    private static function applySaltToChar($__code) {
        return array_reduce(self::textToChars(self::$salt), [__CLASS__, 'reduce'], $__code);
    }

    private static function reduce($a, $b) {
        return $a ^ $b;
    }

    private static function byteHex($n) {
        if (is_numeric($n)) {
            $n = $n + 0;
        }
        return substr("0" . dechex($n), -2);
    }


}