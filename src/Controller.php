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

}