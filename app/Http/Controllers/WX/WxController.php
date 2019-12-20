<?php

namespace App\Http\Controllers\WX;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WxController extends Controller
{
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
        $log_file = 'wx.log';
        $data =date('Y-m-d H:i:s') . json_encode($_POST);
        //将接收的数据记录到日志文件
        file_put_contents($log_file,$data,FILE_APPEND);
    }

    public function getuserinfo(){

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';
    }
}
