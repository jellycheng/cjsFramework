<?php
namespace App\Controllers;

use App\Enum\ExceptionCodeEnum;
use CjsException\BaseException as ServiceException;
use Log;

abstract class Base
{

    const STATUS_SUCCESS = 0;
    protected $userLoginToken = '';
    public function __construct() {

    }

    /**
     * 返回信息
     *
     * @author chengjinsheng
     * @date 2016-10-11
     * @param string $code required 错误码
     * @param string $msg option 错误信息
     * @param array|string $data option 返回数据
     * @return array
     */
    protected function response($code, $msg = '', $data = '', $method = '')
    {
        if(defined('REQUEST_TRACE_ID')){
            $trace = REQUEST_TRACE_ID;
        } else {
            $trace = '';
        }
        $result =  [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'trace_id' =>$trace
        ];
        header("trace-id: $trace");
        if(!$method) {
            $method = __METHOD__;
        }
        Log::debug( $method . "接口出参:",$result);
        return $result;
    }

    /**
     * 正确返回信息
     *
     * @author chengjinsheng
     * @date 2016-10-11
     * @param array $data option 返回数据
     * @return array
     */
    protected function responseSuccess($data = null, $method = '')
    {
        return $this->response(self::STATUS_SUCCESS, 'success', $data, $method);
    }

    /**
     * 错误返回信息
     *
     * @author chengjinsheng
     * @date 2016-10-11
     * @param string $code required 错误码
     * @param string $msg option 错误信息
     * @param array $data option 返回数据
     * @return array
     */
    protected function responseError($code, $msg = 'fail', $data = null, $method = '')
    {
        if ($code == '0') {                         //php抛出异常码为0
            Log::error(__METHOD__.'错误返回异常', [$code, $msg]);
            $code = '10000000';
            $msg = '系统异常';
        }
        return $this->response($code, $msg, $data, $method);
    }


    /**
     * RPC请求参数校验
     *
     * @author chengjinsheng
     * @date 2016-10-11
     * @param array $param required rpc请求的参数
     * @param array $rules required rpc校验参数规则
     * @param array $message option rpc校验错误信息
     * @param boolean $filter option 是否过滤多余参数
     * @param string $method option 调用入口
     * @throws ServiceException
     * @return array 请求的业务参数
     */
    protected function validate($param, $rules, $message = [], $filter = true, $method = '')
    {
        $validator = \Validator::make($param, $rules, $message);
        if ($validator->fails()) {
            $aError = $validator->messages()->toArray();
            Log::debug($method.' 请求参数校验失败', ['params' => $param, 'result' => $aError]);
            foreach ($aError as $field => $error) { //获取第一个错误信息
                $msg = array_get($error, 0, '');
                if (strpos($msg, 'validation.')===0) {
                    $msg = sprintf('%s.%s', $field, $msg);
                }
                break;
            }
            if (ServiceException::code($msg)) {
                throw new ServiceException($msg);
            } else {
                throw new ServiceException($msg, ExceptionCodeEnum::INVALID_ARGUMENT);
            }
        }
        Log::debug($method.' 请求参数校验', ['params' => $param]);
        if ($filter) {
            $data = [];
            foreach ($rules as $key => $val) {
                $val = array_get($param, $key, '');
                if ($val !== '') { //保留null
                    array_set($data, $key, $val);
                }
            }
            return $data;
        }
        return $param;
    }


    /**
     * Controller异常日志方法
     *
     * Controller中记录日志方法，默认error
     *
     * @author chengjinsheng
     * @date 2016-10-11
     * @param string $tips  日志信息
     * @param string $code 异常code
     * @param string $msg  异常错误信息
     * @param array $dataParam  请求参数
     * @return null
     */
    protected function log($tips, $code, $msg, $dataParam = [])
    {
        $this->logError($tips, $code, $msg, $dataParam);
    }


    /**
     * 正常info日志
     *
     * Controller中记录日志方法，只用info,debug,warning,error四种不同级别的方法，按需使用，切勿修改
     *
     * @author chengjinsheng
     * @date 2016-10-18
     * @param string $tips required 日志信息
     * @param string $code requires 异常code
     * @param string $msg requires 异常错误信息
     * @param array $dataParam requires 请求参数
     * @return null
     */
    protected function logInfo($tips, $code, $msg, $dataParam = [])
    {
        Log::info($tips, ['code' => $code, 'msg' => $msg, 'params' => $dataParam]);
    }


    /**
     * debug日志
     *
     * Controller中记录日志方法，只用info,debug,warning,error四种不同级别的方法，按需使用，切勿修改
     *
     * @author chengjinsheng
     * @date 2016-10-18
     * @param string $tips required 日志信息
     * @param string $code requires 异常code
     * @param string $msg requires 异常错误信息
     * @param array $dataParam requires 请求参数
     * @return null
     */
    protected function logDebug($tips, $code, $msg, $dataParam = [])
    {
        Log::debug($tips, ['code' => $code, 'msg' => $msg, 'params' => $dataParam]);
    }


    /**
     * warning日志
     *
     * Controller中记录日志方法，只用info,debug,warning,error四种不同级别的方法，按需使用，切勿修改
     *
     * @author chengjinsheng
     * @date 2016-10-18
     * @param string $tips required 日志信息
     * @param string $code requires 异常code
     * @param string $msg requires 异常错误信息
     * @param array $dataParam requires 请求参数
     * @return null
     */
    protected function logWarning($tips, $code, $msg, $dataParam = [])
    {
        Log::warning($tips, ['code' => $code, 'msg' => $msg, 'params' => $dataParam]);
    }


    /**
     * error日志
     *
     * Controller中记录日志方法，只用info,debug,warning,error四种不同级别的方法，按需使用，切勿修改
     *
     * @author chengjinsheng
     * @date 2016-10-18
     * @param string $tips required 日志信息
     * @param string $code requires 异常code
     * @param string $msg requires 异常错误信息
     * @param array $dataParam requires 请求参数
     * @return null
     */
    protected function logError($tips, $code, $msg, $dataParam = [])
    {
        Log::error($tips, ['code' => $code, 'msg' => $msg, 'params' => $dataParam]);
    }

    /**
     * 禁止生产环境执行，一般用于测试型的代码，避免影响生产环境数据
     *
     * 调用示例：
     * try {
     *    $this->denyProductionExec();//禁止生产环境执行
     * } catch (ServiceException $e) {
     *   self::logError('onlydevopen Exception', $e->getCode(), $e->getMessage()); //错误日
     *   return $this->responseError($e->getErrno(), $e->getMessage(), null); //错误信息返回
     * }
     */
    protected function denyProductionExec() {
        if(env('APP_ENV', 'prod') == 'prod') {
            throw new ServiceException('API_ONLY_DEV_OPEN');
        }
    }

    /**
     * 控制api仅在dev环境调用，杜绝其它环境调用，避免影响生产环境数据
     *
     * 调用示例：
     * try {
     *    $this->onlyDevOpen();//仅开放dev环境调用
     * } catch (ServiceException $e) {
     *   self::logError('onlydevopen Exception', $e->getCode(), $e->getMessage()); //错误日
     *   return $this->responseError($e->getErrno(), $e->getMessage(), null); //错误信息返回
     * }
     */
    protected function onlyDevOpen() {
        if(env('APP_ENV', 'prod') != 'dev') {
            throw new ServiceException('API_ONLY_DEV_OPEN');
        }
    }


}