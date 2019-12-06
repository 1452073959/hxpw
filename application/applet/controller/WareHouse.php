<?php
// +----------------------------------------------------------------------
// | 仓管
// +----------------------------------------------------------------------
namespace app\applet\controller;
use app\admin\model\PickingMaterial;
use think\Db;
use think\Controller;
use app\applet\controller\UserBase;
 
class WareHouse extends UserBase{
    //获取领料列表
    public function getOrder(){
        $where = [];
        if(input('status') == 1){
            //全部订单
            //审核不通过和未审核不需要
            $where['status'] = [2,3,4];
        }else{
            $where['status'] = input('status');
        }
        $where['f_id'] = $this->admininfo['companyid'];
        $picking_material = PickingMaterial::with(['userlist','jianli'])->where($where)->order('id','desc')->paginate(15,false,['query'=>request()->param()])->each(function($item, $key){
            $item['addtime'] = date('Y-m-d',strtotime($item['addtime']));
            $info = Db::name('picking_material_info')->where(['pmid'=>$item['id']])->order('id','asc')->select();
            foreach($info as $k=>$v){
                if($item['status'] == 3 || $item['status'] == 4){
                    $info[$k]['num'] = $info[$k]['actual_num'];
                }
                $info[$k]['total_price'] = round($info[$k]['num'] * $info[$k]['price'],2) ;
            }
            if($item['status'] == 3 || $item['status'] == 4){
                $item['total_money'] = $item['actual_total_money'];
            }
            
            $item['info'] = $info;
            return $item;
        });
        if(!$picking_material){
            //为空 未领料
            $this->json(2,'none',[]);
        }

        $this->json(0,'success',['datas'=>$picking_material,'status'=>input('status')]);
    }

    //获取某用户全部领料列表
    public function getOrderByUser(){
        $where = [];
        $where['userid'] = input('uid');
        $where['status'] = [2,3,4];
        $picking_material = Db::name('picking_material')->where($where)->order('id','desc')->select();
        foreach($picking_material as $key=>$val){
            $picking_material[$key]['addtime'] = date('Y-m-d',strtotime($val['addtime']));
            $info = Db::name('picking_material_info')->where(['pmid'=>$val['id']])->order('id','asc')->select();
            foreach($info as $k=>$v){
                $info[$k]['total_price'] = round($info[$k]['actual_num'] * $info[$k]['price'],2);
            }
            $picking_material[$key]['info'] = $info;
        }
        $this->json(0,'success',$picking_material);
    }

    //获取领料单条
    public function getOrderInfo(){
        $id = input('id');
        $picking_material = Db::name('picking_material')->where(['id'=>$id])->find();
        $picking_material_info = array_column(Db::name('picking_material_info')->where(['pmid'=>$picking_material['id']])->order('id','asc')->select(),null, 'id');
        foreach($picking_material_info as $k=>$v){
            $picking_material_info[$k]['img'] = $this->getImgSrc($v['img']);
            $picking_material_info[$k]['total_price'] = ($v['actual_num']?$v['actual_num']:$v['num']) * $v['price'];
        }
        $picking_material['info'] = $picking_material_info;
        $this->json(0,'success',$picking_material);
    }

    //提交配货内容
    public function submitAllot(){
        $id = input('id');
        $datas = input('datas');
        $total_money = 0;
        $status = Db::name('picking_material')->where(['id'=>$id])->value('status');
        if(!$status || $status != 2){
            $this->json(2,'该订单已配货或订单有误');
        }
        try {
            foreach($datas as $k=>$v){
                Db::name('picking_material_info')->where(['id'=>$v['id']])->update(['actual_num'=>$v['num']]);
                $total_money += $v['num']*$v['price'];
            }
            Db::name('picking_material')->where(['id'=>$id])->update(['actual_total_money'=>$total_money,'status'=>3]);

            // 提交事务
            Db::commit();
            $this->json(0,'配货成功');
        } catch (\Exception $e) {
            $this->json(2,'配货失败');
            Db::rollback();
        }
        
    }

    //确认领料
    public function confirmPick(){
        $id = input('id');
        $status = Db::name('picking_material')->where(['id'=>$id])->value('status');
        if(!$status || $status != 3){
            $this->json(2,'订单状态有误');
        }
        $res = Db::name('picking_material')->where(['id'=>$id])->update(['status'=>4,'gettime'=>time()]);
        if($res){
            $this->json(0,'确认领料成功');
        }else{
            $this->json(2,'确认领料失败');
        }
    }

}