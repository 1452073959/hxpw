<?php

// +----------------------------------------------------------------------
// | 统计报表
// +----------------------------------------------------------------------
namespace app\admin\controller; 

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;
use think\Request;

class Statistical extends Adminbase{
    //业绩统计
    public function results_index(){
        return $this->fetch();
    }

    //领料统计
    public function picking_index(){
        return $this->fetch();
    }

    //工程派单
    public function send_order_index(){
        return $this->fetch();
    }

    //监理管理
    public function supervision_index(){
        return $this->fetch();
    }

    //在施工地
    public function in_word(){
        return $this->fetch();
    }
    //模拟返回数据
    public function return_data(){
        $data =[
                    [
                        "td1"=>"商务经理",
                        "td2"=>"张三",
                        "td3"=>"101900",
                        "td4"=>"51%",
                        "td5"=>"1250",
                        "td6"=>"8",
                        "td7"=>"2",
                        "td8"=>"6",
                        "td9"=>"0",
                        "td10"=>"0",
                        "td11"=>"0",
                    ],
                    [
                        "td1"=>"报价师",
                        "td2"=>"李四",
                        "td3"=>"56900",
                        "td4"=>"49%",
                        "td5"=>"850",
                        "td6"=>"7",
                        "td7"=>"2",
                        "td8"=>"2",
                        "td9"=>"1",
                        "td10"=>"2",
                        "td11"=>"0",
                    ],
                    [
                        "td1"=>"商务经理",
                        "td2"=>"小明",
                        "td3"=>"12900",
                        "td4"=>"45%",
                        "td5"=>"850",
                        "td6"=>"3",
                        "td7"=>"0",
                        "td8"=>"1",
                        "td9"=>"0",
                        "td10"=>"2",
                        "td11"=>"0",
                    ],
                    [
                        "td1"=>"商务经理",
                        "td2"=>"黄五",
                        "td3"=>"99800",
                        "td4"=>"47%",
                        "td5"=>"1150",
                        "td6"=>"11",
                        "td7"=>"2",
                        "td8"=>"3",
                        "td9"=>"1",
                        "td10"=>"4",
                        "td11"=>"2",
                    ]
                ];
        echo json_encode(array('code'=>0,'count'=>count($data),'data'=>$data,'msg'=>'ok'));
    }
    public function return_department_data(){
        echo '{"code":0,"msg":"ok","data":[{"id":0,"name":"\u5e7f\u5dde\u5206\u516c\u53f8","other":"\u5206\u516c\u53f8","levelid":3,"pid":-1,"status":0},{"id":1,"fid":152,"name":"\u8425\u4e1a\u90e8","pid":0,"info_pid":"0","sort":0,"remark":"\u8425\u4e1a\u90e8","addtime":"1567051211"},{"id":15,"fid":152,"name":"\u8bbe\u8ba1\u4e8c\u90e8","pid":3,"info_pid":"0-3","sort":0,"remark":"","addtime":"1567238498"},{"id":3,"fid":152,"name":"\u8bbe\u8ba1\u90e8","pid":0,"info_pid":"0","sort":0,"remark":"\u8bbe\u8ba1\u90e8","addtime":"1567051255"},{"id":5,"fid":152,"name":"\u8bbe\u8ba1\u4e00\u90e8","pid":3,"info_pid":"0-3","sort":0,"remark":"","addtime":"1567051357"},{"id":6,"fid":152,"name":"\u5e02\u573a\u90e8","pid":1,"info_pid":"0-1","sort":0,"remark":"","addtime":"1567051561"},{"id":7,"fid":152,"name":"\u5de5\u7a0b\u90e8","pid":0,"info_pid":"0","sort":0,"remark":"","addtime":"1567070315"},{"id":8,"fid":152,"name":"\u5de5\u7a0b\u4e00\u90e8","pid":7,"info_pid":"0-7","sort":0,"remark":"","addtime":"1567070339"},{"id":10,"fid":152,"name":"\u4ed3\u5e93","pid":8,"info_pid":"0-7-8","sort":0,"remark":"","addtime":"1567130232"},{"id":11,"fid":152,"name":"\u65bd\u5de5","pid":8,"info_pid":"0-7-8","sort":0,"remark":"","addtime":"1567130313"},{"id":12,"fid":152,"name":"\u884c\u653f\u90e8","pid":0,"info_pid":"0","sort":0,"remark":"","addtime":"1567147708"},{"id":13,"fid":152,"name":"\u4eba\u529b\u90e8","pid":12,"info_pid":"0-12","sort":0,"remark":"","addtime":"1567147718"},{"id":14,"fid":152,"name":"\u4f1a\u8ba1","pid":12,"info_pid":"0-12","sort":0,"remark":"","addtime":"1567152792"}],"count":13}';
    }
}