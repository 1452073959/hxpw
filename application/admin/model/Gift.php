<?php
// +----------------------------------------------------------------------
// | 后台订单管理
// +----------------------------------------------------------------------
namespace app\admin\model;
use think\Model;
use think\Db;
class Gift extends Model{
    //获取订单礼品库总价
    public function getGiftTotal($oid){
        return $this->getGiftList($oid)['money'];
    }
    //获取礼品详情
    public function getGiftList($oid,$field='*'){
        $datas = [];
        $money = 0;
        $gift = Db::name('offerlist')->where(['id'=>$oid])->value('gift');
        if(empty($gift)){
            return ['data'=>$datas,'money'=>$money];
        }
        $gift = json_decode($gift,true);
        //获取礼品所有id
        $gids = array_keys($gift);
        $datas = Db::name('gift')->field($field)->where(['id'=>$gids])->select();
        foreach($datas as $k=>$v){
            $datas[$k]['num'] = $gift[$v['id']];
            $money += ($v['price']*$gift[$v['id']]);
        }
        return ['data'=>$datas,'money'=>$money];
    }
}
