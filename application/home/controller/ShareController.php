<?php
namespace app\home\controller;
use think\facade\Request;
use think\Controller;
use app\home\model\TimePlane;


//分享
class ShareController extends Controller{
      // 线上
     // const APPID    = 'wx2a995adc5dc1e56e';
     // const SECRET   = 'ca368106e571441243bd3ac068cd43dc';
     // const SECRETURL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&';
     // const JSAPITICKEURL = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?';

    // 线下
     const APPID    = 'wxffd1ae5542831501';
     const SECRET   = '9aa8f7840b3486e9a216bddd476d0f6f';
     const SECRETURL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&';
     const JSAPITICKEURL = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?';

      // 获取JsapiTicke
    public function getJsapiTicke(){
        // 获取access_token
    		$getToken = $this->getAccessToken();
        if($getToken["code"] == -1){
           $this->returnJson(-1,'获取token失败');
        }
        // 获取jsapi_ticket
        $getJsapiTicket  = $this->getJsapiTicket($getToken["data"]);
        if( $getJsapiTicket["code"] == -1){
           $this->returnJson(-1,'获取ticket失败');
        }
        $arr = array(
            'appid' =>  self::APPID,
            'timestamp' =>time(),
            'noncestr' => $this->nonce_str(),
        );
        $arrTmp = array(
            'jsapi_ticket'=>$getJsapiTicket["data"],
            'noncestr'=>$arr['noncestr'],
            'timestamp'=>$arr['timestamp'],
            'url'=>'http://signing.beliwin.com'
        );
          $stringA = '';
          foreach ($arrTmp as $key => $value) {
            if(!$value) continue;
            if($stringA) $stringA .= "&".$key."=".$value;
            else $stringA = $key."=".$value;
          }
        $arr['signature'] = sha1($tmp);

       $this->returnJson(1,'',$arr);
     
    }

    // 	获取access_token
    private  function getAccessToken(){
        $result = array("code"=>0,"msg"=>'',"data"=>'');
    		$url = self::SECRETURL.'appid=' .self::APPID. '&secret=' .self::SECRET;
        $res = $this->curl_post($url,'');
        $getsecret = json_decode($res,true);
        if(isset($getsecret["access_token"])){
           $result["data"] = $getsecret["access_token"];

        }else{
           $result = array("code"=>-1,"msg"=>'获取access_token失败',"data"=>'');
        }
        return $result;

    }

    // 获取jsapi_ticket
    private function getJsapiTicket($access_token){
        $result = array("code"=>0,"msg"=>'',"data"=>'');
        $url = self::JSAPITICKEURL.'access_token=' . $access_token .'&type=jsapi';
        $res = $this->curl_post($url,'');
        $JsapiTicke = json_decode( $res,true);

        if(isset($JsapiTicke["ticket"])){
           $result["data"] = $JsapiTicke["ticket"];
        }else{
           $result = array("code"=>-1,"msg"=>'获取jsapi_ticket失败',"data"=>'');
        }
        return $result;
    }


     function curl_post($url, $fields, $data_type='text'){
	    $cl = curl_init();
	    if(stripos($url, 'https://') !== FALSE) {
	        curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
	        curl_setopt($cl, CURLOPT_SSLVERSION, 1);
	    }
	    curl_setopt($cl, CURLOPT_URL, $url);
	    curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
	    curl_setopt($cl, CURLOPT_POST, true);    
	    curl_setopt($cl, CURLOPT_POSTFIELDS, $fields);
	    $content = curl_exec($cl);
	    $status = curl_getinfo($cl);
	    curl_close($cl);
	    if (isset($status['http_code']) && $status['http_code'] == 200) {
	        if ($data_type == 'json') {
	            $content = json_decode($content);
	        }
	        return $content;
	    } else {
	        return FALSE;
	    }

	}


	function nonce_str($len = 32) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
       // 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }


    /**
     * 数据返回格式定义
     * @param int   $code 返回状态
     * @param string $msg 返回内容
     * @param array $data 返回值
     * @return array
     */
    static public function returnJson($code, $msg = '', $data = null)
    {
        die(urldecode(json_encode((
            [
                'code' => $code,
                'msg' => $msg ,
                'data' => $data
            ]
        ))));
    }



}