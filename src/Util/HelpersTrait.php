<?php
namespace CjsFramework\Util;
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 16/7/30
 * Time: 下午7:22
 */

trait HelpersTrait {

    protected function _e($value, $encoding='utf-8') {
        return htmlentities($value, ENT_QUOTES, $encoding, false);
    }

    protected function _htmlspecialchars($value, $encoding='utf-8') {

        return htmlspecialchars($value, ENT_QUOTES, $encoding);
    }

    protected function _array_get($array, $key, $default = null) {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment)
        {// a.b.c
            if ( ! is_array($array) || ! array_key_exists($segment, $array))
            {//不存在的key则返回默认值
                return $default instanceof \Closure ? $default() : $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

}