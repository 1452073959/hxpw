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
        $condition = [];
        $where['status'] = [3,4,5,6,7];
        if($this->admininfo['roleid'] != 1 && $this->admininfo['roleid'] != 17){
            $where['gcmanager_id'] = $this->admininfo['userid'];
        }
        $where['frameid'] = $this->admininfo['companyid'];
        $field = 'id,customer_name,address,area,room_type,status,discount_proquant,sign_bill_time,jid,oid,work_status,work_time,in_check,sign_bill_time';
        $userlist = Db::name('userlist')->where($where)->where('jid','>','0')->field($field)->order('sign_bill_time','asc')->select();
        if(!$userlist){
            $this->json(2,'none');
        }
        // $jl_id = array_unique(array_column($userlist,'jid'));
        $admininfo = array_column(Db::name('admin')->field('userid,name,pid')->where(['companyid'=> $this->admininfo['companyid'],'roleid'=>13,'status'=>1])->select(), null,'userid');
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
        //把没有正在施工的监理加进去
        foreach($admininfo as $k=>$v){
            if(!isset($datas[$v['userid']])){
                $datas[$v['userid']]['num'] = 0;
                $datas[$v['userid']]['total_price'] = 0;
                $datas[$v['userid']]['name'] = $v['name'];
                $datas[$v['userid']]['pid'] = $v['pid'];
                $datas[$v['userid']]['userlist'] = [];
            }
        }
        // $this->json(0,'success',$datas);
        $this->json(0,'success',['datas'=>$datas]);
    }

    //获取某工地的收款记录
    public function get_money(){
        $userinfo = Db::name('userlist')->where(['id'=>input('id')])->find();
        $order_info = Model('admin/offerlist')->get_order_info($userinfo['oid'],2);//原单
        // var_dump($userinfo);die;
        $append_list = Model('admin/offerlist')->get_append_info(input('id'));//增减项
        $get_money = array_column(Db::name('financial')->field('id,userid,fid,sum(money) as money,type,remark,addtime')->where(['userid'=>input('id'),'type'=>[1,2,3,4]])->group('type')->select(),null,'type');//收款详情
        $cost_tmp = Db::name('cost_tmp')->where(['f_id'=>$userinfo['frameid']])->find();
        if(!$cost_tmp){
            $this->error('未设置收款比率，请先设置');
        }

        $data = [];
        $data[1] = round($order_info['discount_proquant'] * $cost_tmp['take_rate1']/100,2);
        $data[2] = round($order_info['discount_proquant'] * $cost_tmp['take_rate2']/100,2);
        $data[3] = round($order_info['discount_proquant'] * $cost_tmp['take_rate3']/100,2);
        $data[4] = round($order_info['discount_proquant'] - $data[1] - $data[2] - $data[3],2);
        $total_money = $order_info['discount_proquant'];
        //加上增减项的
        foreach($append_list as $k=>$v){
            $total_money += $v['discount_proquant'];
            $append_total = 0;
            if($k <= 1){
                $data[1] += round($v['discount_proquant']*$cost_tmp['take_rate1']/100,2);
                $append_total += round($v['discount_proquant']*$cost_tmp['take_rate1']/100,2);
            }
            if($k <= 2){
                if($k == 2){
                    $rate = $cost_tmp['take_rate1'] + $cost_tmp['take_rate2'];
                }else{
                    $rate = $cost_tmp['take_rate2'];
                }
                $data[2] += round($v['discount_proquant']*$rate/100,2);
                $append_total += round($v['discount_proquant']*$rate/100,2);
            }
            if($k <= 3){
                if($k == 3){
                    $rate = $cost_tmp['take_rate1'] + $cost_tmp['take_rate2'] + $cost_tmp['take_rate3'];
                }else{
                    $rate = $cost_tmp['take_rate3'];
                }
                $data[3] += round($v['discount_proquant']*$rate/100,2);
                $append_total += round($v['discount_proquant']*$rate/100,2);
            }
            $data[4] += round($v['discount_proquant'] - $append_total,2);
        }
        $new_data = [];
        $new_data[5]['get'] = 0;
        $new_data[5]['give'] = 0;
        foreach($data as $k=>$v){
            $new_data[$k]['give'] = $v;
            if(isset($get_money[$k])){
                $new_data[$k]['get'] = $get_money[$k]['money'];
                $new_data[$k]['get_time'] = date('Y-m-d',strtotime($get_money[$k]['addtime']));
            }else{
                $new_data[$k]['get'] = 0;
            }
            $new_data[5]['get'] += $new_data[$k]['get'];
            $new_data[5]['give'] += $new_data[$k]['give'];
        }
        $new_data[5]['get'] = round($new_data[5]['get'],2);
        $new_data[5]['give'] = round($new_data[5]['give'],2);
        $this->json(0,'success',$new_data);
    }

    //获取领料审核
    public function getPickAudit(){
        $where = [];
        $where['status'] = 1;
        $where['f_id'] = $this->admininfo['companyid'];
        $picking_material = Db::name('picking_material')->where($where)->order('id','desc')->select();
            
        if(!$picking_material){
            $this->json(0,'success',[]);
        }
        foreach($picking_material as $k=>$v){
            //历史领料金额总额
            $picking_material[$k]['actual_total_money'] = Db::name('picking_material')->where(['oid'=>$v['oid'],'status'=>[2,3,4]])->sum('actual_total_money');
            $picking_material[$k]['j_name'] = Db::name('admin')->where(['userid'=>$v['adminid']])->value('name');
            $picking_material[$k]['addtime'] = date('Y-m-d',strtotime($v['addtime']));
        }
        $this->json(0,'success',$picking_material);
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