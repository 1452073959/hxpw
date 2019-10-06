<?php
// +----------------------------------------------------------------------
// | 仓管
// +----------------------------------------------------------------------
namespace app\applet\controller;
use think\Db;
use think\Controller;
use app\applet\controller\UserBase;
 
class Manager extends UserBase{

    //根据监理获取工地信息
    public function getUserListBySupervisor(){
        $where = [];
        $where['status'] = [3,4,5,6,7];
        $where['frameid'] = $this->admininfo['companyid'];
        $field = 'id,customer_name,address,area,room_type,status,discount_proquant,sign_bill_time,jid,oid,work_status,work_time,in_check,sign_bill_time';
        $userlist = Db::name('userlist')->where($where)->field($field)->order('sign_bill_time','asc')->select();
        if(!$userlist){
            $this->json(2,'none');
        }
        $jl_id = array_unique(array_column($userlist,'jid'));
        $admininfo = array_column(Db::name('admin')->field('userid,name,pid')->where(['userid'=>$jl_id])->select(), null,'userid');
        // $userlist = array_column(Db::name('userlist')->where($where)->order('sign_bill_time','asc')->select(),null, 'id');
        $datas = [];
        foreach($userlist as $k=>$v){
            if(!isset($datas[$v['jid']])){
                $datas[$v['jid']]['num'] = 0;
                $datas[$v['jid']]['total_price'] = 0;
                $datas[$v['jid']]['name'] = $admininfo[$v['jid']]['name'];
                $datas[$v['jid']]['pid'] = $admininfo[$v['jid']]['pid'];
            }
            $v['sign_bill_time'] = date('Y-m-d',$v['sign_bill_time']);
            $datas[$v['jid']]['num']++;
            $datas[$v['jid']]['userlist'][] = $v;
        }
        // $this->json(0,'success',$datas);
        $this->json(0,'success',['datas'=>$datas]);
    }

    //获取领料审核
    public function getPickAudit(){
        $where = [];
        $where['status'] = 1;
        $where['f_id'] = $this->admininfo['companyid'];
        $picking_material = Db::name('picking_material')->where($where)->order('id','desc')->select();
            
        if(!$picking_material){
            $this->json(2,'none');
        }
        foreach($picking_material as $k=>$v){
            //历史领料金额总额
            $picking_material[$k]['actual_total_money'] = Db::name('picking_material')->where(['oid'=>$v['oid'],'status'=>[2,3,4]])->sum('actual_total_money');
            $picking_material[$k]['j_name'] = Db::name('admin')->where(['userid'=>$v['adminid']])->value('name');
            $picking_material[$k]['addtime'] = date('Y-m-d',strtotime($v['addtime']));
        }
        $this->json(0,'success',['datas'=>$picking_material]);
    }

    //审核领料
    public function auditPick(){
        $where = [];
        $where['id'] = input('id');
        $picking_material = Db::name('picking_material')->where($where)->find();
        if(!$picking_material || $picking_material['status'] != 1){
            $this->json(2,'无效订单');
        }
        $res = Db::name('picking_material')->where($where)->update(['status'=>input('status'),'auditid'=>$this->admininfo['userid']]);
        if($res){
            $this->json(0,'审核成功');
        }else{
            $this->json(2,'审核失败');
        }
    }
}