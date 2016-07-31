<?php
namespace CjsFramework;
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 16/7/30
 * Time: 下午2:29
 */

class Application {

    const VERSION = '1.0.0';

    protected static $instance = null;

    protected $basePath;

    protected $storagePath;

    protected $pathDir = array();

    protected $routeObj = null;

    protected $routeParam = array();

    protected function __construct($basePath = null)
    {
        static::setInstance($this);
        if ($basePath) $this->setBasePath($basePath);
    }

    public static function getInstance()
    {
        return static::$instance;
    }

    public static function setInstance($container)
    {
        static::$instance = $container;
    }

    public static function createApp($basePath = null)
    {
        if(is_null(static::$instance)) {
            static::$instance = new static($basePath);
            return static::$instance;
        }
        return static::getInstance();
    }

    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\\/') . DIRECTORY_SEPARATOR;
        $this->bindPathsInContainer();
        return $this;
    }

    protected function bindPathsInContainer()
    {
        $this->setPathDir('path', $this->path());
        foreach (['base','app', 'config', 'database', 'lang', 'public', 'storage','bin', 'view', 'controller', 'bmodel', 'model'] as $path)
        {
            $this->setPathDir('path.'.$path, $this->{$path.'Path'}());
        }
    }

    public function setPathDir($key, $val) {
        $this->pathDir[$key] = rtrim($val, '/\\') . DIRECTORY_SEPARATOR;
        return $this;
    }

    public function getPathDir($key) {
        return isset($this->pathDir[$key])?$this->pathDir[$key]:'';
    }

    public function appPath() {
        return $this->path();
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @return string
     */
    public function path()
    {
        return $this->basePath . 'App' . DIRECTORY_SEPARATOR;
    }

    public function basePath()
    {
        return rtrim($this->basePath, '\\/') . DIRECTORY_SEPARATOR;
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->basePath.'App'.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR;
    }

    /**
     * Get the path to the database directory.
     *
     * @return string
     */
    public function databasePath()
    {
        return $this->basePath.'App'.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'Database'.DIRECTORY_SEPARATOR;
    }

    /**
     * Get the path to the language files.
     *
     * @return string
     */
    public function langPath()
    {
        return $this->basePath.'App'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'Lang'.DIRECTORY_SEPARATOR;
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->basePath.'public'.DIRECTORY_SEPARATOR;
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function storagePath()
    {
        return $this->storagePath ?: $this->basePath.'App'.DIRECTORY_SEPARATOR.'Storage'.DIRECTORY_SEPARATOR;
    }

    public function setStoragePath($path)
    {
        $this->storagePath = $path;
        $this->setPathDir('path.storage', $path);
        return $this;
    }

    public function binPath()
    {
        return $this->basePath.'App'.DIRECTORY_SEPARATOR.'Bin'.DIRECTORY_SEPARATOR;
    }

    public function modelPath()
    {
        return $this->basePath.'App'.DIRECTORY_SEPARATOR.'Model'.DIRECTORY_SEPARATOR;
    }

    public function bmodelPath()
    {
        return $this->basePath.'App'.DIRECTORY_SEPARATOR.'Bmodel'.DIRECTORY_SEPARATOR;
    }

    public function viewPath() {
        return $this->basePath.'App'.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR;
    }

    public function controllerPath() {
        return $this->basePath.'App'.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR;
    }

    public function setRoute($routeObj) {
        $this->routeObj = $routeObj;
    }

    public function getRoute() {
        return $this->routeObj;
    }

    public function getRouteParam() {
        return $this->routeParam;
    }

    public function run()
    {
        //返回 array('controller'='控制器', 'action'=>'控制器类方法');
        if($this->getRoute()) {
            $this->routeParam = $this->getRoute()->parse();
        } else {
            $this->routeParam = \CjsFramework\Routing\SimpleRoute::getInstance()->parse();
        }
        $controllerClassName = $this->routeParam['controller'];
        $actionMethod = $this->routeParam['action'];
        //类名只允许是字母，数字，下划线,/,\
        if(!preg_match('/^[a-zA-Z\\\\0-9_\/]+$/', $controllerClassName)) {
            $this->_404();
            return false;
        }
        #动作只允许是字母，数字，下划线,且以字母开头
        if(!preg_match('/^[a-z][a-z0-9_]+$/i', $actionMethod)) {
            $this->_404();
            return false;
        }

        if(class_exists($controllerClassName)) {
            $controller = new $controllerClassName;
            if(method_exists($controllerClassName, $actionMethod)) {
                call_user_func(array($controller, $actionMethod));
            } else{
                $this->_404();
                return false;
            }
        } else {
            $this->_404();
            return false;
        }

    }

    //404 处理
    protected function _404()
    {
        $str404 = "className: " . $this->routeParam['controller'] . " or ActionMethod:" . $this->routeParam['action'] . " not exists!";
        $error404 = \CjsFramework\env('ERROR404_CONTROLLER', '\CjsFramework\Error\Error404Controller@indexAction');
        $isFound = false;
        if($error404) {
            $error404Ary = explode('@', $error404);
            if(count($error404Ary)==2) {
                $className = $error404Ary[0];
                if(class_exists($className)) {
                    $controller = new $className;
                    $action = $error404Ary[1]?$error404Ary[1]:'IndexAction';
                    if(method_exists($controller, $action)) {
                        $isFound = true;
                        call_user_func(array($controller, $action));
                    }
                }
            }
        }

        if(!$isFound) {
            exit($str404);
        }

    }

    public function version()
    {
        return static::VERSION;
    }

}