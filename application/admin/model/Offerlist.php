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
    public function get_order_info($id,$type=1){ //offerquota表 的id , type ->1:合同单 2:整单
        $offerlist_info = Db::name('offerlist')->where(['id'=>$id])->find();
        $content = Db::name('order_project')->where(['type'=>1,'o_id'=>$id])->select();
        $offerlist_info['artificial_cb'] = 0;
        if(is_array($content)){
            foreach($content as $keys => $values){
                $offerlist_info['matquant'] += $values['quota']*$values['num'];//辅材报价
                $offerlist_info['manual_quota'] += $values['craft_show']*$values['num'];//人工报价
                $offerlist_info['artificial_cb'] += $values['labor_cost']*$values['num'];//人工成本
            }
        }
        $offerlist_info['direct_cost'] = $offerlist_info['matquant']+$offerlist_info['manual_quota'];//工程直接费= 辅材报价+人工报价

        if($type == 2){
            //加上增减项
            $order_appned = Db::name('order_project')->where(['type'=>2,'o_id'=>$id])->select();
            foreach($order_appned as $k=>$v){
                $offerlist_info['direct_cost'] += $v['craft_show']*$v['num'];
                $offerlist_info['direct_cost'] += $v['quota']*$v['num'];
                $offerlist_info['artificial_cb'] += $v['labor_cost']*$values['num'];//人工成本
            }
        }
        //=========================计算毛利开始

        $tmp_cost = Db::name('tmp_cost')->where(['tmp_id'=>$offerlist_info['tmp_cost_id']])->field('tmp_name,name,sign,formula,rate,content')->select();
        $append_tmp_cost = json_decode($offerlist_info['tmp_append_cost'],true);//附加项
        $append_tmp_cost = is_array($append_tmp_cost)?$append_tmp_cost:[];
        $offerlist_info['default_cost'] = $tmp_cost;//默认模板明细
        $offerlist_info['append_cost'] = $append_tmp_cost;//附加模板明细
        $tmp_cost = array_merge($tmp_cost,$append_tmp_cost);//合并
        if(!$tmp_cost){
            $tmp_cost = [];
        }

        //初始化优惠 减空会报错
        $offerlist_info['discount'] = $offerlist_info['discount']?$offerlist_info['discount']:0;

        $cost_all = 0;//其他费用总计
        $cost_list = [];
        $sign['A1'] = $offerlist_info['direct_cost'];//直接费
        $sign['A2'] = $offerlist_info['discount'];//优惠
        $operation = [];
        foreach($tmp_cost as $k=>$v){
            $count_sign = count($sign);
            $num = 1;
            foreach($sign as $k2=>$v2){
                $v['formula'] = str_replace($k2,$v2,$v['formula']);
                if($count_sign == $num){
                    $str = $v['formula'];
                    $sign[$v['sign']] = round(eval("return $str ;")*$v['rate']/100,2);
                }else{
                    $num++;
                }
            }
            $tmp_cost[$k]['price'] = $sign[$v['sign']];
            $cost_all += $sign[$v['sign']];
        }
        $offerlist_info['order_cost'] = $tmp_cost; //全部模板明细
        $offerlist_info['order_cost_all_price'] = $cost_all; //其他费用总计

        // // $offerlist_info['sundry'] //运杂
        // // $offerlist_info['discount'] //优惠
            

        

        //计算总人工成本
        // $offerlist_info['artificial_cb'] = $offerlist_info['artificial_cb'];//上面获取了
        // $artificial = json_decode($offerlist_info['artificial'],true);
        // $offerlist_info['artificial_cb'] = 0;
        // foreach($artificial as $k=>$v){
        //     $offerlist_info['artificial_cb'] += ($v['num']*$v['cb']);//人工总成本
        // }

        //计算辅材成本
        $offerlist_info['material_cb'] = $this->get_material_list($offerlist_info['id'],$type)['total_money'];
        // $material = json_decode($offerlist_info['material'],true);
        // $offerlist_info['material_cb'] = 0;
        // foreach($material as $k=>$v){
        //     $offerlist_info['material_cb'] += ($v['omit_num']*$v['price']);//辅材总成本
        // }

        //工程报价 = 直接费+其他费用总计
        $offerlist_info['proquant'] = round($offerlist_info['direct_cost'] + $cost_all,2);
        //优惠后工程报价 工程报价-优惠
        $offerlist_info['discount_proquant'] = $offerlist_info['proquant'] - $offerlist_info['discount'];
        //计算杂项
        $offerlist_info['supervisor_commission'] = round($offerlist_info['supervisor_commission']/100*$offerlist_info['discount_proquant'],2);//监理提成
        $offerlist_info['design_commission'] = round($offerlist_info['design_commission']/100*$offerlist_info['discount_proquant'],2);;//设计提成
        $offerlist_info['repeat_commission'] = round($offerlist_info['repeat_commission']/100*$offerlist_info['discount_proquant'],2);;//回头客奖
        $offerlist_info['business_commission'] = round($offerlist_info['business_commission']/100*$offerlist_info['discount_proquant'],2);;//业务提成

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

    //获取增减项详情
    //oaids = 增减项id数组 
    public function get_append_order_info($oaids){ //offerquota表 的id type=2 直接费加上增减项的项目
        $order_project = Db::name('order_project')->where(['type'=>2,'oa_id'=>$oaids])->order('id','asc')->select();
        $oid = $order_project[0]['o_id'];//原单id
        $offerlist_info = Db::name('offerlist')->where(['id'=>$oid])->find();
        $offerlist_info['matquant'] = 0;
        $offerlist_info['manual_quota'] = 0;
        if(is_array($order_project)){
            foreach($order_project as $keys => $values){
                $offerlist_info['matquant'] += $values['quota']*$values['num'];//辅材报价
                $offerlist_info['manual_quota'] += $values['craft_show']*$values['num'];//人工报价
            }
        }
        $offerlist_info['direct_cost'] = $offerlist_info['matquant']+$offerlist_info['manual_quota'];//工程直接费= 辅材报价+人工报价

        
        //=========================计算毛利开始

        $tmp_cost = Db::name('tmp_cost')->where(['tmp_id'=>$offerlist_info['tmp_cost_id']])->field('tmp_name,name,sign,formula,rate,content')->select();
        $append_tmp_cost = json_decode($offerlist_info['tmp_append_cost'],true);//附加项
        $append_tmp_cost = is_array($append_tmp_cost)?$append_tmp_cost:[];
        $offerlist_info['default_cost'] = $tmp_cost;//默认模板明细
        $offerlist_info['append_cost'] = $append_tmp_cost;//附加模板明细
        $tmp_cost = array_merge($tmp_cost,$append_tmp_cost);//合并
        if(!$tmp_cost){
            $tmp_cost = [];
        }
        //初始化优惠 减空会报错
        $offerlist_info['discount'] = $offerlist_info['discount']?$offerlist_info['discount']:0;
        $cost_all = 0;//其他费用总计
        $cost_list = [];
        $sign['A1'] = $offerlist_info['direct_cost'];//直接费
        $sign['A2'] = 0;//优惠
        $operation = [];
        foreach($tmp_cost as $k=>$v){
            $count_sign = count($sign);
            $num = 1;
            foreach($sign as $k2=>$v2){
                $v['formula'] = str_replace($k2,$v2,$v['formula']);
                if($count_sign == $num){
                    $str = $v['formula'];
                    $sign[$v['sign']] = round(eval("return $str ;")*$v['rate']/100,2);
                }else{
                    $num++;
                }
            }
            $tmp_cost[$k]['price'] = $sign[$v['sign']];
            $cost_all += $sign[$v['sign']];
        }
        $offerlist_info['order_cost'] = $tmp_cost; //全部模板明细
        $offerlist_info['order_cost_all_price'] = $cost_all; //其他费用总计

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

        //工程报价 = 直接费+其他费用总计
        $offerlist_info['proquant'] = round($offerlist_info['direct_cost'] + $cost_all,2);
        //优惠后工程报价 工程报价-优惠
        $offerlist_info['discount_proquant'] = $offerlist_info['proquant'] - $offerlist_info['discount'];
        //计算杂项
        $offerlist_info['supervisor_commission'] = round($offerlist_info['supervisor_commission']/100*$offerlist_info['discount_proquant'],2);//监理提成
        $offerlist_info['design_commission'] = round($offerlist_info['design_commission']/100*$offerlist_info['discount_proquant'],2);;//设计提成
        $offerlist_info['repeat_commission'] = round($offerlist_info['repeat_commission']/100*$offerlist_info['discount_proquant'],2);;//回头客奖
        $offerlist_info['business_commission'] = round($offerlist_info['business_commission']/100*$offerlist_info['discount_proquant'],2);;//业务提成

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

    //统计订单 工程报价 直接费 辅材报价 人工报价  并修改到订单
    public function statistical_order($oid){
        $order_info = $this->get_order_info($oid);
        $info = [];
        $info['direct_cost'] = $order_info['direct_cost'];//直接费
        $info['matquant'] = $order_info['matquant'];//辅材报价
        $info['manual_quota'] = $order_info['manual_quota'];//人工报价
        $info['order_cost_all_price'] = $order_info['order_cost_all_price'];//其他费用
        $info['discount_proquant'] = $order_info['discount_proquant'];//优惠后报价
        return Db::name('userlist')->where(['id'=>$order_info['customerid']])->update($info);
        //加入订单表??  加入客户表???  看以后需求
        //增减项后的 要存吗??
    }

    //按收款期 获取订单增减项详情
    public function get_append_info($uid){
        $datas = [];//返回数据
        $userinfo = Db::name('userlist')->where(['id'=>$uid])->find();
        $o_id = Db::name('offerlist')->where(['customerid'=>$uid,'status'=>[3,4,5]])->value('id');//订单id

        $order_append = Db::name('order_append')->where(['o_id'=>$o_id])->select();
        if(!$order_append){
            return $datas;//没有增减项 返回空
        }
        //按收款期分的增减项id
        $oa_ids = [];
        foreach($order_append as $k=>$v){
            $oa_ids[$v['type']][] = $v['id'];
        }
        //获取每期的详情
        foreach($oa_ids as $k=>$v){
            $datas[$k] =  Model('admin/offerlist')->get_append_order_info($v);
        }
        return $datas;
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
    //type 1合同单 2整点
    public function get_material_list($id,$type=1){
        $total_money = 0;
        $where = [];
        $where['o_id'] = $id;
        if($type == 2){
            $where['status'] = [1,2];
        }else{
            $where['status'] = 1;
        }
        $order_material = Db::name('order_material')->where($where)->select();
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
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['remarks'] = $v['remarks']; //供应来源
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['place'] = $v['place']; //产地
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['phr'] = $v['phr']; //包装规格
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['cb'] = $v['cb']; //单价
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['price_total'] = 0; //总价
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['category'] = $v['category']; //工种
                $datas[$v['type_of_work']][$v['fine']][$v['amcode']]['price_total'] = $v['fine']; //辅材类别
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
                    $total_money += $datas[$k1][$k2][$k3]['coefficient'];
                }
            }
        }
        return ['datas'=>$datas,'total_money'=>$total_money];
    }

    

    //获取人工成本
}
