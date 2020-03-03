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

    public function log()
    {
        $log= Db::view('zlogs')
            ->view('admin', 'userid,username', 'admin.userid=zlogs.operator')->order('operate_time','desc')
            ->select();
        foreach ($log as $k=>$v){
            $log[$k]['cname']=Db::table('fdz_admin')->where('userid', $v['cname'])->value('username');
        }
        $this->assign('log',$log);
        return $this->fetch();
    }
}
