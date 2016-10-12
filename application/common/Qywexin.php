<?php
/**
 * Created by PhpStorm.
 * User: FuJinsong
 * Date: 2016/8/9
 * Time: 15:46
 */

namespace app\common;
use think\Cache;
use think\Request;


class Qyweixin
{


    private $appId="wx06693892f83ef14c";
    private $appSecret="gpVpxWBga2dtl2oi5vtRKktzUNpxQLro7zoqE81LLijLbKW_nmewbCZ7ZWj5jpn8";

    /**
     * 在每次主动调用企业号接口时需要带上AccessToken参数。AccessToken参数由CorpID和Secret换取
     * Qyweixin constructor.
     * @param Request $appId
     * @param $appSecret
     */
    public function init($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
//        $qywx="https://qyapi.weixin.qq.com";
//        $corpid="wx06693892f83ef14c";      //CorpID是企业号的标识，每个企业号拥有一个唯一的CorpID；
//        $secret="gpVpxWBga2dtl2oi5vtRKktzUNpxQLro7zoqE81LLijLbKW_nmewbCZ7ZWj5jpn8";     //Secret是管理组凭证密钥。
//        $api_url="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$corpid&corpsecret=$secret";
    }
    /**
     * 生成签名的随机串
     * @param int $length
     * @return string
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     *  产生需要的参数
     * @return array
     */
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();// 生成签名的随机串
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    /**
     *  获取JS API的Ticket
     *  api_ticket 是用于调用微信卡券JS API的临时票据，有效期为7200 秒，通过access_token 来获取。
     * @return mixed
     */
    private function getJsApiTicket() {

        if(Cache::get()==false){
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
             $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
//            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                   Cache::set('jsapi_ticket',$ticket,7000);//  默认过期时间为7000ms
            }
        }else{
              $ticket= Cache::get();
        }
        return $ticket;
    }

    /**
     * 从微信获取调用API 的Access Token，并将其保存在redis缓存中
     * @return mixed
     */
    private function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        if(Cache::get("token")==false){
            // 如果是企业号用以下URL获取access_token
             $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
//            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                Cache::set('token',$access_token,7000);//  默认过期时间为7000ms
             }
        }else{
            $access_token=Cache::get("token");
        }
        return $access_token;
    }

    /**
     * 调用HTTP GET请求
     * @param $url
     * @return mixed
     */
    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

}