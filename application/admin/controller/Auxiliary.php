<?php

// +----------------------------------------------------------------------
// | 报价管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;
use think\Request;

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

    //
    public function index()
    {
       $userinfo = $this->_userinfo; 
       // dump($userinfo);
       // $da['o.userid'] = $userinfo['userid'];
        if($userinfo['roleid'] != 1){
           $da['o.frameid'] = $userinfo['companyid'];
        }
       
       $da['o.status'] = 1;
      if($this->request->isPost()){
         $search = input('search'); 
         if($search){
            // echo $search;
            // $map['item_number'] = array('like',$search);
           $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where($da)->select();
           // dump($res);
           $list = [];
            foreach ($res as $key => $value) {     
               if (strstr($value['customer_name'],$search)) {
                  $list[$key] = $value;
               }
            }
            // $sres = Db::name('userlist')->where('customer_name','like',"%".$search."%")->select();//查客户

            // dump($list);
            $this->assign('data',$list);    
            return $this->fetch();       
         }else{
           $this->error('请输入搜索内容', url("Auxiliary/index"));
         }

      }else{
        $res = Db::name('offerlist')->select();
        if ($res) {
          $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where($da)->select();
        }
        //统计报价开始 
         // foreach ($res as $key => $value) {
         //  $sum = '';
         //  $sum1 = '';
         //      $newzh = json_decode($value['content'],true);
         //     foreach ($newzh as $kk => $vv){
         //       $sum += $vv['quotaall'];
         //       $sum1 += $vv['craft_showall'];
         //     }
         //     $res[$key]['matquant'] = $sum;//辅材报价
         //     $res[$key]['manual_quota'] = $sum1;//人工报价
         //     $res[$key]['direct_cost'] = $sum+$sum1;//工程直接费= 辅材报价+人工报价
         //     $res[$key]['proquant'] = $sum+$sum1+$value['tubemoney']+$value['taxes']+$value['discount'];//工程报价 =工程直接费+管理费+税金+优惠
         //     //超复杂工程毛利计算开始
         //     $dinge =  Db::name('offerquota')->field('item_number,labor_cost,content')->where('frameid',6)->select();
         //     $nuew = [];
         //     $cgsum = '';
         //     $fcsum = '';
         //     foreach ($newzh as $kk => $vv){
         //        $nuew[$kk]['item_number'] = $vv['item_number'];
         //        $nuew[$kk]['gcl'] = $vv['gcl'];
         //        foreach ($dinge as $k => $v) {
         //         if ($vv['item_number'] == $v['item_number']) {
         //          $nuew[$kk]['labor_cost'] = $v['labor_cost']*$vv['gcl'];

         //          $nuew[$kk]['content'] = json_decode($v['content'],true);
         //          $csum = '';
         //           foreach ($nuew[$kk]['content'] as $ee => $ll) {
         //                   if($ll[0] && is_numeric($ll[1])) {
         //                     $price = $this->returnPrice($ll[0]);//辅材名称对应的价格；
         //                     $csum += $price*$ll[1]*$vv['gcl'];
         //                   }
         //           }
         //           $nuew[$kk]['fucai'] = $csum;
         //         }
         //        }
         //        $cgsum += $nuew[$kk]['labor_cost'];//人工成本
         //        $fcsum += $nuew[$kk]['fucai'];//人工成本
         //     }
         //     $res[$key]['gross_profit'] = $cgsum+$fcsum;//工程毛利
         //    // end
         //     $res[$key]['content'] = $newzh;
         // }

        // dump($res);
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
        $request = request();
        $id = $request->param('id');
        $da['o.id'] = $id;
        // echo $id;
        $res = Db::name('offerlist')->select();
        if ($res) {
          $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address')->join('userlist u','o.customerid = u.id')->where($da)->find();
        }
        $material = $res['material'];
        $material = json_decode($res['material'],true);
        $where = [];
        $where['frameid'] = $res['frameid'];
        $material_list = array_column(Db::name('materials')->where($where)->where('name','in',implode(',', array_keys($material)))->select(), null,'name');
        $total = 0;//总价
        $datas = [];
        foreach($material as $k=>$v){
            $v['amcode'] = $material_list[$k]['amcode'];
            $v['img'] = $material_list[$k]['img'];
            $v['norms'] = $material_list[$k]['norms'];
            $v['units'] = $material_list[$k]['units'];
            $v['phr'] = $material_list[$k]['phr'];
            $v['category'] = $material_list[$k]['category'];//工种类别
            $v['fine'] = $material_list[$k]['fine'];//辅材细类
            $v['name'] = $k;//辅材细类

            if(!$material_list[$k]['category']){
                $material_list[$k]['category'] = '未分类';
            }
            if(!$material_list[$k]['fine']){
                $material_list[$k]['fine'] = '未分类';
            }
            $datas[$material_list[$k]['category']][$material_list[$k]['fine']][] = $v;

            $total += $v['price']*$v['num'];
        }
        $this->assign('material',$datas);
        $this->assign('total',$total);

       //  // $newarr = $material;
       //  // var_dump($material);die;

       //  $content = json_decode($res['content'],true);
       //  if ($content) {
       //  //核算清单
       //   $qingdan = json_decode($res['content'],true);
       //    // dump($qingdan);
       //   $newarr = [];
       //   foreach ($qingdan as $key => $value) {
       //     $newarr[$key]['frameid'] = $value['frameid'];
       //     $newarr[$key]['item_number'] = $value['item_number'];
       //     $newarr[$key]['gcl'] = $value['gcl'];
       //   }
       //    $dataall = Db::name('offerquota')->where(array('frameid'=>$newarr[0]['frameid']))->select();
       //    $cangku = Db::name('materials')->where(array('frameid'=>$newarr[0]['frameid']))->select();//仓库
       //    // dump($dataall);
       //    // $arrayn = [];
       //    foreach ($newarr as $kkk => $vvv) {
       //      $newarr[$kkk]['content'] = [];
       //      foreach ($dataall as $kk => $vv) {
       //        if ($vvv['item_number']==$vv['item_number']){
       //          $shuzus = json_decode($vv['content'],true);
       //          //去空
       //          foreach ($shuzus as $k => $v) {
       //            if ($v[0]==null) {
       //              unset($shuzus[$k]);
       //            }
       //          }
       //          foreach ($shuzus as $kbb => $vbb) {
       //            //通过函数获取所需的信息
       //              //dump($newarr[0]['frameid']);
       //              //dump($vbb[0]);
       //            $getval = $this->getcang($newarr[0]['frameid'],$vbb[0]);
       //               //dump($shuzus[$kbb]);
       //              //dump( $getval );
       //            $shuzus[$kbb] = array_merge($shuzus[$kbb],$getval);
       //            $shuzus[$kbb]['number'] = $vbb[1]*$vvv['gcl'];
       //            $shuzus[$kbb]['total'] = $vbb[1]*$vvv['gcl']*$shuzus[$kbb]['price'];
       //          }
       //          $newarr[$kkk]['content'] = $shuzus;
       //        }
       //      }
       //    }
       //   }else{
       //     $newarr = null;
       //   }
       //  // 组合数组
       //    $ress = array();
       //  foreach ($newarr as $kkk => $val) {
       //      $ress[$kkk] = $val['content'];
       //  }
       //  $arrs = array();
       //   foreach ($ress as $kkk => $val) {
       //        foreach($val as $k=>$v){
       //            $arrs[] = $v;
       //        }
       //   }
       //  //dump($arrs);

       //  //合并同类项
       //  $endarr = [];
       //  foreach ($arrs as $key => $value) {
       //    $endarr[$value[0]][] = $value; 
       //  }
       // foreach ($endarr as $key => $value) {
       //      $item=array();
       //      foreach($value as $k=>$v){
       //          if(!isset($item[$v[0]])){
       //              $item[$v[0]]=$v;
       //          }else{
       //              $item[$v[0]]['number']+=$v['number'];
       //              $item[$v[0]]['total']+=$v['total'];
       //          }
       //      }
       //      $endarr[$key] = $item;
                      
       //  }
      
       //  $allarrs = array();
       //   foreach ($endarr as $kkk => $val) {
       //        foreach($val as $k=>$v){
       //            $allarrs[] = $v;
       //        }
       //   }
       //  var_dump($newarr);
       //  var_dump($res);
       //  var_dump($allarrs);die;
       //  $this->assign('newarr',$newarr);
        $this->assign('data',$res);
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
