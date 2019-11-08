<?php

// +----------------------------------------------------------------------
// | 组织架构管理
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

class Indent extends Adminbase
{
    // protected function initialize()
    // {
    //     parent::initialize();
    //     $this->Menu = new Menu_Model;
    // }

    //
    public function index(){
    
        $res = Db::name('frame')->field('id,name')->where(['levelid'=>2,'status'=>0])->select();
        // $res = array_merge($res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res,$res);
      
        // dump($res);
        $this->assign('data',$res);    
        return $this->fetch();
    }

    // 获取组织架构接口
    public function TreeType(){
      $res = Db::name('frame')->select();
      // dump($res);
      if($res){
          TreeResult(0,'ok',$res,count($res));
      }else{
          TreeResult(1,'获取失败');
      }

    }

       // 添加组织架构信息
    public function adds(){
         $datas = input();
         if($datas){
            $data['name'] = $datas['name'];
            $data['other'] = $datas['other'];
            $data['pid'] = $datas['pid'];
            $data['levelid'] = $datas['levelid']+1;
            $res = Db::name('frame')->insert($data);
            if ($res) {
               Result(0,'添加成功');
            }else{
               Result(1,'添加失败'); 
            }
         }else{
            Result(1,'获取pid失败');
         }
    }

    // 删除组织架构信息
    public function dels(){
         $datas = input();
         if($datas){
           $ziji = Db::name('frame')->where('pid',$datas['id'])->find();
           if ($ziji) {
               Result(1,'请先删除下面的子类');die;
           }
            $res = Db::name('frame')->where('id',$datas['id'])->delete();
            // $res = 1;
            if ($res) {
               Result(0,'删除成功');
            }else{
               Result(1,'删除失败'); 
            }
         }else{
            Result(1,'信息不完整');
         }
    }

    // 禁用启用
    public function editstatu(){
         $datas = input();
         if($datas){
           $ziji = Db::name('frame')->where('pid',$datas['id'])->find();
           if ($ziji) {
               Result(1,'此项父级不能执行此操作');die;
           }
            if ($datas['status'] == 0) {
               $data['status'] = 1;
               $res = Db::name('frame')->where('id',$datas['id'])->update($data);
               $admin = Db::name('admin')->where('companyid',$datas['id'])->select();
               if($admin){
                 foreach ($admin as $key => $value) {
                  $upsta = Db::name('admin')->where('userid',$value['userid'])->update(['status'=>0]);
                 }
                 Result(0,'禁用成功');
               }else{
                 Result(0,'操作成功，但该公司没有操作员');
               }

               
            }elseif($datas['status'] == 1){
               // $data['status'] = 0;
               // $res = Db::name('frame')->where('id',$datas['id'])->update($data);
               Result(1,'已经禁用');
            }else{
               Result(1,'操作失败'); 
            }
         }else{
            Result(1,'信息不完整');
         }
         // dump($datas);

    }

      // 修改组织架构信息
    public function edits(){
         $datas = input();
         if($datas){
            if (isset($datas['frameid'])) {
              if (is_numeric($datas['frameid'])) {
                 $data['pid'] = $datas['frameid'];
              }
            }            
            $data['name'] = $datas['name'];
            $data['other'] = $datas['other'];
            $res = Db::name('frame')->where('id',$datas['id'])->update($data);
            if($res) {
               Result(0,'更新成功');
            }else{
               Result(1,'更新失败'); 
            }
         }else{
            Result(1,'获取数据失败');
         }
         // dump($datas);

    }


 

    //添加
    // public function add()
    // {
    //     if ($this->request->isPost()) {
    //         $data = $this->request->param();         

    //         if(empty($data['name'])) {
    //             $this->error('车辆不能为空');             
    //         }elseif($data['sort']<0) {
    //             $this->error('排序数字不合法');        
    //         }
 
