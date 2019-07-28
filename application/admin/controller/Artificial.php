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



class Artificial extends Adminbase
{
		public $status = [ '未报价','已报价','预算价','合同价','结算价' ];
    // protected function initialize()
    // {
    //     parent::initialize();
    //     $this->Menu = new Menu_Model;
    // }
   public function index()
    {

       $userinfo = $this->_userinfo; 
       $da['userid'] = $userinfo['userid'];
         if($userinfo['roleid'] != 1){
           $da['o.frameid'] = $userinfo['companyid'];
        }
      if($this->request->isPost()){
         $search = input('search'); 
         if($search){
            $sres = Db::name('artificial')->where('itemcode','like',"%".$search."%")->paginate(20);
            // dump($sres);
            $this->assign('data',$sres); 
             $frame = Db::name('frame')->field('id,name')->where('levelid',3)->select();
             $this->assign('frame',$frame);   
            return $this->fetch();       
         }else{
           $this->error('请输入搜索内容', url("Artificial/index"));
         }

      }else{
       // echo __STATIC__;
        $res = Db::name('Offerquota')->where($da)->paginate(20);
        $frame = Db::name('frame')->field('id,name')->where('levelid',3)->select();
        $this->assign('data',$res);    
        $this->assign('frame',$frame);    
        return $this->fetch();
      } 
    }
    //工程成本分析
    public function gcfx(){
        $userinfo = $this->_userinfo;
        $request = request();
        $id = $request->param('customerid');
        $da['o.customerid'] = $id;
        // dump(input());
        // echo $customerid;
        $res = Db::name('offerlist')->select();
        if ($res) {
            $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address,u.manager_name as manager_name')->join('userlist u','o.customerid = u.id')->where($da)->select();
        }
        $true = [];//存放结果数据
        // $content = json_decode($res['content'],true);
        // dump($res);
        //统计报价开始 
           foreach($res as $k=>$v){
						 $v['status'] = $this->status[$v['status']];
              $res[$v['id']] = $v;
              unset($res[$k]);
            }

            foreach ($res as $key => $value) {

             if($value['content']){
                $content = json_decode($value['content'],true);
                foreach($content as $keys => $values){
                    $res[$key]['matquant'] += $values['quotaall'];//辅材报价
                    $res[$key]['manual_quota'] += $values['craft_showall'];//人工报价
                }
                //工程直接费= 辅材报价+人工报价
                $res[$key]['direct_cost'] = $res[$key]['matquant']+$res[$key]['manual_quota'];
                //工程报价 = 工程直接费+管理费+搬运费+清洁费+工程意外险+远程费+旧房局部改造费+税金-优惠
                $res[$key]['proquant'] = $res[$key]['direct_cost']+$res[$key]['tubemoney']+$res[$key]['taxes']+$res[$key]['discount'];

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
            }
// dump($res);
          $this->assign('res',$res);
          
          if($this->request->isPost()){
            $true = input('true');
            $false = input('false');
            if(empty($true) || empty($false)){
              $this->error('请选择结算价和预算价');exit;
            }
            $true = $this->data_come($this->data_deal($res[$true]),$this->data_deal($res[$false]));
            
            // dump($true);
            $this->assign('true',$true);
          }

// dump($res);
        //区分已报价和未报价
        /*$re = [];
        $time = [];//报价录入时间
        foreach($res as $ke=>$va){
          if($va['status'] == 1){
            $re[1][] = $va;//已报价
            $time[1][] = $va['entrytime'];
          }else{
            $re[0][] = $va;//未报价
            $time[0][] = $va['entrytime'];
          }
        }

        if(!empty($re[1])){
          $true = $re[1][0];//有报价
          if(empty($re[0])){
            //预算和结算相同
            $false = $re[1][0];
          }else{
            //结算为已报价的，预算为提交时间最早的
            if(count($re[0]) >1){
              //比较
              $false = $this->mintime($time[0],$re[0]);
            }else{
              //不用比较
              $false = $re[0][0];
            }
          }
        }else{
          //无报价
          // $false = $re[0];
          if(empty($re[0])){
            //无报价数据
            die('无报价数据');
          }else{
            if(count($re[0])>1){
              //预算为提交时间最早的，结算为提交时间最晚的
              $false = $this->mintime($time[0],$re[0]);
              $true = $this->maxtime($time[0],$re[0]);
            }else{
              //预算和结算相同
              $false = $re[0][0];
              $true = $re[0][0];
            }
          }
        }
       $true = $this->data_come($this->data_deal($true),$this->data_deal($false));
      $this->assign(array(
        'data'=>$res[0],
        'true'=>$true,//结算
      ));*/
         return $this->fetch();
      }

    //成本分析选择选择客户
    public function gcfx_first(){
        $userinfo = $this->_userinfo; 
        $da = [];
        if($userinfo['userid'] != 1){
            $da['userid'] = $userinfo['userid'];
        }
        if(input('search')){
            $da['customer_name'] = input('search');
        }
        $userlist = Db::name('userlist')->where($da)->select();
        $this->assign('data',$userlist);    
        $this->assign('userinfo',$userinfo);  
        return $this->fetch();
    }
    public function gcfx_index(){
      error_reporting(E_ALL ^ E_WARNING);
        $userinfo = $this->_userinfo; 
        $da = [];
        // $da['o.userid'] = $userinfo['userid'];
        if($userinfo['roleid'] != 1){
           $da['o.frameid'] = $userinfo['companyid'];
        }
         if(input('id')){
           $da['o.customerid'] = input('id');
        }else{
            $da['o.customerid'] = 0;
        }
        $da['o.number'] = 1;
        //客户姓名搜索
        if($this->request->isPost()){
            $search = $this->request->post('search');
            if(empty($search)){
                $this->error('请输入搜索内容', url("artificial/gcfx_index"));
            }
            $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address,u.manager_name as manager_name')->join('userlist u','o.customerid = u.id')->where($da)->where('u.customer_name','LIKE','%'.$search)->select();
            $this->assign('data',$res);    
            return $this->fetch();
        }
        //所有客户信息
        $res = Db::name('offerlist')->alias('o')->field('o.*,u.customer_name as customer_name,u.quoter_name as quoter_name,u.designer_name as designer_name,u.address as address,u.manager_name as manager_name')->join('userlist u','o.customerid = u.id')->where($da)->select();

        //统计报价开始 
        foreach ($res as $key => $value) {
            $content = json_decode($value['content'],true);
            foreach($content as $keys => $values){
                $res[$key]['matquant'] += $values['quotaall'];//辅材报价
                $res[$key]['manual_quota'] += $values['craft_showall'];//人工报价
            }
            $res[$key]['direct_cost'] = $res[$key]['matquant']+$res[$key]['manual_quota'];//工程直接费= 辅材报价+人工报价

            //=========================计算毛利开始
            //计算杂项
            $res[$key]['supervisor_commission'] = round($res[$key]['supervisor_commission']/100*$res[$key]['direct_cost'],2);//监理提成
            $res[$key]['design_commission'] = round($res[$key]['design_commission']/100*$res[$key]['direct_cost'],2);;//设计提成
            $res[$key]['repeat_commission'] = round($res[$key]['repeat_commission']/100*$res[$key]['direct_cost'],2);;//回头客奖
            $res[$key]['business_commission'] = round($res[$key]['business_commission']/100*$res[$key]['direct_cost'],2);;//业务提成

            $res[$key]['carry'] = round($res[$key]['carry']/100*$res[$key]['direct_cost'],2);//搬运费
            $res[$key]['clean'] = round($res[$key]['clean']/100*$res[$key]['direct_cost'],2);//清洁费
            $res[$key]['accident'] = round($res[$key]['accident']/100*$res[$key]['direct_cost'],2);//工程意外险
            $res[$key]['remote'] = round($res[$key]['remote']/100*$res[$key]['direct_cost'],2);//远程费
            $res[$key]['old_house'] = round($res[$key]['old_house']/100*$res[$key]['direct_cost'],2);//旧房局部改造费
            $res[$key]['tubemoney'] = round($res[$key]['tubemoney']/100*$res[$key]['direct_cost'],2);//管理费
            $res[$key]['taxes'] = round($res[$key]['taxes']/100*$res[$key]['direct_cost'],2);//税金
            // $res[$key]['sundry'] //运杂
            // $res[$key]['discount'] //优惠


            //工程报价  辅材+人工+管理+搬运+清洁+意外险+远程+旧房改造+税金+运杂-优惠
            $res[$key]['proquant'] = $res[$key]['matquant']+$res[$key]['manual_quota']+$res[$key]['tubemoney']+$res[$key]['carry']+$res[$key]['clean']+$res[$key]['accident']+$res[$key]['remote']+$res[$key]['old_house']+$res[$key]['taxes']+$res[$key]['sundry']-$res[$key]['discount'];

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
                //毛利
                $res[$key]['gross_profit'] = round(($res[$key]['direct_cost'] - $res[$key]['artificial_cb'] - $res[$key]['material_cb'] - $res[$key]['discount'] - $res[$key]['sundry'] - $res[$key]['supervisor_commission'] - $res[$key]['design_commission'] - $res[$key]['repeat_commission'] - $res[$key]['business_commission'] ),2);
                //毛利率
                $res[$key]['profit_rate'] = round( $res[$key]['gross_profit'] / $res[$key]['direct_cost'] * 100,2);

            }else{
                $res[$key]['profit_rate']  = 0;
                $res[$key]['gross_profit']  = 0;
            }
        }
        $this->assign('data',$res);    
        return $this->fetch();
    }
    //解决预算和结算的数据来源 $true 已报价的项目 $false 未报价的项目
    public function data_come($true=[],$false=[]){
        if(!empty($true['content'])){
            foreach ($true['content'] as $key => $value) {
                 if(!empty($false['content'])){
                    foreach ($false['content'] as $k => $v) {
                        if($value['type_of_work'] == $v['type_of_work']){
                            $true['content'][$key]['pre_quotaall'] = $v['quotaall']; 
                            $true['content'][$key]['pre_craft_showall'] = $v['craft_showall']; 
                        }else{
                            $true['content'][$key]['pre_quotaall'] = 0; 
                            $true['content'][$key]['pre_craft_showall'] = 0; 
                        }
                    }
                }
            }

        }
        return $true;
    }
    //数据处理
    public function data_deal($data){
      $type_of_work = [];//按空间类型（工种）分类的数据
      $result = [];
      if(!empty($data['content'])){
        foreach ($data['content'] as $key => $value) {
          $type_of_work[] = $value['type_of_work']; 
        }
        $type_of_work = array_unique($type_of_work);
          foreach($type_of_work as $ke=>$val){
              foreach($data['content'] as $k=>$v){
                  if($val == $v['type_of_work']){
                    $result[$ke]['type_of_work'] = $val; 
                    $result[$ke]['projects'][] = $v; 
                  }
              }
          }
          $quotaall = 0;$craft_showall = 0;
          foreach($result as $key => $value){
              foreach ($value['projects'] as $k => $v) {
                  $quotaall += $v['quotaall'];
                  $craft_showall += $v['craft_showall'];
              }
              $result[$key]['quotaall'] = $quotaall;
              $result[$key]['craft_showall'] = $craft_showall;
          }
          $data['content'] = $result;
      }
      return $data;
    }
      //根据最早的录入时间取值
      public function mintime($compare,$data){
        $min = min($compare);
        foreach($data as $key=>$value){
          if($value['entrytime'] == $min){
            $result = $value;
          }
        }
        return $result;
      }
      //根据最晚的录入时间取值
      public function maxtime($compare,$data){
        $min = max($compare);
        foreach($data as $key=>$value){
          if($value['entrytime'] == $max){
            $result = $value;
          }
        }
        return $result;
      }

