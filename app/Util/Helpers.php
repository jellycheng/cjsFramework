<?php
namespace App\Util;

function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false) return value($default);

    switch (strtolower($value))
    {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;

        case 'null':
        case '(null)':
            return null;

        case 'empty':
        case '(empty)':
            return '';
    }

    return $value;
}

function value($value)
{
    return $value instanceof \Closure ? $value() : $value;
}

function getgpc($k, $var='G', $default = NULL) {
    switch($var) {
        case 'G': $var = &$_GET; break;
        case 'P': $var = &$_POST; break;
        case 'C': $var = &$_COOKIE; break;
        case 'R': $var = &$_REQUEST; break;
    }
    return isset($var[$k]) ? $var[$k] : $default;
}

/**
 * 获取指定的请求头值
 * @param $key
 */
function getHeader($key) {
    $key = strtoupper('HTTP_' . $key);
    $v = isset($_SERVER[$key])?$_SERVER[$key]:null;
    return $v;
}

