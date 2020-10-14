<?php
namespace App\Util;

class GateWay
{
    protected $gateWayConfig = [];
    protected $parseResult = [];

    protected function __construct() {

    }

    public static function getInstance() {
        static $instance;
        if(!$instance) {
            $instance = new static();
        }
        return $instance;
    }

    public function getTag4Pattern($pattern) {
        $ret = [[], []];
        if (preg_match_all('/<(.+?)>/', $pattern, $matches)) {
            $ret = $matches;
        }
        return $ret;
    }
    public function getGateWayConfig()
    {
        return $this->gateWayConfig;
    }
    public function setGateWayConfig($gateWayConfig)
    {
        $this->gateWayConfig = $gateWayConfig;
        return $this;
    }
    public function appendGateWayConfig($key, $val)
    {
        $this->gateWayConfig[$key] = $val;
        return $this;
    }
    //生成正则表达式
    public function buildPattern($pattern) {
        $pattern = preg_replace(array('/\//', '/<.+?>/'), array('\/', '([^\/]+)'), $pattern);
        return $pattern;
    }
    public function matchParams($paramValue, $tagKeys) {
        $ret = [];
        if (count($tagKeys)) {
            array_shift($paramValue);
            $ret = array_combine($tagKeys, $paramValue);
        }
        return $ret;
    }
    public function init($configFile) {
        $config = include $configFile;
        foreach ($config as $k=>$v) {
            $this->appendGateWayConfig($k, $v);
        }
    }

    //没有匹配到返回空数组
    public function parse($uri) {
        $ret =[];
        $uriInfo = explode('?', $uri, 2);
        $uriPath = $uriInfo[0];
        $gateWayConfig = $this->getGateWayConfig();
        foreach($gateWayConfig as $pattern=>$val) {
            if (preg_match('/^' . $this->buildPattern($pattern) . '/', $uriPath, $matches)) {//找到符合规则
                $tagKeys = $this->getTag4Pattern($pattern);
                $newMatchUrlParam = $this->matchParams($matches, $tagKeys[1]);
                $ret['pattern'] = $pattern;
                $ret['val'] = $val;
                $ret['param'] = $newMatchUrlParam;
                $this->parseResult = $ret;
                return $ret;
            }
        }
        $this->parseResult = $ret;
        return $ret;
    }

    public function getParseResult() {
        return $this->parseResult;
    }


}