    //         if (Db::name('vehicle_type')->insert($data)) {
    //             $this->success("添加成功！", url("Carlist/index"));
    //         } else {
    //             $error = Db::name('vehicle_type')->getError();
    //             $this->error($error ? $error : '添加失败！');
    //         }
    //     } else {

    //         return $this->fetch();
    //     }
    // }

    //编辑后台菜单
    public function edit()
    {
        if ($this->request->isPost()) {
            // $data = $this->request->param();
              
            // $eui['name'] = $data['name'];
            // $eui['sort'] = $data['sort'];
            // $eui['remarks'] = $data['remarks'];
            // $eui['load'] = $data['load'];
            // $eui['lwh'] = $data['lwh'];
            // $eui['volume'] = $data['volume'];
            // $eui['start_money'] = $data['start_money'];
            // $eui['extra_mileage'] = $data['extra_mileage'];
   

            // if (Db::name('vehicle_type')->where('vehicle_type_id', $data['id'])->update($eui)) {
            //     $this->success("编辑成功！", url("Carlist/index"));
            // } else {
            //     $error = Db::name('vehicle_type')->getError();
            //     $this->error($error ? $error : '编辑失败！');
            // }
        } else {
            $request = request();
            $id = $request->param('id');
            $rs = Db::name('indent')->where(["Id" => $id])->find();
            // dump($rs);
            $this->assign("data", $rs);
            return $this->fetch();
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

    //    // echo $id;
    //     if (Db::name('vehicle_type')->where(["vehicle_type_id" => $id])->delete()) {
    //         $this->success("删除成功", url("Carlist/index"));
    //     } else {
    //         $this->error("删除失败！");
    //     }
    // }
    // 

         //设置状态
    // public function setstate($id, $status)
    // {
    //     $id = (int) input('id/d');
    //     $status = (int) input('status/d');


    //     if (($status != 0 && $status != 1) || !is_numeric($id) || $id < 0) {
    //         return '参数错误';
    //     }elseif ($status==0) {
    //        $status=1;
    //     }elseif ($status==1) {
    //        $status=0;
    //     }


    //     if (Db::name('driver')->where('id', $id)->update(['status' => $status])) {
    //         $this->error('更新成功');
    //     } else {
    //         $this->error('更新失败');
    //     }
    // }
    // 导入excel表
    public function ImportExcel(Request $request){
          
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
                $data[$i]['name']  = $sheet->getCell("A".$i)->getValue();
                $data[$i]['other']  = $sheet->getCell("B".$i)->getValue();
                $data[$i]['levelid']  = $sheet->getCell("C".$i)->getValue();
                $data[$i]['pid']  = $sheet->getCell("D".$i)->getValue();
                $data[$i]['status']  = $sheet->getCell("E".$i)->getValue();
            }

            //将数据保存到数据库
            if ($data) {
               //把获取到的二维数组遍历进数据库
               foreach ($data as $key => $value) {
                   $res = Db::name('frame')->insert($value);
               }
               $this->success('导入成功');
            }else{
              $this->error('获取导入文件数据失败');
            }

       }else{
          $this->error('请选择上传文件');
       }
    }

    //分公司自行配置基础信息页面
    public function set_tmp(){
        // $this->assign("data", $rs);
        $cost_tmp = Db::name('cost_tmp')->where(['f_id'=>$this->_userinfo['companyid']])->find();
        if(!$cost_tmp){
            $cost_tmp = [
                // 'taxes'=>0,
                'supervisor'=>0,
                'design'=>0,
                'repeat'=>0,
                'business'=>0,
                'order_tfoot'=>'',
                'take_rate1'=>0,
                'take_rate2'=>0,
                'take_rate3'=>0,
                'take_rate4'=>0,
                'pick_rate'=>0,
                'order_check'=>'',
            ];//返回空数据
        }
        if($cost_tmp['order_check']){
            $cost_tmp['order_check'] = json_decode($cost_tmp['order_check'],true);
            foreach ($cost_tmp['order_check'] as $k => $v) {
                if(empty($v[1])){
                    $cost_tmp['order_check'][$k] = $v[0];
                }else{
                    $cost_tmp['order_check'][$k] = implode('-', $v);
                }
            }
            $cost_tmp['order_check'] = implode("\n", $cost_tmp['order_check']);
        }
        $this->assign("data", $cost_tmp);
        $this->assign("f_id", $this->_userinfo['companyid']);
        return $this->fetch();
    }

