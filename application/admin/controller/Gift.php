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



class Gift extends Adminbase{

    //礼品库
    public function gift_index(){
        // $this->assign('frame',$frame);
        $where = [];
        if(input('gname')){
            $where[] = ['name','like','%'.input('gname').'%'];
        }
        if(input('gbrand')){
            $where[] = ['brand','like','%'.input('gbrand').'%'];
        }
        if(input('status')){
            $where[] = ['status','=',input('status')];
        }
        if(input('fid') && $this->_userinfo['roleid'] == 1){
            $where[] = ['fid','=',input('fid')];
        }else{
            $where[] = ['fid','=',$this->_userinfo['companyid']];
        }
        if(input('cates')){
            $where[] = ['cate','like','%'.input('cates').'%'];
        }
        $data = Db::name('gift')->where($where)->paginate(20,false,['query'=>request()->param()]);
        $cates = Db::name('gift')->group('cate')->field('cate') ->where('fid',$this->_userinfo['companyid'])->select();
        $cate=[];
        foreach ($cates as $k=>$v){
            if(!empty($v['cate'])){
                $cate[]=$v['cate'];
            }
        }
        $frame = array_column(Db::name('frame')->where('levelid',3)->field('id,name')->select(), null,'id');
        $this->assign('data',$data);
        $this->assign('cate',$cate);
        $this->assign('frame',$frame);
        $this->assign('admininfo',$this->_userinfo);
        return $this->fetch();
    }

    //ajax添加礼品库
    public function gift_add(){
        $data['name'] = input('name');
        $data['brand'] = input('brand');
        $data['price'] = input('price');
        $data['cost'] = input('cost');
        $data['content'] = input('content');
        $data['cate'] = input('cate');
        if(!$data['name']){
            $this->error('名称不能为空');
        }
        if(!is_numeric($data['price']) || $data['price'] < 0){
            $this->error('市场价有误');
        }
        if(!is_numeric($data['cost']) || $data['cost'] < 0){
            $this->error('成本有误');
        }
        $data['adminid'] = $this->_userinfo['userid'];
        $data['fid'] = $this->_userinfo['companyid'];
        $res = Db::name('gift')->insert($data);
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    //ajax修改礼品库
    public function gift_edit(){
        $id = input('id');
        $data['name'] = input('name');
        $data['brand'] = input('brand');
        $data['price'] = input('price');
        $data['cost'] = input('cost');
        $data['content'] = input('content');
        if(!$data['name']){
            $this->error('名称不能为空');
        }
        if(!is_numeric($data['price']) || $data['price'] < 0){
            $this->error('市场价有误');
        }
        if(!is_numeric($data['cost']) || $data['cost'] < 0){
            $this->error('成本有误');
        }
        $res = Db::name('gift')->where(['id'=>$id])->update($data);
        if($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }

    //ajax修改礼品库
    public function gift_del(){
        $id = input('id');
        $has = Db::name('offerlist')->where('gift','like','%"'.$id.'":%')->find();
        if($has){
            $this->error('该礼品已使用，禁止删除');
        }
        $res = Db::name('gift')->where(['id'=>$id])->delete();
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //ajax启用/禁用礼品库
    public function gift_edit_status(){
        $id = input('id');
        $status = input('status');
        
        $res = Db::name('gift')->where(['id'=>$id])->update(['status'=>$status]);
        if($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }

    //ajax 获取分公司的礼品库
    public function get_gift_byf(){
        $admininfo = $this->_userinfo;
        $data = Db::name('gift')->field('id,name,price,content,brand')->where(['fid'=>$admininfo['companyid'],'status'=>1])->select();
        if($data){
            $this->success('success','',$data);
        }else{
            $this->error('暂无礼品信息');
        }
    }

    //ajax 添加礼品到订单
    public function order_add_gift(){
        $admininfo = $this->_userinfo;
        $gift = input('gift');
        $g_num = input('g_num');
        $g_name = input('g_name');
        $data = [];
        if($gift){
            foreach($gift as $k=>$v){
                if($g_num[$v] > 0 && is_numeric($g_num[$v])){
                    $data[$v] = $g_num[$v];
                }else{
                    $this->error($g_name[$v].'数量格式填写错误');
                }
            }
            $json = json_encode($data);
        }else{
            $json = '';
        }
        $res = Db::name('offerlist')->where(['id'=>input('id')])->update(['gift'=>$json]);
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }
    //获取订单礼品详情
    public function get_gift_list(){
        $oid = input('oid');
        $datas = Model('gift')->getGiftList($oid,'id,name,price,brand,content');
        $this->success('success','',$datas);
    }
}