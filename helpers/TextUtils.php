<?php

namespace helpers;

class TextUtils
{
    public static function firstUp($text, $encoding = 'utf-8')
    {
        $string = strtolower($text);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }

    public static function notMarkdown($text)
    {
        $r = str_replace("_", "\\_", $text);
        $r = str_replace("*", "\\*", $r);
        $r = str_replace("[", "\\[", $r);
        $r = str_replace("`", "\\`", $r);
        return $r;
    }
}