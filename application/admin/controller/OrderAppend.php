<?php

// +----------------------------------------------------------------------
// | 报表管理
// +----------------------------------------------------------------------
namespace app\admin\controller; 

use app\common\controller\Adminbase;
use think\Db;
use think\Request;

class OrderAppend extends Adminbase
{

    //获取监理申请的增加项
    public function get_append_img(){
        $oid = input('oid');
        $append_img = Db::name('append_img')->where('oid',$oid)->order('id','asc')->select();
        foreach($append_img as $k=>$v){
            $append_img[$k]['img'] = 'http://'.$_SERVER['HTTP_HOST'].'/uploads/images/'.$v['img'];
            $append_img[$k]['time'] = date('Y-m-d',strtotime($v['time']));
            if($v['deal_id'] != 0){
                $append_img[$k]['status'] = '已处理';//已处理
            }else{
                $append_img[$k]['status'] = '未处理';
            }
            $append_img[$k]['img'] = 'http://'.$_SERVER['HTTP_HOST'].'/uploads/images/'.$v['img'];
        }
        if($append_img){
            $this->success('success','',$append_img);
        }else{
            $this->success('none');
        }
    }
    //确认处理增加项
    public function deal_append_img(){
        $id = input('id');
        $append_img = Db::name('append_img')->where('id',$id)->find();
        if($append_img['deal_id'] != 0){
            $this->error('该订单已处理');
        }
        if($append_img['fid'] != $this->_userinfo['companyid']){
            // $this->error('禁止处理非本公司的订单');
        }
        $update = Db::name('append_img')->where('id',$id)->update(['deal_id'=>$this->_userinfo['userid'],'deal_time'=>time()]);
        if($update){
            $this->success('处理成功');
        }else{
            $this->error('处理失败');
        }
    }

    //新增时页面
    public function add_index(){
        if(!input('customer_id') || !input('order_id')){
            $this->error('非法操作');
        }
        $userinfo = $this->_userinfo;
        $offer_type_list = Db::name('offer_type')->where(['companyid'=>$userinfo['companyid'],'status'=>1])->select();
        $offer_type = [1=>[],2=>[]];
        foreach($offer_type_list as $k=>$v){
            $offer_type[$v['type']][] = $v;
        }
        $customer_info = Db::name('userlist')->where(['id'=>input('customer_id')])->find();
        $offerlist = Db::name('offerlist')->where(['id'=>input('order_id')])->find();
        if($offerlist['status'] < 3){
            $this->error('只有合同价才能增减项');
        }
        if($offerlist['status'] >= 5){
            $this->error('结算状态禁止增加项');
        }
        $this->assign([
            'offer_type'=>$offer_type,
            'customer_info'=>$customer_info,
            'order_id'=>input('order_id'),
        ]);
        if($customer_info['is_new'] == 9){
            return $this->fetch('add_order_olduser');
        }
        return $this->fetch();
    }

