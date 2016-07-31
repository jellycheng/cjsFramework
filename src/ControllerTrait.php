<?php
namespace CjsFramework;
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 16/7/30
 * Time: 下午7:15
 */

trait ControllerTrait {


    /**
     * @param int $code
     * @param string $msg
     * @param string $data
     */
    public function response($code = 0, $msg = 'OK', $data = '') {
        if(!$data) {
            $data = \CjsFramework\Util\EmptyObj::g();
        }
        $code = intval($code);
        $data = array(
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data,
            'elapsed_time'=>\CjsFramework\Util\Benchmark::elapsed_time('apiElapsedTime')*1
        );
        header("Content-Type: application/json;charset=" . \CjsFramework\env('CHARSET', 'utf-8'));
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * @param string $data
     * @param string $msg
     */
    public function success($data = '', $msg = 'OK') {
        $this->response(0, $msg, $data);

    }

    /**
     * @param $code
     * @param $msg
     * @param string $data
     */
    public function error($code, $msg, $data = '') {
        if(!$code) {
            $code = 100000;
        }
        $this->response($code, $msg, $data);

    }


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