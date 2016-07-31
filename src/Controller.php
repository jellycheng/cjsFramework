<?php
namespace CjsFramework;

/**
 * 控制器基类
 * @package CjsFramework
 */
class Controller
{

    public function __construct()
    {
        //编码
        $charset = \CjsFramework\env('CHARSET', 'utf-8');
        header("Content-type: text/html; charset=" . $charset);
        if(\CjsFramework\env('TIMEZONE', '')) {
            date_default_timezone_set(\CjsFramework\env('TIMEZONE'));
        }

    }

    public function getgpc($k, $var='G', $default = NULL) {
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
    public function getHeader($key) {
        $key = strtoupper('HTTP_' . $key);
        $v = isset($_SERVER[$key])?$_SERVER[$key]:null;
        return $v;
    }






}