    //新增操作
    public function add(){
        $userinfo = $this->_userinfo; 
        if (input('data') && input('order_id')) {

            $price = [];
            if(input('price')){
                $price = input('price');
            }
            $time = time();
            $order_info = Db::name('offerlist')->where('id',input('order_id'))->find();
            if($order_info['status'] >= 5){
                $this->error('结算状态禁止增加项');
            }
            if(!$order_info){
                $this->error('订单信息有误');
            }
            $order_project = [];

            foreach (input('data') as $k1 => $v1) {
                foreach($v1 as $k2=>$v2){
                    $item = Db::name('offerquota')->where('item_number',$k2)->where('frameid',$userinfo['companyid'])->find();//获取定额数据
                    if(!$item){
                        $this->error('项目有误');
                    }
                    //=========================项目 另存新数据库 后面慢慢完善
                    $project = [];
                    $project['o_id'] = input('order_id');//订单id
                    $project['oa_id'] = 0;
                    $project['item_number'] = $k2;
                    $project['num'] = $v2;
                    $project['type_of_work'] = $item['type_of_work'];
                    $project['space'] = $k1;
                    $project['project'] = $item['project'];
                    $project['company'] = $item['company'];
                    $project['cost_value'] = $item['cost_value'];
                    //旧客户手动输入价格 start
                    if(!isset($price[$k2]['quota'])  || empty($price[$k2]['quota'])){
                        $project['quota'] = $item['quota'];
                        $project['quota_now'] = '';
                    }else{
                        if(is_numeric($price[$k2]['quota'])){
                            $project['quota'] = $price[$k2]['quota'];
                            $project['quota_now'] = $item['quota'];
                        }else{
                            $this->error($k2.'手动输入的价格有误');
                        }
                    }

                    if(!isset($price[$k2]['craft_show'])  || empty($price[$k2]['craft_show'])){
                        $project['craft_show'] = $item['craft_show'];
                        $project['craft_show_now'] = '';
                    }else{
                        if(is_numeric($price[$k2]['craft_show'])){
                            $project['craft_show'] = $price[$k2]['craft_show'];
                            $project['craft_show_now'] = $item['craft_show'];
                        }else{
                            $this->error($k2.'手动输入的价格有误');
                        }
                    }
                    //判断手动输入的价格与原单是否冲突
                    if(!empty($project['quota_now']) || empty($project['craft_show_now'])){
                        $history_project = Db::name('order_project')->where(['o_id'=>input('order_id'),'item_number'=>$k2])->find();
                        if($history_project){
                            if($history_project['quota'] != $project['quota'] || $history_project['craft_show'] != $project['craft_show']){
                                // $this->error($history_project['craft_show'].'-'.$project['craft_show'].'-'.$history_project['craft_show'].'-'.$project['craft_show']);
                                $this->error($k2.'手动输入的价格与原单不匹配'.$history_project['quota'].'-'.$history_project['craft_show']);
                            }
                        }
                    }
                    //旧客户手动输入价格 end
                    $project['labor_cost'] = $item['labor_cost'];
                    $project['material'] = $item['material'];
                    $project['content'] = $item['content'];
                    $project['type'] = 2;
                    $project['add_time'] = $time;
                    $order_project[] = $project; 
                }
            }

            //===============================计算物料总合计 成本
            // $arr['工种类']['项目编号']['辅材名称'] = ['数量','成本','单价','利润']
            $material_all = [];
            $order_material = [];//订单辅料详情 - 以后顶替material_all这种json储存方法

            foreach($order_project as $k=>$v) {
                if(!empty($v['content'])){
                $need_material = json_decode($v['content'], true);//需要的物料
                foreach ($need_material as $one_material) {
                    if ($one_material[0] && $one_material[1]) {
                        if (!isset($material_all[$one_material[0]])) {
                            //上面2个foreach筛选offerquota表里面的content的有用数据 ( 里面有20个所需辅材 没有的用空数组代替 上面是提出空数组 )
                            $material_all[$one_material[0]]['num'] = 0;
                            $material_all[$one_material[0]]['price'] = 0;//成本单价
                        }
                        // $materials_info = Db::name('materials')->where(array('frameid'=>$userinfo['companyid'],'name'=>$one_material[0]))->find();
                        $materials_info = Db::name('materials')->where(array('frameid' => $userinfo['companyid']))->where('name|amcode', '=', $one_material[0])->find();
                        $price = $materials_info['price'];
                        $coefficient = $materials_info['coefficient'];
                        if (!$price) {
                            $this->error($one_material[0] . '成本有误，请及时补充辅材仓库');
                        }

                        $material_all[$one_material[0]]['price'] = $price;//成本单价
                        $material_all[$one_material[0]]['coefficient'] = $coefficient;//系数
                        $material_all[$one_material[0]]['important'] = $materials_info['important'];
                        $material_all[$one_material[0]]['num'] += $one_material[1] * $v['num']; //需要的用料单数 * 工程单位
                        //===============订单辅料详情  $arr['工种类']['项目编号']['辅材名称'] = ['数量','成本','单价','利润']
                        if (!isset($order_material[$v['type_of_work']][$v['item_number']][$one_material[0]])) {
                            //初始化数据 这个框架会神奇的报错 = =
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['cb'] = $materials_info['price'];//成本
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['price'] = $v['quota'];//辅材单价
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['name'] = $materials_info['name'];//辅材名称
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['profit'] = $v['quota'] - $materials_info['price'];//利润
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['coefficient'] = $coefficient;//系数
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['important'] = $materials_info['important'];//是否重要
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['num'] = 0;//初始化数据
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['amcode'] = $materials_info['amcode'];//
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['fine'] = $materials_info['fine'];//
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['brand'] = $materials_info['brand'];//
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['place'] = $materials_info['place'];//
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['img'] = $materials_info['img'];//
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['phr'] = $materials_info['phr'];//
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['remarks'] = $materials_info['remarks'];//
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['category'] = $materials_info['category'];//
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['units'] = $materials_info['units'];//
                        }
                        $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['num'] += $one_material[1] * $v['num'];
                    }
                }
                }
            }

            //=========订单辅料详情 组装数据存进数据库
            //$order_material['工种类']['项目编号']['辅材名称'] = ['数量','成本','单价','利润']
            $order_material_datas = [];
            foreach($order_material as $k1=>$v1){
                foreach($v1 as $k2=>$v2){
                    foreach($v2 as $k3=>$v3){
                        // $order_material_datas['o_id'] = '';//还没有
                        $data_info['o_id'] = input('order_id');
                        $data_info['f_id'] = $order_info['frameid'];
                        $data_info['type_of_work'] = $k1;;
                        $data_info['item_number'] = $k2;;
                        $data_info['m_name'] = $v3['name'];
                        $data_info['num'] = $v3['num'];
                        $data_info['cb'] = $v3['cb'];
                        $data_info['price'] = $v3['price'];
                        $data_info['profit'] = $v3['profit'];
                        $data_info['coefficient'] = $v3['coefficient'];
                        $data_info['important'] = $v3['important'];
                        $data_info['amcode'] = $v3['amcode'];
                        $data_info['fine'] = $v3['fine'];
                        $data_info['brand'] = $v3['brand'];
                        $data_info['place'] = $v3['place'];
                        $data_info['img'] = $v3['img'];
                        $data_info['phr'] = $v3['phr'];
                        $data_info['remarks'] = $v3['remarks'];
                        $data_info['category'] = $v3['category'];
                        $data_info['units'] = $v3['units'];
                        $data_info['status'] = 2;


                        $order_material_datas[] = $data_info;
                    }
                }
            }
            Db::startTrans();
            try{
                $userlist = Db::name('userlist')->where(['id'=>input('customerid')])->find();
                $re = Db::name('order_append')->insertGetId(['o_id'=>input('order_id'),'adminid'=>$userinfo['userid'],'add_time'=>$time,'remark'=>input('remark')?input('remark'):'','type'=>($userlist['status']-1) ]);
                if($order_material_datas){
                    foreach($order_material_datas as $k=>$v){
                        $order_material_datas[$k]['oa_id'] = $re;
                    }
                    $order_material_res = Db::name('order_material')->insertAll($order_material_datas);
                }else{
                    $order_material_res = 1;
                }
                foreach($order_project as $k=>$v){
                    $order_project[$k]['oa_id'] = $re;
                }
                $order_project_res = Db::name('order_project')->insertAll($order_project);
                // 提交事务
                Db::commit();    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error('失败');
            }
            if($re!==false && $order_material_res && $order_project_res){
                $this->success('成功',url('admin/offerlist/index',array('customer_id'=>input('customerid'))));
            }else{
                $this->error('失败');
            }
        }else{
            $this->error('参数错误');
        }
    }

