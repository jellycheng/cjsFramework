<?php
namespace App\Controllers;

class YzmController extends Base {

    //响应图片验证码： /yzm/index
    public function indexAction()
    {
        $width = 130;
        $hight = 30;
        $fontsize = 20;
        $codelen = 4;
        $yzmObj = new \App\Util\ValidateCode($width, $hight, $fontsize, $codelen);
        $yzmObj->doimg();
        $code  = $yzmObj->getCode();
        //把code值存储session中或redis中 todo

    }

}