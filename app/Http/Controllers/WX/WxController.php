<?php
namespace App\Http\Controllers\WX;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WeixinModel;
use Illuminate\Contracts\Redis;
class WxController extends Controller
{

    protected $access_token;

    public function __construct()
    {
        //获取access_token
        $this->access_token = $this->getAccessToken();
    }


    protected function getAccessToken()
    {
        $keys = "wx_access_token";
        $access_token = Redis::get($keys);
        if ($access_token) {
            return $access_token;
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . env('WX_APPID') . '&secret=' . env('WX_APPSECREET');
        $data_json = file_get_contents($url);
        $arr = json_decode($data_json, true);
        Redis::set($keys, $arr['access_token']);
        Redis::expire($keys, 3600);
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
        $log_file = "wx.log";       // public
        //将接收的数据记录到日志文件
        $xml_str = file_get_contents("php://input");
        $data = date('Y-m-d H:i:s')  . ">>>>>>\n" . $xml_str . "\n\n";
        file_put_contents($log_file,$data,FILE_APPEND);     //追加写
        //处理xml数据
        $xml_obj = simplexml_load_string($xml_str);
        $event = $xml_obj->Event;       // 获取事件类型
        $openid = $xml_obj->FromUserName;       //获取用户的openid

        if($event=='subscribe'){
            //判断用户是否已存在
            $u = WeixinModel::where(['openid'=>$openid])->first();
            if($u){
                $msg = '欢迎回来';
                $xml = '<xml>
                            <ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.$msg.']]></Content>
                        </xml>';
                echo $xml;
            }else{
                //获取用户信息 zcza
                $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN';
                $user_info = file_get_contents($url);       //
                $u = json_decode($user_info,true);

                //入库用户信息
                $user_data = [
                    'openid'    => $openid,
                    'nickname'  => $u['nickname'],
                    'sex'       => $u['sex'],
                    'headimgurl'    => $u['headimgurl'],
                    'subscribe_time'    => $u['subscribe_time']
                ];
                //openid 入库
                $uid = WeixinModel::insertGetId($user_data);
                $msg = "谢谢关注";
                //回复用户关注
                $xml = '<xml>
                            <ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.$msg.']]></Content>
                        </xml>';
                echo $xml;
            }
        }


        }
    }


    public function getuserInfo($access_token,$openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
    }
}