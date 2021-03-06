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
    public function get_order_info($id,$type=1,$tmp_cost=[]){ //offerlist 的id , type ->1:合同单 2:整单
        $offerlist_info = Db::name('offerlist')->where(['id'=>$id])->find();
        $content = Db::name('order_project')->where(['type'=>1,'o_id'=>$id])->select();
        if($type == 2){
            $content = Db::name('order_project')->where(['o_id'=>$id])->select();
        }else{
            $content = Db::name('order_project')->where(['type'=>1,'o_id'=>$id])->select();
        }
        $offerlist_info['artificial_cb'] = 0;
        $offerlist_info['design'] = [];
        $no_discount = ['matquant'=>0,'manual_quota'=>0];//打拆工程
        if(is_array($content)){
            foreach($content as $keys => $values){
                if(strpos($values['project'],'设计') !== false){
                    $offerlist_info['design'][] = $values;
                    unset($content[$keys]);
                    continue;
                }
                $offerlist_info['matquant'] += $values['quota']*$values['num'];//辅材报价
                $offerlist_info['manual_quota'] += $values['craft_show']*$values['num'];//人工报价
                $offerlist_info['artificial_cb'] += $values['labor_cost']*$values['num'];//人工成本
                if($values['type_of_work'] == '打拆工程'){
                    // 打拆不打折
                    $no_discount['matquant'] += $values['quota']*$values['num'];//打拆辅材报价
                    $no_discount['manual_quota'] += $values['craft_show']*$values['num'];//人工报价
                }else if($values['oa_id'] != 0 && $offerlist_info['discount_append'] == 2){
                    //增加项不打折
                    $no_discount['matquant'] += $values['quota']*$values['num'];//打拆辅材报价
                    $no_discount['manual_quota'] += $values['craft_show']*$values['num'];//人工报价
                }
            }
        }
        $offerlist_info['direct_cost'] = $offerlist_info['matquant']+$offerlist_info['manual_quota'];//工程直接费= 辅材报价+人工报价

        //=========================计算毛利开始
        if(empty($tmp_cost)){
            $tmp_cost = Db::name('order_tmp_cost')->where(['oid'=>$offerlist_info['id']])->field('name,sign,formula,rate,content,sort')->order('sort','asc')->order('id','asc')->select();
        }
        // $tmp_cost = Db::name('order_tmp_cost')->where(['oid'=>$offerlist_info['id']])->field('name,sign,formula,rate,content,sort')->order('sort','asc')->order('id','asc')->select();
        
        
        
        $offerlist_info['default_cost'] = $tmp_cost;//默认模板明细
        $offerlist_info['append_cost'] = [];//附加模板明细

        //初始化优惠 减空会报错
        $offerlist_info['discount_zk'] = 0;//折扣优惠金额
        if($offerlist_info['discount_type'] == 2){
            //整单打折
            $offerlist_info['discount_zk'] = ($offerlist_info['direct_cost']-$no_discount['matquant']-$no_discount['manual_quota']) * (1-$offerlist_info['discount_num']/100);//折扣优惠金额

        }elseif($offerlist_info['discount_type'] == 3){
            // 人工打折
            $offerlist_info['discount_zk'] = ($offerlist_info['manual_quota']-$no_discount['manual_quota']) * (1-$offerlist_info['discount_num']/100);//折扣优惠金额
        }else{
            $offerlist_info['discount_zk'] = 0;
        }
        $offerlist_info['discount_zk'] = round($offerlist_info['discount_zk'],2);
        $offerlist_info['discount'] = $offerlist_info['discount']?$offerlist_info['discount']:0;
        // $offerlist_info['discount'] += $offerlist_info['discount_zk'];
        $cost_all = 0;//其他费用总计
        $cost_list = [];
        $sign['A1'] = $offerlist_info['direct_cost'];//直接费
        $sign['A2'] = $offerlist_info['discount'] + $offerlist_info['discount_zk'];//优惠
        $sign['A3'] = $offerlist_info['matquant'];//材料直接费
        // $sign['S'] = 0;//工程报价
        // $sign['T'] = 0;//总计
        $s_location = 0;
        foreach($tmp_cost as $k=>$v){
            if($v['sign'] == 'A1'){
                $tmp_cost[$k]['price'] = round($offerlist_info['direct_cost'],2);
            }else if($v['sign'] == 'S'){
                //工程报价
                if($sign['A2'] > 0){
                    $info = [];
                    $info['price'] = round($offerlist_info['direct_cost'] + $cost_all,2);
                    $info['name'] = '优惠前报价';
                    $info['sign'] = '';
                    $info['rate'] = '';
                    $info['formula'] = '';
                    $s_location = $k;
                }
                $tmp_cost[$k]['price'] = round($offerlist_info['direct_cost'] + $cost_all - $sign['A2'],2);

                $sign['S'] = $tmp_cost[$k]['price'];
                //工程报价 = 直接费+其他费用总计
                $offerlist_info['proquant'] = round($offerlist_info['direct_cost'] + $cost_all,2);
                //优惠后工程报价 工程报价-优惠
                $offerlist_info['discount_proquant'] = $tmp_cost[$k]['price'];
            }else if($v['sign'] == 'T'){
                //合计
                $tmp_cost[$k]['price'] = round($offerlist_info['direct_cost'] + $cost_all - $sign['A2'],2);
                $sign['T'] = $tmp_cost[$k]['price'];
                if($tmp_cost[$k]['price'] == $sign['S']){
                    //总计等于工程报价
                    // unset($tmp_cost[$k]);
                }
            }else if($v['sign'] == 'A2'){
                //优惠
                if($sign['A2'] == 0){
                    // unset($tmp_cost[$k]);
                    $tmp_cost[$k]['price'] = 0;
                }else{
                    $tmp_cost[$k]['price'] = $sign['A2'];
                }
                
            }else{
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
        }
        if($s_location){
            array_splice($tmp_cost,$s_location,0,array($info));
        }
        // var_dump($tmp_cost);die;
        $offerlist_info['order_cost'] = $tmp_cost; //全部模板明细
        $offerlist_info['order_cost_all_price'] = $cost_all; //其他费用总计

        //计算辅材成本
        $offerlist_info['material_cb'] = $this->get_material_list($offerlist_info['id'],$type)['total_money'];

        //工程报价 = 直接费+其他费用总计
        $offerlist_info['proquant'] = isset($offerlist_info['proquant'])?$offerlist_info['proquant']:round($offerlist_info['direct_cost'] + $cost_all,2);
        //优惠后工程报价 工程报价-优惠
        $offerlist_info['discount_proquant'] = isset($offerlist_info['discount_proquant'])?$offerlist_info['discount_proquant']:$offerlist_info['proquant'] - $offerlist_info['discount'] - $offerlist_info['discount_zk'];
        //计算杂项
        $offerlist_info['supervisor_commission'] = round($offerlist_info['supervisor_commission']/100*$offerlist_info['discount_proquant'],2);//监理提成
        $offerlist_info['design_commission'] = round($offerlist_info['design_commission']/100*$offerlist_info['discount_proquant'],2);;//设计提成
        $offerlist_info['repeat_commission'] = round($offerlist_info['repeat_commission']/100*$offerlist_info['discount_proquant'],2);;//回头客奖
        $offerlist_info['business_commission'] = round($offerlist_info['business_commission']/100*$offerlist_info['discount_proquant'],2);;//业务提成
        //计算礼品成本
        if(!empty($offerlist_info['gift'])){
            $offerlist_info['gift'] = Model('gift')->getGiftTotal($offerlist_info['id']);
        }else{
            $offerlist_info['gift'] = 0;
        }

        //计算毛利 利润/报价
        if($offerlist_info['direct_cost']){
            //工程毛利 优惠后工程报价 - 辅材成本-人工成本
            $offerlist_info['gross_profit'] = round(($offerlist_info['discount_proquant'] - $offerlist_info['artificial_cb'] - $offerlist_info['material_cb'] - $offerlist_info['gift'] ),2);
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
        return $this->get_order_info_by_project($order_project,1);
    }

    //获取订单详情
    public function get_order_info_by_project($order_project,$is_append=0){
        $oid = $order_project[0]['o_id'];
        $offerlist_info = Db::name('offerlist')->where(['id'=>$oid])->find();
        $offerlist_info['artificial_cb'] = 0;
        $offerlist_info['design'] = [];
        $no_discount = ['matquant'=>0,'manual_quota'=>0];//打拆工程
        if(is_array($order_project)){
            foreach($order_project as $keys => $values){
                if(strpos($values['project'],'设计') !== false){
                    $offerlist_info['design'][] = $values;
                    unset($order_project[$keys]);
                    continue;
                }
                $offerlist_info['matquant'] += $values['quota']*$values['num'];//辅材报价
                $offerlist_info['manual_quota'] += $values['craft_show']*$values['num'];//人工报价
                $offerlist_info['artificial_cb'] += $values['labor_cost']*$values['num'];//人工成本
                if($values['type_of_work'] == '打拆工程'){
                    // 打拆不打折
                    $no_discount['matquant'] += $values['quota']*$values['num'];//打拆辅材报价
                    $no_discount['manual_quota'] += $values['craft_show']*$values['num'];//人工报价
                }else if($values['oa_id'] != 0 && $offerlist_info['discount_append'] == 2){
                    //增加项不打折
                    $no_discount['matquant'] += $values['quota']*$values['num'];//打拆辅材报价
                    $no_discount['manual_quota'] += $values['craft_show']*$values['num'];//人工报价
                }
            }
        }
        $offerlist_info['direct_cost'] = $offerlist_info['matquant']+$offerlist_info['manual_quota'];//工程直接费= 辅材报价+人工报价

        //=========================计算毛利开始

        $tmp_cost = Db::name('order_tmp_cost')->where(['oid'=>$offerlist_info['id']])->field('name,sign,formula,rate,content,sort')->order('sort','asc')->order('id','asc')->select();

        $offerlist_info['default_cost'] = $tmp_cost;//默认模板明细
        $offerlist_info['append_cost'] = [];//附加模板明细
        

        //初始化优惠 减空会报错
        $offerlist_info['discount_zk'] = 0;//折扣优惠金额
        if($offerlist_info['discount_type'] == 2){
            //整单打折
            $offerlist_info['discount_zk'] = ($offerlist_info['direct_cost']-$no_discount['matquant']-$no_discount['manual_quota']) * (1-$offerlist_info['discount_num']/100);//折扣优惠金额

        }elseif($offerlist_info['discount_type'] == 3){
            // 人工打折
            $offerlist_info['discount_zk'] = ($offerlist_info['manual_quota']-$no_discount['manual_quota']) * (1-$offerlist_info['discount_num']/100);//折扣优惠金额
        }else{
            $offerlist_info['discount_zk'] = 0;
        }
        $offerlist_info['discount_zk'] = round($offerlist_info['discount_zk'],2);
        if($is_append){
            $offerlist_info['discount'] = 0;
        }else{
            $offerlist_info['discount'] = $offerlist_info['discount']?$offerlist_info['discount']:0;
        }
        
        // $offerlist_info['discount'] += $offerlist_info['discount_zk'];
        $cost_all = 0;//其他费用总计
        $cost_list = [];
        $sign['A1'] = $offerlist_info['direct_cost'];//直接费

        $sign['A2'] = $offerlist_info['discount'] + $offerlist_info['discount_zk'];//优惠
        $s_location = 0;
        foreach($tmp_cost as $k=>$v){
            if($v['sign'] == 'A1'){
                $tmp_cost[$k]['price'] = round($offerlist_info['direct_cost'],2);
            }else if($v['sign'] == 'S'){
                //工程报价
                if($sign['A2'] > 0){
                    $info = [];
                    $info['price'] = round($offerlist_info['direct_cost'] + $cost_all,2);
                    $info['name'] = '优惠前报价';
                    $s_location = $k;
                }
                $tmp_cost[$k]['price'] = round($offerlist_info['direct_cost'] + $cost_all - $sign['A2'],2);
                $sign['S'] = $tmp_cost[$k]['price'];
                //工程报价 = 直接费+其他费用总计
                $offerlist_info['proquant'] = round($offerlist_info['direct_cost'] + $cost_all,2);
                //优惠后工程报价 工程报价-优惠
                $offerlist_info['discount_proquant'] = $tmp_cost[$k]['price'];
                // echo $offerlist_info['discount_proquant'];die;
            }else if($v['sign'] == 'T'){
                //合计
                $tmp_cost[$k]['price'] = round($offerlist_info['direct_cost'] + $cost_all - $sign['A2'],2);
                $sign['T'] = $tmp_cost[$k]['price'];
                if($tmp_cost[$k]['price'] == $sign['S']){
                    //总计等于工程报价
                    unset($tmp_cost[$k]);
                }
            }else if($v['sign'] == 'A2'){
                //合计
                if($sign['A2'] == 0){
                    unset($tmp_cost[$k]);
                }else{
                    $tmp_cost[$k]['price'] = $sign['A2'];
                }
            }else{
                if(is_numeric($v['formula']) && $is_append){
                    //是个数字 直接 增加项直接忽略
                    // $tmp_cost[$k]['price'] = 0;
                    unset($tmp_cost[$k]);
                }else{
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
            }
        }
        if($s_location){
            array_splice($tmp_cost,$s_location,0,array($info));
        }
        $offerlist_info['order_cost'] = $tmp_cost; //全部模板明细
        $offerlist_info['order_cost_all_price'] = $cost_all; //其他费用总计


        //计算辅材成本
        // $offerlist_info['material_cb'] = $this->get_material_list($offerlist_info['id'],$type)['total_money'];
        $offerlist_info['material_cb'] = 0; // 不计算辅材成本 暂时不需要

        //工程报价 = 直接费+其他费用总计
        $offerlist_info['proquant'] = isset($offerlist_info['proquant'])?$offerlist_info['proquant']:round($offerlist_info['direct_cost'] + $cost_all,2);
        //优惠后工程报价 工程报价-优惠
        $offerlist_info['discount_proquant'] = isset($offerlist_info['discount_proquant'])?$offerlist_info['discount_proquant']:$offerlist_info['proquant'] - $offerlist_info['discount'] - $offerlist_info['discount_zk'];
        //计算杂项
        $offerlist_info['supervisor_commission'] = round($offerlist_info['supervisor_commission']/100*$offerlist_info['discount_proquant'],2);//监理提成
        $offerlist_info['design_commission'] = round($offerlist_info['design_commission']/100*$offerlist_info['discount_proquant'],2);;//设计提成
        $offerlist_info['repeat_commission'] = round($offerlist_info['repeat_commission']/100*$offerlist_info['discount_proquant'],2);;//回头客奖
        $offerlist_info['business_commission'] = round($offerlist_info['business_commission']/100*$offerlist_info['discount_proquant'],2);;//业务提成
        //计算礼品成本
        if(!empty($offerlist_info['gift'])){
            $offerlist_info['gift'] = Model('gift')->getGiftTotal($offerlist_info['id']);
        }else{
            $offerlist_info['gift'] = 0;
        }
        //计算毛利 利润/报价
        if($offerlist_info['direct_cost']){
            //工程毛利 优惠后工程报价 - 辅材成本-人工成本
            $offerlist_info['gross_profit'] = round(($offerlist_info['discount_proquant'] - $offerlist_info['artificial_cb'] - $offerlist_info['material_cb'] - $offerlist_info['gift'] ),2);
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

    //获取监理申请结算的其他费用总额
    public function getSettlementTotal($sid){
        $total_money = 0;
        $settlement = Db::name('settlement')->where(['id'=>$sid])->find();
        $total_money = $settlement['material_append'] + $settlement['carry'] + $settlement['other_fee'];
        $settlement_worker = Db::name('settlement_worker')->where(['sid'=>$sid])->select();
        foreach($settlement_worker as $k=>$v){
            $total_money += $v['wage'];
            $total_money += $v['rent'];
            $total_money += $v['remote'];
        }
        return $total_money;
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
    //type 1合同单 2整单
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
                        if($v3['num'] < 1 && $v['important']){
                            $datas[$k1][$k2][$k3]['omit_num'] = ceil($v3['num']);
                        }else{
                            $datas[$k1][$k2][$k3]['omit_num'] = floor($v3['num']);
                        }
                    }
                    $datas[$k1][$k2][$k3]['coefficient'] = ($datas[$k1][$k2][$k3]['omit_num'] * $v3['cb']);
                    $total_money += $datas[$k1][$k2][$k3]['coefficient'];
                }
            }
        }
        return ['datas'=>$datas,'total_money'=>$total_money];
    }
    //获取人工成本


    public function user()
    {
        return $this->belongsTo(AdminUser::class,'userid','userid');
    }
    public function ddyl()
    {
        return $this->hasMany(OrderProject::class,'o_id','id');
    }
}
