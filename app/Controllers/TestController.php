<?php
namespace App\Controllers;

use App\Exceptions\ServiceException;

class TestController extends Base {

    public function indexAction()
    {

        try {
            $this->onlyDevOpen();//仅开放dev环境调用


            return $this->responseSuccess(['tips'=>'测试代码，仅在dev环境执行'], __METHOD__);

         } catch (ServiceException $e) {
            self::logError('onlydevopen Exception', $e->getCode(), $e->getMessage()); //错误日
            return $this->responseError($e->getCode(), $e->getMessage(), null); //错误信息返回
         }

    }

}