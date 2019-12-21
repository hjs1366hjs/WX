<?php

namespace App\Http\Controllers\Text;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TextController extends Controller
{
    //
    public function textxml()
    {
        $xml_str = '<xml>
                        <ToUserName><![CDATA[gh_ac3aca1c15ad]]></ToUserName>
                        <FromUserName><![CDATA[oELurxD8LCH5TTkL2NHZKsHK3MJg]]></FromUserName>
                        <CreateTime>1576897370</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[aaa]]></Content>
                        <MsgId>22575739709751413</MsgId>
                    </xml>';

        $xml_arr = simplexml_load_string($xml_str);
        echo '<pre>';print_r($xml_arr);echo '</pre>';
    }
}