    public function show_append(){
        $o_id = input('order_id');
        //订单数据
        $order_info = Db::name('offerlist')->where('id',$o_id)->find();
        $userinfo = Db::name('userlist')->where('id',$order_info['customerid'])->find();
        $order_project = Db::name('order_project')->where('o_id',$o_id)->where('type',2)->select();

        //==========获取工种 空间类型
        $offer_type_list = Db::name('offer_type')->where(['companyid'=>$userinfo['frameid'],'status'=>1])->select();
        $offer_type = [1=>[],2=>[]];
        foreach($offer_type_list as $k=>$v){
            $offer_type[$v['type']][] = $v;
        }
        //===========获取工种结束
        $datas = [];
        $item_number = [];
        foreach($order_project as $k=>$v){
            if(!isset($datas[$v['type_of_work']][$v['space']][$v['item_number']])){
                $datas[$v['type_of_work']][$v['space']][$v['item_number']]['info'] = $v;
                $datas[$v['type_of_work']][$v['space']][$v['item_number']]['num'] = 0;
                $datas[$v['type_of_work']][$v['space']][$v['item_number']]['project'] = $v['project'];
                // $item_number[] = $v['item_number'];

            }
            $datas[$v['type_of_work']][$v['space']][$v['item_number']]['num'] += $v['num'];
        }
        // $item_number = array_unique($item_number);
        // $offerquota = array_column(Db::name('offerquota')->where('item_number','in',$item_number)->where('frameid',$order_info['frameid'])->select(), null,'item_number');
        // var_dump($datas);die;
        $this->assign([
            'datas'=>$datas,
            'order_info'=>$order_info,
            'userinfo'=>$userinfo,
            // 'offerquota'=>$offerquota,
            'offer_type'=>$offer_type,
        ]); 
        return $this->fetch();
    }

    //获取订单全部增减项
    public function ajax_get_append_all(){
        $o_id = input('o_id');
        $order_project = Db::name('order_project')->where('o_id',$o_id)->select();
        $datas = [];
        foreach($order_project as $k=>$v){
            if(!isset($datas[$v['type_of_work']][$v['space']][$v['item_number']])){
                $datas[$v['type_of_work']][$v['space']][$v['item_number']]['num'] = 0;
                $datas[$v['type_of_work']][$v['space']][$v['item_number']]['project'] = $v['project'];
            }
            $datas[$v['type_of_work']][$v['space']][$v['item_number']]['num'] += $v['num'];
        }
        if($datas){
            echo json_encode(array('code'=>0,'msg'=>'success','datas'=>$datas));
        }else{
            echo json_encode(['code'=>1,'msg'=>'暂无数据']);
        }
    }
	
}