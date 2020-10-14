<?php
namespace App\Enum;

class ExceptionCodeEnum
{
    const SUCCESS = 0;//成功返回代码
    const FAIL = 1;//操作错误代码
    const INVALID_ARGUMENT = 2;//参数错误
    const USER_NO_LOGIN = 10000;//登陆态token过期/未登录

}