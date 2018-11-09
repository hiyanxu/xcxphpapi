<?php
namespace xcxphpsdk\Xcx;

/**
 * 日志记录
 * @author hiyanxu
 * @date 2018-11-09
 */
class Log {
    public static $log_path;
    
    //几种日志类型
    const LOG    = 'log';
    const ERROR  = 'error';
    const INFO   = 'info';
    const MSG    = 'msg';
    const DEBUG  = 'debug';
    
    public static $log_type = ['log', 'error', 'info', 'msg', 'debug'];
    
    /**
    * 写入日志
    * @date:2018年11月9日 下午4:51:12
    * @author:hiyanxu
    * @param string $msg 日志内容
    * @param string $log_type 日志类型
    */
    public static function write($msg, $log_type='error'){
        if (self::checkLogPath(self::$log_path)){
            $filename = self::$log_path.date('Ym', time()).DIRECTORY_SEPARATOR.date('d', time()).'.log';
            $fp = file($filename, 'a');
            if (!in_array($log_type, self::$log_type)){
                throw new \Exception("Unknow log type：".$log_type);
            }
            if (flock($fp, LOCK_EX)){
                $msg = "【".strtoupper($log_type)."】".$msg."\r\n";
                fwrite($fp, $msg);
                flock($fp, LOCK_UN);
            }
            
            fclose($fp);
        } else {
            throw new \Exception("Create log path fail");
        }
    }
    
    /**
     * 检查path
     * @author hiyanxu 2018-10-26
     */
    public static function checkLogPath($log_path){
        if (empty(self::$log_path) && empty($log_path)){
            self::$log_path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'xcxlog' . DIRECTORY_SEPARATOR;
        } else if (empty(self::$log_path) && !empty($log_path)){
            self::$log_path = $log_path;
        }
        self::$log_path = rtrim(self::$log_path, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir(self::$log_path) && !mkdir(self::$log_path, 0755, true)) {  //没有该目录，则新建立目录
            return false;
        }
        return true;
    }
}