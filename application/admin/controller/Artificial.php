<?php

// +----------------------------------------------------------------------
// | 报价管理
// +----------------------------------------------------------------------
namespace app\admin\controller; 

use app\admin\model\Userlist;
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
  public $show_page = 15;
    // protected function initialize()
    // {
    //     parent::initialize();
    //     $this->Menu = new Menu_Model;
    // }
    public function index(){

        $userinfo = $this->_userinfo;
        $where = [];
        $da['userid'] = $userinfo['userid'];
        if($userinfo['roleid'] != 1){
            $da['frameid'] = $userinfo['companyid'];
        }
        if (!empty($_GET['item_number'])) {
            $where[] = ['item_number','like',"%{$_GET['item_number']}%"];
        }
        if (!empty($_GET['company'])) {
            $where[] = ['frameid','like',"{$_GET['company']}"];
        }

        if (!empty($_GET['type_of_work'])) {
            $where[] = ['type_of_work','like',"{$_GET['type_of_work']}"];
        }
        // echo __STATIC__;
        $res = Db::name('Offerquota')->where($da)->where($where)->paginate(20,false,['query'=>request()->param()]);
        $frame = Db::name('frame')->field('id,name')->where('levelid',3)->select();
        $company = Db::table('fdz_frame')->select();
        $gz=Db::name('offerquota')->group('type_of_work')->select();
        $this->assign('data',$res);    
        $this->assign('frame',$frame);
        $this->assign('gz',$gz);
        $this->assign(  'company' ,$company);
        return $this->fetch();
    }
    //工程成本分析
    public function gcfx(){
        $userinfo = $this->_userinfo;
        $request = request();
        $id = $request->param('customerid');//客户id
        $userinfo = Db::name('userlist')->where(['id'=>$id])->find();
        $this->assign('userinfo',$userinfo);//客户信息

        //选择框
        $option = Db::name('offerlist')->where(['customerid'=>$id])->field('status,unit,id')->select();

        $this->assign('res',$option);//菜单
        $this->assign('status',[0=>'未报价',1=>'已报价',2=>'预算价',3=>'合同价',4=>'结算价']);//菜单
        //对比
        if(input('true') && input('false')){
            $datas = [];//最终订单信息数据
            $item_info = [];//按项目的详情
            $datas['true'] = model('offerlist')->get_order_info(input('true'));
            $datas['false'] = model('offerlist')->get_order_info(input('false'));
            $user_id = 
            $item_info['true'] = model('offerlist')->get_info_for_item(input('true'))['datas'];
            $item_info['false'] = model('offerlist')->get_info_for_item(input('false'))['datas'];
            $item_info_new = []; //组装数据 每一个工种的详情
            foreach($item_info as $k1=>$v1){
                foreach($v1 as $k2=>$v2){
                    $item_info_new[$k2][$k1] = $v2;
                }
            }
            $this->assign('datas',$datas);
            $this->assign('item_info',$item_info_new);
        }

        return $this->fetch();
    }
    

    //成本分析选择选择客户
    public function gcfx_first(){
        $userinfo = $this->_userinfo; 
        $condition = [];//用于时间搜索 new where不会用
        $where = [];
        $da = [];
        if(input('customer_name')){
            $where[] = ['customer_name','LIKE','%'.input('customer_name').'%'];
        }
        if(input('quoter_name')){
            $where[] = ['quoter_name','LIKE','%'.input('quoter_name').'%'];
        }
        if(input('designer_name')){
            $where[] = ['designer_name','LIKE','%'.input('designer_name').'%'];
        }
        if(input('address')){
            $where[] = ['address','LIKE','%'.input('address').'%'];
        }
        if(input('manager_name')){
            $where[] = ['manager_name','LIKE','%'.input('manager_name').'%'];
        }
        if(input('frameid') && $userinfo['roleid'] == 1){
            $where[] = ['frameid','=',input('frameid')];
        }
        if(input('begin_time') && input('end_time')){
            $condition = array(['addtime','>',strtotime(input('begin_time'))],['addtime','<',strtotime('+1 day',strtotime(input('end_time')))]);
        }   
        if($userinfo['userid'] != 1 && $userinfo['roleid'] != 10){
            $da['userid'] = $userinfo['userid'];
        }
        if($userinfo['roleid'] == 10){
            $da['frameid'] = $userinfo['companyid'];
        }
        $off=Db::name('offerlist')->field('customerid')->select();
        $off = array_unique(array_column($off,'customerid'));
        $re = Userlist::with('profile')->where($where)->where('id','in',$off)->where($da)->where($condition)->order('id','desc')->paginate($this->show_page,false,['query'=>request()->param()]);

//        $re = json_decode($re,true);
//        dump($re);
//        $kn = Userlist::with('profile')->select();
//        dump($kn);die;
        if($userinfo['roleid'] == 1){
          $frame = Db::name('frame')->field('id,name')->where('levelid',3)->select();
          $this->assign('frame',$frame);
        }
        $this->assign('data',$re);
//        dump($re);
        $this->assign('userinfo',$userinfo);
        return $this->fetch();
    }
    public function gcfx_index(){
        error_reporting(E_ALL ^ E_WARNING);
        $userinfo = $this->_userinfo; 
        $da = [];
        // $da['o.userid'] = $userinfo['userid'];
        if($userinfo['roleid'] != 1){
           $where['frameid'] = $userinfo['companyid'];
        }
         if(input('id')){
           $where['customerid'] = input('id');
        }else{
            $where['customerid'] = 0;
        }
        $where['number'] = 1; //不知道是什么
        //客户姓名搜索
        if(input('search')){
            // $where[]
        }
        //所有客户信息
        $res = Db::name('offerlist')->where($where)->field('id,remark')->select();

        //统计报价开始 
        foreach($res as $k=>$v){
            $res[$k]['order_info'] = Model('offerlist')->get_order_info($v['id']);
        }
        //用户信息
        $userinfo = Db::name('userlist')->where(['id'=>input('id')])->find();//客户信息
        $this->assign('data',$res);    
        $this->assign('userinfo',$userinfo);    
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
    public function ajax_get_artificial(){
        $id = input('id');
        $info = Db::name('offerlist')->where(['id'=>$id])->find();
        if(!$info){
            echo json_encode(array('code'=>1,'msg'=>'订单信息有误'));die;
        }
        // $artificial = json_decode($info['artificial'],true);
        
        $artificial = Db::name('order_project')->where(['o_id'=>$id,'type'=>1])->select();
        if(!$artificial){
            echo json_encode(array('code'=>1,'msg'=>'无成本详情'));die;
        }
        $arr = [];//拼装数组
        $total = 0;
        foreach($artificial as $k=>$v){
            if(!isset($arr[$v['type_of_work']])){
                $arr[$v['type_of_work']] = 0;
            }
            $arr[$v['type_of_work']] += $v['labor_cost']*$v['num'];
            $total += $v['labor_cost']*$v['num'];
        }
        echo json_encode(array('code'=>0,'datas'=>$arr,'total'=>$total));
    }

    //获取辅料成本详情  
    public function ajax_get_material(){
        $id = input('id');
        $list = Db::name('order_material')->where(['o_id'=>$id,'status'=>1])->select();//该订单全部辅料 增减项除外
        if(!$list){
            echo json_encode(array('code'=>1,'msg'=>'无辅料信息'));die;
        }
        
        $material_list = model('artificial')->getmaterial_info($id);
        $total = 0;
        //反正需要的数据
        $arr = [];
        foreach($material_list as $k1=>$v1){
            foreach($v1 as $k2=>$v2){
                if(!isset($arr[$k1])){
                    $arr[$k1] = 0;
                }
                $arr[$k1] += $v2['omit_num']*$v2['cb'];
                $total += $v2['omit_num']*$v2['cb'];
            }
        }
        echo json_encode(array('code'=>0,'datas'=>$arr,'total'=>$total));
    }

    public function ajax_get_order_cb(){
        $id = input('id');
        $arr = [];
        $total = ['price'=>0,'cb'=>0];
        $offerlist = Db::name('offerlist')->where(['id'=>$id])->find();
        $content = Db::name('order_project')->where(['o_id'=>$id,'type'=>1])->select();
        // $content = json_decode($offerlist['content'],true);
        foreach($content as $k=>$v){
             if(!isset($arr[$v['type_of_work']])){
                $arr[$v['type_of_work']]['cb'] = 0;
                $arr[$v['type_of_work']]['price'] = 0;
            }
            $arr[$v['type_of_work']]['price'] += $v['quota']*$v['num'];//辅材单价
            $arr[$v['type_of_work']]['price'] += $v['craft_show']*$v['num'];//人工单价
            $arr[$v['type_of_work']]['cb'] += $v['labor_cost']*$v['num'];//人工成本

            $total['price'] += ($v['quota']*$v['num']+$v['craft_show']*$v['num']);//辅材+人工单价
            $total['cb'] += $v['labor_cost']*$v['num'];//人工成本
        }


        // //辅材成本
        $order_material = Db::name('order_material')->where(['o_id'=>$id,'status'=>1])->select();//该订单全部辅料 增减项除外
        $material_list = model('artificial')->getmaterial_info($id);
        foreach($material_list as $k1=>$v1){
            foreach($v1 as $k2=>$v2){
                if(!isset($arr[$k1])){
                    $arr[$k1]['cb'] = 0;
                    $arr[$k1]['price'] = 0;
                }
                $arr[$k1]['cb'] += $v2['omit_num']*$v2['cb']; //辅材成本
                $total['cb'] += $v2['omit_num']*$v2['cb'];  //辅材成本合计
            }
        }

        
        //其他成本 
        echo json_encode(array('code'=>0,'datas'=>$arr,'total'=>$total));
    }


}