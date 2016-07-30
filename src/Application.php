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

    protected function __construct()
    {
        static::setInstance($this);

    }

    public static function getInstance()
    {
        return static::$instance;
    }

    public static function setInstance($container)
    {
        static::$instance = $container;
    }

    public static function createApp()
    {
        if(is_null(static::$instance)) {
            return new static();
        }
        return static::getInstance();
    }

    public function run()
    {

    }

}