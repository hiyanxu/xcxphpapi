<?php
namespace xcxphpsdk\Xcx;

/**
 * 基类
 * @author hiyanxu
 * @date 2018-10-26
 */
class Common{
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin/';
    const ACCESS_TOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&";
    /**
     * 配置config
     * @var unknown
     */
    public $config;
    
    /**
     * 错误码
     * @var unknown
     */
    public $error_code;
    
    /**
     * 错误信息
     * @var unknown
     */
    public $error_msg;
    
    public function __construct($config){
        $this->config = $config;
    }    
    
    /**
    * 获取access_token
    * @date:2018年10月26日 下午4:03:13
    * @author:hiyanxu
    * @param array $config 小程序配置
    */
    public function getAccessToken(){
        //查询缓存
        $cache_name = "wechatxcx_access_token_".$this->config['appid'];
        $cache_res = Cache::getCache($cache_name);
        if (!empty($cache_res)){
            return $cache_res;
        }
        
        //缓存没有，则请求微信服务器，获取access_token
        $url = self::ACCESS_TOKEN_URL."appid={$this->config['appid']}&secret={$this->config['secret']}";
        $res = self::httpGet($url);
        if ($res){
            $res = json_decode($res, true);
            if (isset($res['errcode'])){  //请求失败，设置error信息，记录日志
                $this->setError($res);
                Log::write("请求access_token，返回error：".json_encode($res));
                return false;
            } else {  //获取成功，记录缓存，返回
                Cache::setCache($cache_name, $res['access_token'], $res['expires_in']-1000);  //有效期设置为：微信有效期-1000
                return $res['access_token'];
            }
        } else {
            return false;
        }
        
    }
    
    /**
     * 以get方式提交请求
     * @param $url
     * @return bool|mixed
     */
    public static function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSLVERSION, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        list($content, $status) = array(curl_exec($curl), curl_getinfo($curl), curl_close($curl));
        return (intval($status["http_code"]) === 200) ? $content : false;
    }
    
    /**
     * 以post方式提交请求
     * @param string $url
     * @param array|string $data
     * @return bool|mixed
     */
    public static function httpPost($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, self::_buildPost($data));
        list($content, $status) = array(curl_exec($curl), curl_getinfo($curl), curl_close($curl));
        return (intval($status["http_code"]) === 200) ? $content : false;
    }
    
    /**
     * POST数据过滤处理
     * @param array $data
     * @return array
     */
    static private function _buildPost(&$data)
    {
        if (is_array($data)) {
            foreach ($data as &$value) {
                if (is_string($value) && $value[0] === '@' && class_exists('CURLFile', false)) {
                    $filename = realpath(trim($value, '@'));
                    file_exists($filename) && $value = new CURLFile($filename);
                }
            }
        }
        return $data;
    }
    
    /**
    * set error信息
    * @date:2018年11月9日 下午3:59:46
    * @author:hiyanxu
    * @param array $res 请求返回数据
    */
    public function setError($res){
        if (isset($res['errcode']) && !empty($res['errcode'])){
            $this->error_code = $res['errcode'];
            $this->error_msg = $res['errmsg'];
        }
    }
}