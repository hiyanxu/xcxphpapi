<?php
namespace xcxphpsdk;

use think\Exception;
use xcxphpsdk\Xcx\Cache;
use xcxphpsdk\Xcx\Log;
/**
 * 小程序api
 * @author hiyanxu
 * @date 2018-10-26
 */
class XcxApi {
    /**
     * config配置
     * @var unknown
     */
    public $config=[  //默认为教练端配置
        'appid' => 'wx49fb1c5f4fe95a95',
        'secret' => '9351445d41dee37c59c3c8a197daecee',
        'grant_type' => 'authorization_code',
    ];
    
    const RUNTIME_PATH = __DIR__.DIRECTORY_SEPARATOR;
    
    /**
     * @var 暂时写死cache_path到当前脚本目录的/xcxcache/目录下
     */
    private $cache_path = RUNTIME_PATH."xcxcache".DIRECTORY_SEPARATOR;
    
    /**
     * 日志路径
     * @var unknown
     */
    private $log_path = RUNTIME_PATH."xcxlog".DIRECTORY_SEPARATOR;
    
    /**
     * 调用时所需的实例类型
     * @var unknown
     */
    public $obj;
    
    /**
     * 构造函数
     * @param array $config 
     * @param unknown $obj_type
     */
    public function __construct($obj_type, $config=[]){
        //设置config
        if (!empty($config)){
            $this->config = array_merge($this->config, $config);
        }
        
        //设置缓存路径
        Cache::$cachepath = empty($this->config['cache_path']) ? $this->cache_path : $this->config['cache_path'];
        //设置日志路径
        Log::$log_path = empty($this->config['log_path']) ? $this->log_path : $this->config['log_path'];
        
        unset($this->config['cache_path']);
        unset($this->config['log_path']);
        
        //设置实例
        try {
            $this->obj = XcxFactory::getInstance($obj_type, $this->config);
        } catch (Exception $e){
            throw $e;
        }
    }
    
    
    /**
     * 直接调用实例的方法
     * @param string $method_name 方法名
     * @param array $params 参数
     */
    public function __call($method_name, $params){
        if (!empty($this->obj)){
            if (method_exists($this->obj, $method_name)){
                return call_user_func_array([$this->obj, $method_name], $params);
            } else {
                throw new Exception("Unknow method:".$method_name);
            }            
        }
    }
    
    
}