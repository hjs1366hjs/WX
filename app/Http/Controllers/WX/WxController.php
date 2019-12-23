<?php

namespace App\Http\Controllers\WX;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WxController extends Controller
{

    protected $access_token;

    public function __construct()
    {
        //获取access_token
        $thuis->access_token = $this->getAccessToken();
    }

    protected function getAccessToken()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'';
        $data_json = file_get_contents($url);
        $arr = json_decode($data_json,true);
        return $arr['access_token'];
    }

    /**
     *
     * 处理接入
     */
    public function wechat()
    {
        $token = 'qwertyuiopasdfg';       //开发提前设置好的 token
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr = $_GET["echostr"];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){        //验证通过
            echo $echostr;
        }else{
            die("not ok");
        }
    }

    /**
     * 接受微信推送事件
    **/
    public function receiv()
    {
        $log_file = 'wx.log';       //public
        //将接收的数据记录到日志文件
        $xml_str = file_get_contents("php://input");
        $data =date('Y-m-d H:i:s') . $xml_str;
        file_put_contents($log_file,$data,FILE_APPEND);

        //处理xml数据
        $xml_obj = simplexml_load_string($xml_str);

        //入库  其他逻辑
        $event = $xml_obj->Event;   //获取事件类型
        if($event=='subscribe'){
            //获取用户的openid
            $openid = $xml_obj->FromUserName;
            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$this->openid.'&lang=zh_CN';
            $user_info = file_get_contents($url);
            file_put_contents('wx_user.log',$user_info,FILE_APPEND);
        }


        //判断消息类型
        $msg_type = $xml_obj->Msg0Type;

        if($msg_type == 'text'){
            $response_text = '<xml>
                                    <ToUserName><![CDATA[toUser]]></ToUserName>
                                    <FromUserName><![CDATA[fromUser]]></FromUserName>
                                    <CreateTime>12345678</CreateTime>
                                    <MsgType><![CDATA[text]]></MsgType>
                                    <Content><![CDATA[你好]]></Content>
            </xml>';

            echo $response_text;
        }

    }

    public function getuserInfo($access_token,$openid){

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
    }
}
