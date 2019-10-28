<?php
// +----------------------------------------------------------------------
// | 领料
// +----------------------------------------------------------------------
namespace app\applet\controller;
use think\Db;
use think\Controller;
use think\facade\Cache;
use app\applet\controller\UserBase;
 
class Mail extends UserBase{
    //监理领料 客户列表
    public function userlist(){
        $where = [];
        if($this->admininfo['userid'] != 1){
            $where['jid'] = $this->admininfo['userid'];
        }
        $where['status'] = [3,4,5,6,7];
        $where['frameid'] = $this->admininfo['companyid'];
        $userlist = array_column(Db::name('userlist')->where($where)->order('sign_bill_time','asc')->select(),null, 'id') ;
        foreach($userlist as $k=>$v){
            $userlist[$k]['sign_bill_time'] = date('Y-m-d');
        }
        $this->json(0,'success',$userlist);
    }

    public function getStoreClassify(){
        $datas = [];
        $where = [];
        $where['frameid'] = $this->admininfo['companyid'];
        $materials = Db::name('materials')->where($where)->field('category,fine')->group('category')->group('fine')->select();
        foreach($materials as $k=>$v){
            if(empty(trim($v['fine']))){
                continue;
            }
            $v['category'] = mb_substr($v['category'] , 0 , 2);
            if(!isset($datas[$v['category']])){
                $datas[$v['category']] = [];
            }
            $datas[$v['category']][] = $v['fine'];
        }
        $i = 0;
        foreach($datas as $k=>$v){
            $datas[$i][0] = $k;
            $datas[$i][1] = $v;
            $datas[$i][2] = 0;
            unset($datas[$k]);
            $i++;
        }
        $this->json(0,'success',$datas);
    }

    //获取商品详情
    public function getGoodsInfo(){
        $amcode = input('amcode');
        if(empty($amcode)){
            $this->json(1,'none',[]);
        }
        $where = [];
        $where['frameid'] = $this->admininfo['companyid'];
        $where['amcode'] = $amcode;
        $goods_info = Db::name('materials')->where($where)->find();
        if($goods_info){
            $goods_info['img'] = $this->getImgSrc($goods_info['img']);
            $this->json(0,'success',$goods_info);
        }else{
            $this->json(1,'none',[]);
        }
    }

    //获取商品列表
    public function getGoods(){
        $category = input('category');
        $fine = input('fine');
        if(!$category || !$fine){
            $this->json(1,'none',[]);
        }
        $where = [];
        $cate = Db::name('materials')->where(['frameid'=>$this->admininfo['companyid'],'remarks'=>'公司仓库'])->field('category')->group('category')->select();
        $cate = array_column($cate,'category' ,'category');
        foreach($cate as $k=>$v){
            if($v){
                $cate[mb_substr($v , 0 , 2)] = $k;
                unset($cate[$k]);
            }else{
                unset($cate[$k]);
            }
        }
        $where['category'] = $cate[$category];
        $where['fine'] = $fine;
        $where['frameid'] = $this->admininfo['companyid'];
        $goods = Db::name('materials')->where($where)->field('amcode,fine,brand,category,name,img,units,phr,price')->paginate(10,false,['query'=>request()->param()])->each(function($item, $key){
            $item['img'] = $this->getImgSrc($item['img']);
            return $item;
        });
        $this->json(0,'success',$goods);
    }

