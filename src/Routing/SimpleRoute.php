<?php
namespace CjsFramework\Routing;
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 16/7/31
 * Time: 下午1:32
 */
use CjsFramework\Application;

class SimpleRoute {

    protected static $instance = null;

    protected $_currentUri = null;
    //控制器命名空间前缀
    protected $_controllerNamespacePrefix = '';

    private function __construct()
    {
        $this->_currentUri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';

    }

    public function getRequestUri() {
        return $this->_currentUri;
    }

    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    /**
     *
     * @return array = array('controller' => '', 'action'=>'')
     */
    public function parse() {
        $routeParam = array('controller' => '',
            'action'=>'',
            'ext_param'=>array()
        );
        $url = $this->_currentUri;
        if($url) {
            $pattern = \CjsFramework\env('APP_URI_PREFIX', '');
            if($pattern) {
                $url = preg_replace($pattern.'', '', $url);
            }
            $appPath = Application::getInstance()->configPath();
            if(file_exists($appPath . 'ControllerNamespacePrefix.php')) {
                $cnp = include $appPath . 'ControllerNamespacePrefix.php';
                foreach((array)$cnp as $k_pattern=>$namespacePrefix) {
                    if(preg_match($k_pattern, $url)) {
                        $this->_controllerNamespacePrefix = $namespacePrefix;
                        $url = preg_replace($k_pattern, '', $url);
                        //匹配到第1个就不要往后匹配了
                        break;
                    }

                }
            }

        }

        $uriInfo = explode('?', $url, 2);
        $uriPath = trim(array_shift($uriInfo), '/');
        $reg = "/\.(js|css|jpg|jpeg|gif|png|ico|swf|bmp|tiff|svg|doc|docx|txt|rtf|pdf|xls|xlsx|ppt|pptx)$/i";
        if(preg_match($reg, strrchr($uriPath, '.'))) {//对静态资源不做路由
            exit();
        }
        $controller = $action = '';
        if (!empty($uriPath)) {
            $uriPath =  explode('/', $uriPath);
            $controller = ucfirst(preg_replace_callback('/(_|-|\.)([a-zA-Z])/', function($match){return '\\'.strtoupper($match[2]);}, $uriPath[0]));
            //动作
            if (isset($uriPath[1])) {
                $action =  $uriPath[1];
                unset($uriPath[0], $uriPath[1]);
            } else {
                unset($uriPath[0]);
            }
        }
        //默认控制器
        if(!$controller) {
            $controller = \CjsFramework\env('DEFAULT_CONTROLLER', 'Index');
        }
        if(!$action){
            $action   = \CjsFramework\env('DEFAULT_ACTION', 'index');
        }

        if($this->_controllerNamespacePrefix && $controller) {
            $controller = trim($this->_controllerNamespacePrefix, '\\') . '\\' . $controller;
        }

        if ($uriPath && count($uriPath) > 0) {
            $mark = 0;
            $val = $key = array();
            foreach($uriPath as $value){
                $mark++;
                if ($mark % 2 == 0) {
                    $val[] = $value;
                } else {
                    $key[] = $value;
                }
            }
            if(count($key) !== count($val)) $val[] = '';
            $get = array_combine($key,$val);
            foreach($get as $key=>$value) $routeParam['ext_param'][$key] = $value;
        }
        $routeParam['controller'] = $this->getControllerClassName($controller);
        $routeParam['action'] = $this->getActionMethod($action);
        return $routeParam;
    }

    public function getControllerClassName($control)
    {
        $appNameSpace = \CjsFramework\env('APP_NAMESPACE', 'App');
        return $appNameSpace.'\\Controller\\' . $control . 'Controller';
    }

    public function getActionMethod($action) {
        return $action . 'Action';
    }

}