    public function get_tmp(){
        $f_id = input('f_id');
        $cost_tmp = Db::name('cost_tmp')->where(['f_id'=>$f_id])->find();
        if(!$cost_tmp){
            $cost_tmp = [
                            // 'tubemoney'=>0,
                            // 'carry'=>0,
                            // 'clean'=>0,
                            // 'accident'=>0,
                            // 'remote'=>0,
                            // 'old_house'=>0,
                            // 'taxes'=>0,
                            'supervisor'=>0,
                            'design'=>0,
                            'repeat'=>0,
                            'business'=>0,
                            'order_tfoot'=>'',
                            'take_rate1'=>0,
                            'take_rate2'=>0,
                            'take_rate3'=>0,
                            'take_rate4'=>0,
                            'pick_rate'=>0,
                            'order_check'=>'',
                        ];//返回空数据
        }
        if($cost_tmp['order_check']){
            $cost_tmp['order_check'] = json_decode($cost_tmp['order_check'],true);
            foreach ($cost_tmp['order_check'] as $k => $v) {
                if(empty($v[1])){
                    $cost_tmp['order_check'][$k] = $v[0];
                }else{
                    $cost_tmp['order_check'][$k] = implode('-', $v);
                }
            }
            $cost_tmp['order_check'] = implode("\n", $cost_tmp['order_check']);
        }
        Result(0,'',$cost_tmp);
    }
                // if(in_array($v[0], $title)){
                //     Result(1,'流程名称不能重复');
                // }
                // $title[] = $v[0];
    public function edit_tmp(){
        $f_id = input('f_id');
        $datas['supervisor'] = input('supervisor');
        $datas['design'] = input('design');
        $datas['repeat'] = input('repeat');
        $datas['business'] = input('business');
        $datas['order_tfoot'] = input('order_tfoot');
        $datas['take_rate1'] = input('take_rate1');
        $datas['take_rate2'] = input('take_rate2');
        $datas['take_rate3'] = input('take_rate3');
        $datas['take_rate4'] = input('take_rate4');
        $datas['pick_rate'] = input('pick_rate');
        $datas['order_check'] = input('order_check');

        $order_check = explode("\n", input('order_check'));
        $title = [];
        foreach($order_check as $k=>$v){
            $info = explode('-', $v);
            if(mb_strlen($info[0]) > 3){
                Result(1,'流程名称不能超过3个字');
            }
            if(in_array($info[0], $title)){
                Result(1,'流程名称不能重复');
            }

            $title[] = $info[0];
            if(!isset($info[1])){
                $info[1] = '';//没有说明
            }
            $info = array_slice($info,0,2);
            $order_check[$k] = $info;
        }
        $datas['order_check'] = json_encode($order_check);
        
        if($datas['take_rate1'] + $datas['take_rate2'] + $datas['take_rate3'] + $datas['take_rate4'] != 100){
            Result(1,'收款比率合计必须为100');
        }
        $cost_tmp = Db::name('cost_tmp')->where(['f_id'=>$f_id])->find();
        if($cost_tmp){
            //修改
            $res = Db::name('cost_tmp')->where(['f_id'=>$f_id])->update($datas);
        }else{
            //添加
            $datas['f_id'] = $f_id;
            $res = Db::name('cost_tmp')->insert($datas);

        }
        if($res){
            Result(0,'修改成功');
        }else{
            Result(1,'修改失败');
        }
    }

















}
