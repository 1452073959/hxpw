<?php

// +----------------------------------------------------------------------
// | 工程管理
// +----------------------------------------------------------------------
namespace app\admin\controller; 

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;
use think\Request;

class ProjectManager extends Adminbase{
    public $show_page = 20;
    //工程派单
    public function send_order_index(){
        $admininfo = $this->_userinfo;
        $where = [];
        $condition = [];
        if(input('name')){
            $where[] = ['customer_name','like','%'.input('name').'%'];
        }
        if(input('begin_time') && input('end_time')){
            $condition = array(['sign_bill_time','>',strtotime(input('begin_time'))],['sign_bill_time','<',strtotime('+1 day',strtotime(input('end_time')))]);
        } 
        $where[] = ['status','>','2'];
        $where[] = ['jid','=','0'];
        $where[] = ['frameid','=',$admininfo['companyid']];
        $datas = Db::name('userlist')->where($where)->where($condition)->paginate($this->show_page,false,['query'=>request()->param()]);
        $this->assign('datas',$datas);
        return $this->fetch();
    }

    public function ajax_get_supervision(){
        $admininfo = $this->_userinfo;
        $where = [];
        $where['a.roleid'] = 13;
        $where['a.status'] = 1;
        $where['a.companyid'] = $admininfo['companyid'];
        $datas = Db::name('admin')->alias('a')->leftjoin('userlist u','a.userid = u.jid')->field(['a.*', 'SUM(u.discount_proquant)'=>'total_money','count(u.jid)'=>'total_count','SUM(u.status=3)'=>'count1','SUM(u.status=4)'=>'count2','SUM(u.status=5)'=>'count3','SUM(u.status=6)'=>'count4'])->where($where)->group('a.userid')->select();
        $this->success('success','',$datas);
        // var_dump($datas);die;
    }

    //派单操作
    public function send_order(){
        if(input('uid') && input('jid')){
            $where = [];
            $where[] = ['id','=',input('uid')];
            $where[] = ['jid','=',0];
            $where[] = ['status','>',2];
            if(Db::name('userlist')->where($where)->count()){
                $res = Db::name('userlist')->where($where)->update(['jid'=>input('jid')]);
                if($res){
                    $this->success('派单成功');
                }else{
                    $this->error('派单失败');
                }
            }else{
                $this->error('参数错误1');
            }
        }else{
            $this->error('参数错误');
        }
    }
}