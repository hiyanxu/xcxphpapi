<?php
namespace xcxphpsdk\Xcx;

class test extends Common{
    public function haha($param){
        return array_merge($this->config, $param);
    }
}