<?php
namespace xcxphpsdk;

use think\Exception;

/**
 * 创建对象类
 * @author hiyanxu
 * @date 2018-10-26
 */
class XcxFactory {
    /**
     * 以实现的类
     * @var array
     */
    private static $_class=[
        'test' => "xcxphpsdk\\Xcx\\test",
        "TemplateMsg" => "xcxphpsdk\\Xcx\\TemplateMsg",
    ];
    
    private static $instance = [];
    
    /**
     * 创建实例化对象  暂时废弃
     * @param unknown $obj_type
     * @throws Exception
     * @return unknown
     */
    public function makeObj($obj_type, $config){
        return false;
        
        if (in_array($obj_type, array_keys(self::$_class))){
            return new self::$_class[$obj_type]($config);
        } else {
            throw new Exception('Unknow class type:'.$obj_type);
        }
    }
    
    /**
    * 返回实例化对象
    * @date:2018年11月9日 下午3:32:55
    * @author:hiyanxu
    * @param string $obj_type 待获取的对象类型
    * @param array $config 传入的config配置
    */
    public static function getInstance($obj_type, $config){
        $config_str = serialize($config);  //构造一个二维数组，对不同类、不同config 做实例化返回
        if (is_null(self::$instance[$obj_type][$config_str])){
            if (in_array($obj_type, array_keys(self::$_class))){
                self::$instance[$obj_type][$config_str] = new self::$_class[$obj_type]($config); 
            } else {
                throw new \Exception('Unknow class type:'.$obj_type);
            }
        }
        
        return self::$instance[$obj_type][$config_str];
    }
}