<?php
// +----------------------------------------------------------------------
// | 后台订单管理
// +----------------------------------------------------------------------
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Session;
class Offerlist extends Model
{
    //获取订单详情
    public function get_order_info($id){ //offerquota表 的id
        $offerlist_info = $this->where(['id'=>$id])->find();
        $content = json_decode($offerlist_info['content'],true);
        if(is_array($content)){
            foreach($content as $keys => $values){
                $offerlist_info['matquant'] += $values['quotaall'];//辅材报价
                $offerlist_info['manual_quota'] += $values['craft_showall'];//人工报价
            }
        }
        $offerlist_info['direct_cost'] = $offerlist_info['matquant']+$offerlist_info['manual_quota'];//工程直接费= 辅材报价+人工报价
        //=========================计算毛利开始

            

            // // $offerlist_info['sundry'] //运杂
            // // $offerlist_info['discount'] //优惠
            //搬运费 比率*工程直接费
            $offerlist_info['carry'] = round($offerlist_info['carry']/100*$offerlist_info['direct_cost'],2);

            //清洁费 比率*工程直接费
            $offerlist_info['clean'] = round($offerlist_info['clean']/100*$offerlist_info['direct_cost'],2);

            //旧房局部改造费 比率*工程直接费
            $offerlist_info['old_house'] = round($offerlist_info['old_house']/100*$offerlist_info['direct_cost'],2);

            //管理费 比率*(工程直接费+搬运费)
            $offerlist_info['tubemoney'] = round($offerlist_info['tubemoney']/100*($offerlist_info['direct_cost']+$offerlist_info['carry']),2);

            

            //远程费 比率*(辅材+人工+管理+搬运+清洁+旧房改造)
            $offerlist_info['remote'] = round($offerlist_info['remote']/100 * ($offerlist_info['matquant']+$offerlist_info['manual_quota']+$offerlist_info['tubemoney']+$offerlist_info['carry']+$offerlist_info['clean']+$offerlist_info['old_house']),2);

            //工程意外险 比率*(工程直接费+搬运费+清洁费+管理费+旧房+远程)
            $offerlist_info['accident'] = round($offerlist_info['accident']/100*($offerlist_info['matquant']+$offerlist_info['manual_quota']+$offerlist_info['carry']+$offerlist_info['clean']+$offerlist_info['tubemoney']+$offerlist_info['old_house']+$offerlist_info['remote']),2);

            //税金 比率*(工程直接费+搬运费+清洁费+旧房+管理费+远程费+工程意外险-优惠)
            $offerlist_info['taxes'] = round($offerlist_info['taxes']/100*($offerlist_info['direct_cost']+$offerlist_info['carry']+$offerlist_info['clean']+$offerlist_info['old_house']+$offerlist_info['tubemoney']+$offerlist_info['remote']+$offerlist_info['accident']-$offerlist_info['discount']),2);

            //工程报价 辅材+人工+管理+搬运+清洁+意外险+旧房改造+税金+远程
            //原始工程报价
            $offerlist_info['proquant'] = round($offerlist_info['matquant']+$offerlist_info['manual_quota']+$offerlist_info['tubemoney']+$offerlist_info['carry']+$offerlist_info['clean']+$offerlist_info['accident']+$offerlist_info['old_house']+$offerlist_info['taxes']+$offerlist_info['remote'],2);

            //优惠后工程报价 工程报价-优惠
            $offerlist_info['discount_proquant'] = $offerlist_info['proquant']-$offerlist_info['discount'];

            //计算杂项
            $offerlist_info['supervisor_commission'] = round($offerlist_info['supervisor_commission']/100*$offerlist_info['discount_proquant'],2);//监理提成
            $offerlist_info['design_commission'] = round($offerlist_info['design_commission']/100*$offerlist_info['discount_proquant'],2);;//设计提成
            $offerlist_info['repeat_commission'] = round($offerlist_info['repeat_commission']/100*$offerlist_info['discount_proquant'],2);;//回头客奖
            $offerlist_info['business_commission'] = round($offerlist_info['business_commission']/100*$offerlist_info['discount_proquant'],2);;//业务提成

            //计算总人工成本
            $artificial = json_decode($offerlist_info['artificial'],true);
            $offerlist_info['artificial_cb'] = 0;
            foreach($artificial as $k=>$v){
                $offerlist_info['artificial_cb'] += ($v['num']*$v['cb']);//人工总成本
            }
            //计算辅材成本
            $material = json_decode($offerlist_info['material'],true);
            $offerlist_info['material_cb'] = 0;
            foreach($material as $k=>$v){
                $offerlist_info['material_cb'] += ($v['omit_num']*$v['price']);//辅材总成本
            }
            //计算毛利 利润/报价
            if($offerlist_info['direct_cost']){
                //工程毛利 优惠后工程报价 - 辅材成本-人工成本
                $offerlist_info['gross_profit'] = round(($offerlist_info['discount_proquant'] - $offerlist_info['artificial_cb'] - $offerlist_info['material_cb'] ),2);
                //毛利率
                $offerlist_info['profit_rate'] = round( $offerlist_info['gross_profit'] / $offerlist_info['discount_proquant'] * 100,2);
                //总毛利   工程毛利 - 4个提成 - 运杂 
                $offerlist_info['gross_profit_total'] = round($offerlist_info['gross_profit'] - $offerlist_info['supervisor_commission'] - $offerlist_info['design_commission'] - $offerlist_info['repeat_commission'] - $offerlist_info['business_commission'] - $offerlist_info['sundry'],2);
                //总毛利率 
                $offerlist_info['profit_rate_total'] = round( $offerlist_info['gross_profit_total'] / $offerlist_info['discount_proquant'] * 100,2);

            }else{
                $offerlist_info['gross_profit']  = 0;
                $offerlist_info['profit_rate']  = 0;
                $offerlist_info['gross_profit_total']  = 0;
                $offerlist_info['profit_rate_total']  = 0;
            }
        return $offerlist_info;
    }

