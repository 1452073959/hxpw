<?php

namespace app\admin\controller;

use app\admin\model\AdminUser;
use app\common\controller\Adminbase;
use think\Db;

/**
 * 管理员管理
 */
class Manager extends Adminbase
{
    // protected function initialize()
    // {
    //     parent::initialize();
    //     $this->AdminUser = new AdminUser;
    // }

    /**
     * 管理员管理列表
     */
    public function index()
    {

          if($this->request->isPost()){
             //模糊搜索
                $search = input('search'); 
                $len = preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$search);
                if($len){
                    //查询分公司返公司id
                  $frame = Db::name('frame')->where(array('name'=>$search))->find();
                  if($frame){
                      $re = Db::name('admin')->where(array('companyid'=>$frame['id']))->order(array('userid' => 'ASC'))->select();
                      $this->assign('Userlist',$re); 
                      return $this->fetch();
                  }else{
                      return $this->error('无该公司信息', url("Manager/index"));
                  }
                }
               if($search){          
                 $User = Db::name('admin')->where('username|phone','like',"%".$search."%")->order(array('userid' => 'ASC'))->select();
                 if ($User) {
                      $this->assign('Userlist',$User); 
                 }else{
                     return $this->error('数据查询失败', url("Manager/index"));
                 }
                 return $this->fetch();       
                }else{
                 $this->error('请输入搜索内容', url("Manager/index"));
                } 
         
          }else{
            $User = Db::name("admin")->order(array('userid' => 'ASC'))->select();
            $company = Db::name('frame')->field('id,name')->where(array('levelid'=>3))->select();
            $this->assign("Userlist", $User);
            $this->assign("company", $company);
            return $this->fetch();
          }  
    }

    // 分公司选择
        public function myquery(){

          if (request()->isAjax()){
             $User = Db::name("admin")->where(array('companyid'=>input('value')))->order(array('userid' => 'ASC'))->select();
             if($User){
              $newdata = array();
             //把id转换文字
             foreach ($User as $k => $v) {
                 $newdata[$k] = $v;
                 $newdata[$k]['companyid'] = getcid($v['companyid']);
                 $newdata[$k]['roleid'] = model('admin/AuthGroup')->getRoleIdName($v['roleid']);

             }
                  // dump($newdata);
              Result(0,'查询成功',$newdata);
             }else{
                Result(1,'查询不到数据');
             }
          }else{
                Result(1,'请求失败');
          }

       }
    /**
     * 添加管理员
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post('');
            // dump($data);exit;
            $result = $this->validate($data, 'AdminUser.insert');

            if ($result !== true) {
                return $this->error($result);
            }
            if (!$data['hidecid']) {
                return $this->error('请选择所属公司');
            }
            unset($data['password_confirm']);
            unset($data['nickname']);
            $data['roleid'] = $data['roleid'];
            $data['companyid'] = $data['hidecid'];
            $data['password'] = md5($data['password']);
            unset($data['hidecid']);
            // $data = array_values($data);
            // $appinfo['username'] = $data['username'];
            // $appinfo['password'] = $data['password'];
            // $appinfo['email'] = $data['email'];
            // $appinfo['nickname'] = $data['nickname'];
            // $appinfo['phone'] = $data['phone'];
            // $appinfo['roleid'] = $data['roleid'];
            // dump($data);exit;

            if ($res = Db::name('admin')->insert($data)) {
                $this->success("添加管理员成功！", url('admin/manager/index'));
            } else {
                // $error = Db::name('admin')->getError();
                $this->error('添加失败！');
            }

        } else {
            // $company = Db::name('frame')->field('id,name')->where(array('levelid'=>3))->select();
               
            // $this->assign("company",$company);
            $this->assign("roles", model('admin/AuthGroup')->getGroups());

            return $this->fetch();
        }
    }
     /**
     * 选择公司
     */
    public function ajaxquery(){
          if ($this->request->isPost()){
              $sres = Db::name('frame')->where(array('levelid'=>3))->where('name','like',"%".input('value')."%")->select();
              if($sres){
                 Result(0,'查询成功',$sres);
              }else{
                 Result(1,'没有查询到该公司');
              }
          }
    } 

    /**
     * 管理员编辑
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post('');
                  // dump($data);exit;

            $result = $this->validate($data, 'AdminUser.update');
            if ($result !== true) {
                return $this->error($result);
            }
            $data['password'] = md5($data['password']);
            unset($data['password_confirm']);
            unset($data['nickname']);
            // dump($data);exit;
            if (Db::name('admin')->update($data)) {
                $this->success("修改成功！");
            } else {
                $this->error(Db::name('admin')->getError() ?: '修改失败！');
            }
        }else{
            $id = $this->request->param('id/d');
            $data = Db::name('admin')->where(array("userid" => $id))->find();
            if (empty($data)) {
                $this->error('该信息不存在！');
            }
            $this->assign("data", $data);
            $this->assign("roles", model('admin/AuthGroup')->getGroups());
            return $this->fetch();
        }
    }

     /**
     * 管理员禁用启用
     */
    public function bankai()
    {
        $allurl = input('param.');
        // dump($allurl);
        if($allurl['status']==1){
          $data['status'] = 0;
            if (Db::name('admin')->where('userid',$allurl['id'])->update($data)){
                $this->success("禁用成功！");
            } else {
                $this->error('操作失败！');
            }           
          
        }else{
            // $data['status'] = 1;
            $this->error('已经禁用！');
        }
        
    }

    /**
     * 管理员删除
     */
    public function del()
    {
        $id = $this->request->param('id/d');
        if (Db::name('admin')->where('userid',$id)->delete()){
            $this->success("删除成功！");
        } else {
            $this->error(Db::name('admin')->getError() ?: '删除失败！');
        }
    }

}
