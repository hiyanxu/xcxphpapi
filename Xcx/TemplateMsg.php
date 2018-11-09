<?php
namespace xcxphpsdk\Xcx;

/**
 * 模板消息
 * @author hiyanxu
 * @date 2018-10-26
 */
class TemplateMsg extends Common{
    public $msg_url = self::API_URL_PREFIX."message/wxopen/template/send?access_token=";
    
    /**
    * 发送模板消息
    * @date:2018年10月26日 下午3:53:01
    * @author:hiyanxu
    * @param array $data 请求参数
    */
    public function sendTempleteMsg($data){
        //回去access_token
        $access_token = $this->getAccessToken();
        
        //请求
        $res = self::httpPost($this->msg_url.$access_token, json_encode($data));
        if ($res){
            $res_arr = json_decode($res, true);
            if (empty($res_arr) || $res_arr['errcode'] != 0){
                $this->setError($res_arr);
                Log::write("[小程序通知]请求access_token，返回error：".json_encode($res), 'error');
            } 
            return $res_arr;
        } else {
            return false;
        }
    }
}