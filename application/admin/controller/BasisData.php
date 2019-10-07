<?php

// +----------------------------------------------------------------------
// | 辅材基础数据 分公司提供
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;
use think\Request;
use think\db\Where;
use think\Cache;

use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;



class BasisData extends Adminbase{
    public function project(){
        $admininfo = $this->_userinfo; 
        $where = [];
        $where['frameid'] = $admininfo['companyid'];
        $res = Db::name('offerquota')->where($where)->select();
        // $res = Db::name('basis_offerquota')->where($where)->select();
        $this->assign('data',$res);
        return $this->fetch();
    }

    public function save_project(){
        $admininfo = $this->_userinfo; 
        $item_number = input('item_number');
        $craft_show = input('craft_show'); //人工单价
        $labor_cost = input('labor_cost'); //人工成本
        $quota = input('quota'); //辅材单价
        $basis_offerquota = Db::name('basis_offerquota')->where(['item_number'=>$item_number,'frameid'=>$admininfo['companyid']])->find();
        if($basis_offerquota){
            $update = [];
            $update['craft_show'] = $craft_show;
            $update['labor_cost'] = $labor_cost;
            $update['quota'] = $quota;
            $update['userid'] = $admininfo['userid'];
            $res = Db::name('basis_offerquota')->where(['item_number'=>$item_number,'frameid'=>$admininfo['companyid']])->update($update);
            if($res){
                $this->success('保存成功');
            }else{
                $this->error('保存失败');
            }
        }else{
            $basis_offerquota = Db::name('basis_offerquota')->where(['item_number'=>$item_number,'frameid'=>0])->find();
            $basis_offerquota['frameid'] = $admininfo['companyid'];
            $basis_offerquota['craft_show'] = $craft_show;
            $basis_offerquota['labor_cost'] = $labor_cost;
            $basis_offerquota['quota'] = $quota;
            $basis_offerquota['userid'] = $admininfo['userid'];
            unset($basis_offerquota['id']);
            $res = Db::name('basis_offerquota')->insert($basis_offerquota);
            if($res){
                $this->success('保存成功');
            }else{
                $this->error('保存失败');
            }
        }
    }
}