      //按辅材名称返回辅材单价
    public function returnPrice($val){
        if(is_null($val)){
            return null;
        }
        $re = Db::name('materials')->field('price')->where('name',$val)->find();
        return $re['price'];
    }


 // 弹窗修改信息
    public function ajaxedits(){
         $datas = input();
         if($datas){
            $data['type_of_work'] = $datas['type_of_work'];
            $data['project'] = $datas['project'];
            $data['company'] = $datas['company'];
            $data['labor_cost'] = $datas['labor_cost'];
           
            $res = Db::name('Offerquota')->where('id',$datas['id'])->update($data);
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
                 $res = Db::name('Offerquota')->where('id',$value)->update($data);
             }
            Result(0,'字段更新成功',$data);

            // $res = Db::name('Offerquota')->where('id',$datas['id'])->update($data);
            // if($res) {
            //    Result(0,'更新成功',$data);
            // }else{
            //    Result(1,'更新失败'); 
            // }
         }else{
            Result(1,'获取数据失败');
         }
    }

    // 报价导出数据接口
    // public function baojia()
    // {    

    //      $userinfo = $this->_userinfo; 
    //      $da['userid'] = $userinfo['userid'];
    //      $res = Db::name('offerlist')->where($da)->select();
       
    //      $newdata = array();
    //      //过滤无用字段
    //      foreach ($res as $k => $v) {
    //          unset($v['addtime']);
    //          $newdata[$k] = $v;

    //      }
    //        // dump($news);
    //      if($newdata){
    //           TreeResult(0,'ok',$newdata,count($newdata));
    //       }else{
    //           TreeResult(1,'获取失败');
    //       }
    // }


      //添加
    // public function add()
    // {
    //     if ($this->request->isPost()) {
    //         $data = $this->request->param();  
    //         if(!$data['typeid']) {
    //             $this->error('请选择分类');  
    //         }   
    //         $data['item_number'] = $this->GetNumber();
    //         $data['addtime'] = time();
    //         // dump($data);exit;
    //         if (Db::name('offerlist')->insert($data)) {
    //             $this->success("添加成功！", url("offerlist/index"));
    //         } else {
    //             $this->error('添加失败！');
    //         }
    //     }else{
    //          $res = Db::name('offer_type')->field('id,name')->select();;
    //           $this->assign("data", $res);
    //         return $this->fetch();
    //     }
    // }



    //编辑后台菜单
    // public function edit()
    // {
    //     if ($this->request->isPost()) {
    //         $data = $this->request->param();
    //          // dump($data);exit; 
    //         if(Db::name('offerlist')->where('id', $data['id'])->update($data)){
    //             $this->success("编辑成功！", url("offerlist/index"));
    //         }else{
    //             $this->error('编辑失败了！');
    //         }
    //     } else {
    //         $request = request();
    //         $id = $request->param('id');
    //         $rs = Db::name('offerlist')->where(["id" => $id])->find();    
    //         $res = Db::name('offer_type')->field('id,name')->select();;

    //         $this->assign("ones", $res); 
    //         $this->assign("data", $rs);
    //         return $this->fetch();
    //     }

    // }

  // 列表单字段修改
    public function singlefield_edit()
    {
        if ($this->request->isPost()) {
            $receive = $this->request->param();
            $data[$receive['field']] = $receive['value'];
            if(Db::name('offerquota')->where('id', $receive['id'])->update($data)){
                 Result(0,'单字段编辑成功'); 
            }else{
                Result(1,'编辑失败了！'); 
            }
        } else {
           Result(1,'获取字段信息失败'); 
        }

    }

