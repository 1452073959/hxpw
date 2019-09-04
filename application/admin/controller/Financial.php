<?php

// +----------------------------------------------------------------------
// | 统计报表
// +----------------------------------------------------------------------
namespace app\admin\controller; 

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;
use think\Request;

class Financial extends Adminbase{
    //订单列表 (只显示 合同价-未审 合同价以审 结算价) 未审订单靠上
    public function order_list(){
        $where = [];
        if(input('status')){
            $where['status'] = input('status');
        }
        $datas = Db::name('offerlist')->where($where)->order('id','desc')->paginate(10,false,['query'=>request()->param()]);
        $this->assign([
            'datas'=>$datas
        ]);
        return $this->fetch();
    }

   
}