    //下单
    public function placeTheOrder(){
        $cart = input('cart');
        if(empty($cart)){
            $this->json(1,'请选择辅材后再提交');
        }
        $uid = input('uid');
        $amcode = array_keys($cart);
        $materials = array_column(Db::name('materials')->where(['frameid'=>$this->admininfo['companyid'],'amcode'=>$amcode])->select(),null,'amcode');
        $userinfo = Db::name('userlist')->where(['id'=>$uid])->find(); //用户详情

        //领料单具体每个辅材明细
        $picking_material_info = [];
        $total_money = 0;
        foreach($cart as $k=>$v){
            $info = [];
            $info['num'] = $v['num'];
            $info['img'] = $v['img'];
            $info['pmid'] = 0;
            $info['oid'] = $userinfo['oid'];
            $info['userid'] = $uid;
            $info['f_id'] = $this->admininfo['companyid'];
            $info['type_of_work'] = $materials[$k]['category'];
            $info['m_name'] = $materials[$k]['name'];
            $info['price'] = $materials[$k]['price'];
            $info['amcode'] = $materials[$k]['amcode'];
            $info['fine'] = $materials[$k]['fine'];
            $info['brand'] = $materials[$k]['brand'];
            $info['place'] = $materials[$k]['place'];
            $info['img'] = $materials[$k]['img'];
            $info['phr'] = $materials[$k]['phr'];
            $info['remarks'] = $materials[$k]['remarks'];
            $info['category'] = $materials[$k]['category'];
            $info['units'] = $materials[$k]['units'];
            $picking_material_info[] = $info;
            $total_money += ($info['num']*$info['price']);
        }

        // 已领金额
        //********* 这里的status 未审核的要不要算已领金额??? *********
        $pink_total_money = Db::name('picking_material')->where(['userid'=>$uid,'status'=>[2,3]])->sum('total_money');

        //领料单数据
        $picking_material = [];
        $picking_material['oid'] = $userinfo['oid'];
        $picking_material['userid'] = $uid;
        $picking_material['adminid'] = $this->admininfo['userid'];
        $picking_material['f_id'] = $this->admininfo['companyid'];
        $picking_material['auditid'] = 0;
        $picking_material['total_money'] = $total_money;
        
        //********这里需要判断 已领金额 到达预算金额的多少后 需要审核
        //获取领料超过多少则需要审核
        $pick_rate = Db::name('cost_tmp')->where(['f_id'=>$this->admininfo['companyid']])->value('pick_rate');
        if(!$pick_rate){
            $pick_rate = 80;
        }
        //获取订单辅材成本总额
        $material_total_money = model('admin/offerlist')->get_material_list($userinfo['oid'],2)['total_money'];
        if($material_total_money * $pick_rate/100 >= ($pink_total_money+$total_money)){
            //未达到金额 不需要审核
            $picking_material['status'] = 2;
        }else{
            // 超过金额 需要审核
            $picking_material['status'] = 1;
        }
        try {
            $picking_material_id = Db::name('picking_material')->insertGetId($picking_material);
            if($picking_material_id){
                foreach($picking_material_info as $k=>$v){
                    $picking_material_info[$k]['pmid'] = $picking_material_id;
                }
            }
            $picking_material_info = Db::name('picking_material_info')->insertAll($picking_material_info);
            Cache::rm('cart_'.$uid.$this->admininfo['userid']);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->json(1,'下单失败');
        }
        $this->json(0,'下单成功');
        
    }

    //获取购物车
    public function getCart(){
        $cart = Cache::get('cart_'.input('uid').$this->admininfo['userid']);
        $cart = $cart?$cart:[];
        $this->json(0,'success',$cart);
    }

    //保存购物车
    public function saveCart(){
        $uid = input('uid');
        $cart = input('cart');
        // var_dump(input());die;
        Cache::set('cart_'.$uid.$this->admininfo['userid'],$cart,86400*7);
        $this->json(0,'success',$cart);
    }

    //缓存保存购物车 (详情页点击加入购物车)
    public function saveCartGoodsOne(){
        $amcode = input('amcode');
        $num = input('num');
        $name = input('name');
        $price = input('price');
        $uid = input('uid');
        $img = input('img');
        $cart = Cache::get('cart_'.$uid.$this->admininfo['userid']);
        if($cart){
            $cart[$amcode]['num'] = $num;
            $cart[$amcode]['name'] = $name;
            $cart[$amcode]['price'] = $price;
            $cart[$amcode]['img'] = $img;
        }else{
            $cart = [];
            $cart[$amcode]['num'] = $num;
            $cart[$amcode]['name'] = $name;
            $cart[$amcode]['price'] = $price;
            $cart[$amcode]['img'] = $img;
        }
        Cache::set('cart_'.$uid.$this->admininfo['userid'],$cart,86400*7);
        $this->json(0,'success',$cart);
    }

    //获取领料详情
    public function getHistoryPicking(){
        $where = [];
        $where['userid'] = input('uid');
        // $where['adminid'] = $this->admininfo['userid'];
        $picking_material = Db::name('picking_material')->where($where)->order('id','asc')->select();
        if(!$picking_material){
            //为空 未领料
            $this->json(2,'none',[]);
        }
        // $picking_material = array_column($picking_material,null ,'id');
        foreach($picking_material as $k=>$v){
            $picking_material[$k]['addtime'] = date('Y-m-d',strtotime($v['addtime']));
            $picking_material[$k]['info'] = Db::name('picking_material_info')->where(['pmid'=>$v['id']])->order('id','asc')->select();
            foreach($picking_material[$k]['info'] as $k1=>$v1){
                $picking_material[$k]['info'][$k1]['total'] = round($v1['price']*$v1['num'],2);
            }
        }
        $this->json(0,'success',$picking_material);
    }

}