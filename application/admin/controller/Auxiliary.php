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



class Auxiliary extends Adminbase
{
    // protected function initialize()
    // {
    //     parent::initialize();
    //     $this->Menu = new Menu_Model;
    // }
    public $show_page = 15;

    //选择客户
    public function userlist(){
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

    public function index()
    { 
       $userinfo = $this->_userinfo; 
        $da = [];
        if($userinfo['roleid'] != 1){
           $da['o.frameid'] = $userinfo['companyid'];
        }
       $da['o.customerid'] = input('c_id');
       if($this->request->isPost()){
            $search = input('search'); 
            if($search){
              $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')  -> where($da)->select();
              $list = [];
               foreach ($res as $key => $value) {     
                  if (strstr($value['customer_name'],$search)) {
                     $list[$key] = $value;
                  }
               }
               $this->assign('data',$list);    
               return $this->fetch();       
            }else{
              $this->error('请输入搜索内容', url("Auxiliary/index"));
            }
       }else{
           $res = Db::name('offerlist')->select();
           if ($res) {
             $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')-> where($da)->select();
           }
           $this->assign('data',$res);
           return $this->fetch();
        } 
    }


    //按辅材名称返回辅材单价
    public function returnPrice($val){
       if ($val != null) {
         $re = Db::name('materials')->where('name',$val)->find();
         return $re['price'];
       }else{
         return '';
       }
    }

    public function history(){
        $userinfo = $this->_userinfo;
        $o_id = input('id');//订单id
        

        $material_list = model('offerlist')->get_material_list($o_id);
        $c_id = Db::name('offerlist')->where(['id'=>$o_id])->value('customerid');//客户id
        $userinfo = Db::name('userlist')->where(['id'=>$c_id])->find();//客户信息
        // echo $id;
        
        $this->assign('material',$material_list);
        $this->assign('userinfo',$userinfo);
        // var_dump($material_list);die;
        // $this->assign('total',$total);
       
        // $this->assign('data',$res);
       //  $this->assign('arrs',$allarrs);
        return $this->fetch();

    }

     public function getcang($frameid,$name){
         $getobj = Db::name('materials')->where(array('frameid'=>$frameid,'name'=>$name))->find();
         if($getobj) {
           $newzu = array('amcode'=>$getobj['amcode'],'units'=>$getobj['units'],'price'=>$getobj['price']);
           return $newzu;
         }else{
           return array('amcode'=>'未找到','units'=>'未找到','price'=>'未找到');
         }
         


     }

     public function qukong($val){
          $kongzhi = json_decode($val,true);
          foreach ($kongzhi as $key => $value) {
               if($value[0]==null && $value[1]==null){
                 unset($kongzhi[$key]);
               }else{
                   $kongzhi[$key]['info'] =  Db::name('materials')->where(['name'=>$value[0],'frameid'=>$this->_userinfo->companyid])->find();
                   if(empty($kongzhi[$key]['info'])) unset($kongzhi[$key]);
               }


           } 
           return  $kongzhi;
     }




}
