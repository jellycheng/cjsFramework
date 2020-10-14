<?php
namespace App\Controllers;
/**
 * 404页面
 */
use Log;

class NotfoundController extends Base {

    /**
     * 404 地址
     */
    public function indexAction()
    {
        $uri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
        Log::info(__METHOD__ . '请求地址不存在', ['uri' => $uri]);
        return $this->responseError('404','页面不存在', new \stdClass(), __METHOD__);
    }


}