    //根据项目编号返回订单明细
    public function get_info_for_item($id){
        $arr = [];
        $total = ['a_price'=>0,'a_cb'=>0,'m_price'=>0,'m_cb'=>0];
        $offerlist = Db::name('offerlist')->where(['id'=>$id])->find();
        $content = json_decode($offerlist['content'],true);
        $content = $content?$content:[];
        foreach($content as $k=>$v){
             if(!isset($arr[$v['type_of_work']])){
                $arr[$v['type_of_work']]['m_cb'] = 0;
                $arr[$v['type_of_work']]['a_cb'] = 0;
                $arr[$v['type_of_work']]['m_price'] = 0;
                $arr[$v['type_of_work']]['a_price'] = 0;
            }
            $arr[$v['type_of_work']]['m_price'] += $v['quota']*$v['gcl'];//辅材单价
            $arr[$v['type_of_work']]['a_price'] += $v['craft_show']*$v['gcl'];//人工单价
            $arr[$v['type_of_work']]['a_cb'] += $v['labor_cost']*$v['gcl'];//人工成本

            $total['m_price'] += $v['quota']*$v['gcl'];
            $total['a_price'] +=$v['craft_show']*$v['gcl'];
            $total['a_cb'] += $v['labor_cost']*$v['gcl'];
        }
        //辅材成本
        $order_material = Db::name('order_material')->where(['o_id'=>$id,'status'=>1])->select();//该订单全部辅料
        foreach($order_material as $k=>$v){
            if(!isset($arr[$v['type_of_work']])){
                $arr[$v['type_of_work']]['m_cb'] = 0;
            }
            $arr[$v['type_of_work']]['m_cb'] += $v['cb']*$v['num'];//辅材成本
            $total['m_cb'] += $v['cb']*$v['num'];
        }
        return ['datas'=>$arr,'total'=>$total];
    }

    //领料清单
    //type 0-系数后 1->系数前 
    public function get_material_list($id,$type=1){
        $order_material = Db::name('order_material')->where(['o_id'=>$id,'status'=>1])->select();
        $datas = [];
        foreach($order_material as $k=>$v){
            $v['fine'] = $v['fine']?$v['fine']:'通用';
            if(!isset($datas[$v['type_of_work']][$v['fine']][$v['amcode']])){
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['amcode'] = $v['amcode']; //辅材编码
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['m_name'] = $v['m_name']; //辅材名称
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['brand'] = $v['brand']; //品牌
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['img'] = $v['img']; //图片
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['units'] = $v['units']; //单位
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['num'] = 0; //数量
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['phr'] = $v['phr']; //包装规格
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['cb'] = $v['cb']; //单价
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['price_total'] = 0; //总价
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['coefficient'] = $v['coefficient'];
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['important'] = $v['important'];
            }
            $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['num'] += $v['num'];
        }
        //处理系数
        foreach($datas as $k1=>$v1){
            foreach($v1 as $k2=>$v2){
                foreach($v2 as $k3=>$v3){
                    $num = explode('.',$v3['num']);
                    if(!isset($num[1])){
                        $num[1] = 0;
                    }
                    if($num[1]*10 > $v3['coefficient']){
                        $datas[$k1][$k2][$k3]['omit_num'] = ceil($v3['num']);
                    }else{
                        //不足1时向上取证
                        if($v['num'] < 1 && $v['important']){
                            $datas[$k1][$k2][$k3]['omit_num'] = ceil($v3['num']);
                        }else{
                            $datas[$k1][$k2][$k3]['omit_num'] = floor($v3['num']);
                        }
                    }
                    $datas[$k1][$k2][$k3]['coefficient'] += $datas[$k1][$k2][$k3]['omit_num'] * $v3['cb'];
                }
            }
        }
        return $datas;
    }
}
