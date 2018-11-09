<?php
namespace xcxphpsdk\Xcx;

use think\Exception;
/**
 * 缓存类
 * @author hiyanxu
 * @date 2018-10-26
 */
class Cache {
    /**
     * @var 缓存目录
     */
    public static $cachepath;
    
    /**
     * 检查cache_path
     * @author hiyanxu 2018-10-26
     */
    public static function checkCachePath($cache_path){
        //若未设置cache_path，则在当前目录下建立Cache放置缓存
        if (empty(self::$cachepath) && empty($cache_path)){
            self::$cachepath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'xcxcache' . DIRECTORY_SEPARATOR;
        } else if (empty(self::$cachepath) && !empty($cache_path)){
            self::$cachepath = $cache_path;
        }
        self::$cachepath = rtrim(self::$cachepath, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir(self::$cachepath) && !mkdir(self::$cachepath, 0755, true)) {  //没有该目录，则新建立目录
            return false;
        }
        return true;
    }
    
    /**
     * 设置缓存
     * @param unknown $name
     * @param unknown $value
     * @param number $expired
     */
    public static function setCache($name, $value, $expired=0){
        if (self::checkCachePath(self::$cachepath)){
            $data = serialize(array('value' => $value, 'expired' => $expired > 0 ? time() + $expired : 0));
            file_put_contents(self::$cachepath . $name, $data);  //每次都去覆盖该缓存文件，省去了判断删除缓存的步骤
        } else {
            throw new Exception("cache path set error");
        }
    }
    
    /**
     * 获取缓存cache
     * @param unknown $name
     * @return NULL|unknown|NULL
     */
    public static function getCache($name){
        if (($file = self::$cachepath . $name) && file_exists($file) && ($data = file_get_contents($file)) && !empty($data)) {
            $data = unserialize($data);
            if (isset($data['expired']) && ($data['expired'] > time() || $data['expired'] === 0)) {
                return isset($data['value']) ? $data['value'] : null;
            }
        }
        return null;
    }
    
    
    
}