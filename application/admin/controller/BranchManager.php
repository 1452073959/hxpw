<?php


namespace app\admin\controller;


use app\common\controller\Adminbase;
use think\Db;
class BranchManager extends Adminbase
{

    public function index()
    {
        $login = $this->_userinfo;
        $admin=Db::table('fdz_admin')->where('companyid', $login['companyid'])->select();
        foreach ($admin as $k=>$v){
            $admin[$k]['auth']=Db::table('fdz_auth_group')->where('id', $v['roleid'])->value('title');
        }
        $this->assign('admin',$admin);
        return $this->fetch();
    }
}