// 导入excel表
    public function ImportExcel(Request $request){
            $da = $this->request->param();
           if ($_FILES['excel']['error'] == 4) {
             $this->error('没有文件被上传', url("Artificial/index"));die;
           }
           if ($da['frameid'] == '') {
             $this->error('请选择导入的公司', url("Artificial/index"));die;
           }
         
           // dump($da);exit;
           // $userInfo = $this->_userinfo;
           // if(!$userInfo) {
           //      $this->error('无法获取当前操作人员');die;
           //  }
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
               if ($col_num != 'E') {
                   $this->error('文件数据字段不匹配，请重新选择');die;
                } 
                for ($i = 2; $i <= $row_num; $i ++) {
                    $data[$i]['frameid']  = $da['frameid'];
                    $data[$i]['itemcode']  = $sheet->getCell("A".$i)->getValue();
                    $data[$i]['worktype']  = $sheet->getCell("B".$i)->getValue();
                    $data[$i]['title']  = $sheet->getCell("C".$i)->getValue();
                    $data[$i]['company']  = $sheet->getCell("D".$i)->getValue();
                    $data[$i]['labor_cost']  = $sheet->getCell("E".$i)->getValue(); 
                    
                }
                // dump($data);exit;
                //将数据保存到数据库
                if ($data) {
                   //把获取到的二维数组遍历进数据库
                   foreach ($data as $key => $value) {
                       $res = Db::name('artificial')->insert($value);
                   }
                   $this->success('导入成功');
                }else{
                  $this->error('获取导入文件数据失败');
                }

           }else{
              $this->error('请选择上传文件');
           }
           


    }


    /**
      * @LoadExcel  下载excel数据
      * @author  Han
      * @version  1.0
      * @param null
    */

    public function LoadExcel(Request $request){
        ob_clean();
        require'../extend/PHPExcel/PHPExcel/Worksheet.php';
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'ID')
        ->setCellValue('B1', '用户')
        ->setCellValue('C1', '详情')
        ->setCellValue('D1', '结果')
        ->setCellValue('E1', '时间')
        ->setCellValue('F1', 'IP');
        $spreadsheet->getActiveSheet()->setTitle('登陆日志');
        dump($spreadsheet);
    }

  //删除
    public function delete()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param(); 
            if(!$data['id']) {
                 Result(1,'数据不存在');  
            }
           if (Db::name('Offerquota')->where(array('id'=>$data['id']))->delete()) {
                Result(0,'删除成功');  
            } else {
                Result(1,'删除失败');  
            }
        }  
    }

    /**
     * 删除
     */
    // public function delete()
    // {
    //     $id = $this->request->param('id/d');
    //     if (empty($id)) {
    //         $this->error('ID错误');
    //     }
    //     $result = Db::name('menu')->where(["parentid" => $id])->find();
    //     if ($result) {
    //         $this->error("含有子菜单，无法删除！");
    //     }
    //     if ($this->Menu->del($id) !== false) {
    //         $this->success("删除菜单成功！");
    //     } else {
    //         $this->error("删除失败！");
    //     }
    // }
    
    //获取人工成本详情  
    public function ajax_get_artificial_cb_info(){
        $id = input('id');
        $info = Db::name('offerlist')->where(['id'=>$id])->find();
        if(!$info){
            echo json_encode(array('code'=>1,'msg'=>'订单信息有误'));die;
        }
        $artificial = json_decode($info['artificial'],true);
        if($artificial){
            $item_number = array_keys($artificial);
        }else{
            echo json_encode(array('code'=>1,'msg'=>'无成本详情'));die;
        }
        
        $offerquota_list = Db::name('offerquota')->where('item_number','in',implode(',', $item_number))->where(['frameid'=>$info['frameid']])->select();
        if($offerquota_list){
            $offerquota_list = array_column($offerquota_list,null,'item_number');
        }else{
            $offerquota_list = [];
        }
        $arr = [];//拼装数组
        $total = 0;
        foreach($artificial as $k=>$v){
            if(!isset($arr[$offerquota_list[$k]['type_of_work']])){
                $arr[$offerquota_list[$k]['type_of_work']] = 0;
            }
            $arr[$offerquota_list[$k]['type_of_work']] += $v['cb']*$v['num'];
            $total += $v['cb']*$v['num'];
        }
        echo json_encode(array('code'=>0,'datas'=>$arr,'total'=>$total));
    }

}