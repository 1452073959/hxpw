<?php
/**
 * Created by PhpStorm.
 * User: 覃宇彬
 * Date: 2019/6/17
 * Time: 10:40
 */

namespace app\admin\controller;


use app\common\controller\Adminbase;
use think\Db;
class Settlement extends Adminbase
{
    public function index()
    {
        $userinfo = $this->_userinfo;
        // $da['o.userid'] = $userinfo['userid'];
        if($userinfo['roleid'] != 1){
           $da['o.frameid'] = $userinfo['companyid'];
        }
        $da['o.status'] = 1;
        if($this->request->isPost()){
            $search = input('search');
            if($search){
                $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where($da)->select();
                $list = [];
                foreach ($res as $key => $value) {
                    if (strstr($value['customer_name'],$search)) {
                        $list[$key] = $value;
                    }
                }
                $this->assign('data',$list);
                return $this->fetch();
            }else{
                $this->error('请输入搜索内容', url("offerlist/index"));
            }

        }else{
            $res = Db::name('offerlist')->select();
            if ($res) {
                $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where($da)->select();
            }
            $this->assign('data',$res);
            return $this->fetch();
        }
    }
	//自选报表对比
	public function compare(){
		
		return $this->fetch();
	}
	//结算毛利对比
    public function contrast(){
        $userinfo = $this->_userinfo;
        $request = request();
        $id = $request->param('id');
        $da['o.id'] = $id;
        // echo $customerid;
        $res = Db::name('offerlist')->select();
        if ($res) {
            $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where($da)->select();
        }
        
        //统计报价开始 
        foreach ($res as $key => $value) {
            $content = json_decode($value['content'],true);
            foreach($content as $keys => $values){
                $res[$key]['matquant'] += $values['quotaall'];//辅材报价
                $res[$key]['manual_quota'] += $values['craft_showall'];//人工报价
            }
            $res[$key]['direct_cost'] = $res[$key]['matquant']+$res[$key]['manual_quota'];//工程直接费= 辅材报价+人工报价
            $res[$key]['proquant'] = $res[$key]['matquant']+$res[$key]['manual_quota']+$res[$key]['tubemoney']+$res[$key]['taxes']+$res[$key]['discount'];

            $tariff = array();$labor_cost = '';$fucai = '';
            foreach ($content as $keys => $values) {
                $dinge[$keys] =  Db::name('offerquota')->field('item_number,labor_cost,content')->where('item_number',$content[$keys]['item_number'])->find();
                $tariff[$keys]['item_number'] = $content[$keys]['item_number'];
                $tariff[$keys]['gcl'] = $content[$keys]['gcl'];
                $tariff[$keys]['labor_cost'] = $dinge[$keys]['labor_cost'] * $content[$keys]['gcl'];
                $tariff[$keys]['content'] = json_decode($dinge[$keys]['content'],true);
                //辅材成本
                foreach($tariff[$keys]['content'] as $k => $v){

                }
                $tariff[$keys]['fucai'] = 0;
                foreach ($tariff[$keys]['content'] as $e => $ll) {
                    if($ll[0] && is_numeric($ll[1])){
                        $price = $this->returnPrice($ll[0]);//辅材名称对应的价格；
                        $tariff[$keys]['fucai'] += $price*$ll[1]*$content[$keys]['gcl'];
                    }
                }
                $labor_cost += $tariff[$keys]['labor_cost'];
                $fucai += $tariff[$keys]['fucai']; 
            }
            $fc_cost = 0;//辅材成本
            $labor_cost = 0;//人工成本
            foreach($tariff as $k=>$v){
                foreach($v['content'] as $k1 => $v2){
                    $fc_cost += $v2[1] * $v['gcl'];
                }
                $labor_cost += $v['labor_cost'];
            }
            $res[$key]['fc_cost'] = $fc_cost;
            $res[$key]['labor_cost'] = $labor_cost;
            $res[$key]['gross_profit'] = $labor_cost+$fucai;
            $res[$key]['content'] = $content;
        }
        
        //结算数据
        $budget = Db::name('budget')->where('offerlist_id',$id)->find();
        $this->assign('budget',$budget);
        $this->assign('offerlist_id',$id);
        $this->assign('data',$res);
        $this->assign('tariff',$tariff);

        return $this->fetch();
    }

 //按辅材名称返回辅材单价
    public function returnPrice($val){
        if(is_null($val)){
            return null;
        }
        $re = Db::name('materials')->field('price')->where('name',$val)->find();
        return $re['price'];
    }

    //保存预算数据
    public function savebudget(){
        if($this->request->isPost()){
            $param = $this->request->param();
            $param['savetime'] = time();
            $has_save = Db::name('budget')->where('offerlist_id',$param['offerlist_id'])->find();
            if($has_save){
                $re = Db::name('budget')->where('id',$has_save['id'])->update($param);
            }else{
                $re = Db::name('budget')->insert($param);
            }
            if($re !== false){
                return json([ 'msg'=>'success' ]);
            }else{
                return json([ 'msg'=>'fail' ]);
            }
                    
        }
    }


}