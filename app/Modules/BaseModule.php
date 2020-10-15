<?php
namespace App\Modules;

//业务逻辑基础类
abstract class BaseModule {
    //每次实例化新的对象
    public static function getInstance() {
        return new static();
    }

}
