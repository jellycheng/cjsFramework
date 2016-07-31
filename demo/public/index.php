<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 16/7/30
 * Time: 下午2:35
 */
require_once '../../vendor/autoload.php';

$basePath = dirname(__DIR__);
\CjsFramework\Application::createApp($basePath)->run();


