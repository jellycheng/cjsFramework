<?php
namespace CjsFramework\Error;
use CjsFramework\Controller;
/**
 * 404错误
 */
class Error404Controller extends Controller {

    // todo 根据是否ajax请求来响应不同的 html内容
    public function indexAction() {

        exit("Sorry,page not found！ <!-- System 404 -->" . PHP_EOL);
    }

}