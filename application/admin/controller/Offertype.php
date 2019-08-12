<?php

// +----------------------------------------------------------------------
// | 司机管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;

class Offertype extends Adminbase
{
    //空间类型
    public function index()
    {
        $userinfo = $this->_userinfo;
        $datas = Db::name('offer_type')->where(['type'=>1,'companyid'=>$userinfo['companyid'],'status'=>1])->select();
        $this->assign('datas',$datas);   
        return $this->fetch();
    }

    //工种
    public function space_index(){
        $userinfo = $this->_userinfo;
        $datas = Db::name('offer_type')->where(['type'=>2,'companyid'=>$userinfo['companyid'],'status'=>1])->select();
        $this->assign('datas',$datas);   
        return $this->fetch();
    }
    
    //新增
    public function ajax_add_word(){
        if(input('name') && input('type')){
            $userinfo = $this->_userinfo;
            $offer_type = Db::name('offer_type')->where(['name'=>input('name'),'type'=>input('type'),'companyid'=>$userinfo['companyid']])->find();
            if($offer_type){
                if($offer_type['status'] == 0){
                    echo json_encode(['code'=>1,'msg'=>'工种已存在']);
                }elseif($offer_type['status'] == 9){
                    Db::name('offer_type')->where(['name'=>input('name'),'type'=>input('type'),'companyid'=>$userinfo['companyid']])->update(['status'=>1,'addtime'=>time()]);
                    echo json_encode(['code'=>1,'msg'=>'添加成功']);
                }
            }else{
                Db::name('offer_type')->insert(['name'=>input('name'),'type'=>input('type'),'companyid'=>$userinfo['companyid'],'addtime'=>time()]);
                echo json_encode(['code'=>1,'msg'=>'添加成功']);
            }
        }else{
             echo json_encode(['code'=>0,'msg'=>'参数错误']);
        }
    }

    //编辑
    public function ajax_edit_word(){
        if(input('name') && input('type') && input('id')){
            $userinfo = $this->_userinfo;
            $res = Db::name('offer_type')->where(['id'=>input('id'),'type'=>input('type'),'companyid'=>$userinfo['companyid']])->update(['name'=>input('name')]);
            if($res){
                echo json_encode(['code'=>1,'msg'=>'修改成功']);
            }else{
                echo json_encode(['code'=>0,'msg'=>'修改失败']);
            }
        }else{
             echo json_encode(['code'=>0,'msg'=>'参数错误']);
        }
    }

    //删除
    public function ajax_delete_word(){
        if(input('id')){
            $userinfo = $this->_userinfo;
            $res = Db::name('offer_type')->where(['id'=>input('id')])->update(['status'=>9]);
            if($res){
                echo json_encode(['code'=>1,'msg'=>'删除成功']);
            }else{
                echo json_encode(['code'=>0,'msg'=>'删除失败']);
            }
        }else{
             echo json_encode(['code'=>0,'msg'=>'参数错误']);
        }
    }
}
