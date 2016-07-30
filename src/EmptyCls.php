<?php
namespace CjsFramework;
/**
 * 该类的对象的方法永远返回空
 */
class EmptyCls {

    public function __call() {
        return '';
    }

}