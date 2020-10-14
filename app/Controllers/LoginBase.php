<?php
/**
 * 需要登录的接口控制器类继承
 */
namespace App\Controllers;

use App\Enum\ExceptionCodeEnum;
use CjsException\BaseException as ServiceException;
use Log;

class LoginBase extends Base
{
    public function __construct()
    {
        parent::__construct();
        $error = true;
        try{
            if($this->checkLogin()){
                $error = false;
            }
        }catch (ServiceException $e){
            Log::info(__FUNCTION__,[$e->getMessage(),$e->getCode()]);
        }catch (\Exception $e){
            Log::info(__FUNCTION__,[$e->getMessage(),$e->getCode()]);
        }
        if($error){
            $res = $this->responseError(ExceptionCodeEnum::USER_NO_LOGIN, '登录失效', new \stdClass());
            echo json_encode($res, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}