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
    //签到信息
    public function sign()
    {
        $userinfo = $this->_userinfo;
        $data=Db::table('fdz_userlist')->where('frameid',$userinfo['companyid'])->select();
        $where=[];
        if(input('name')){
            $where[] = ['name','LIKE','%'.input('name').'%'];
        }
        if(input('addres')){
            $where[] = ['addres','LIKE','%'.input('addres').'%'];
        }
        foreach ($data as $k=>$v)
        {
            $data[$k]['user']=Db::table('fdz_register')->where($where)->where('uid',$v['id'])->select();
        }
        foreach ($data as $k1=>$v1)
        {
            if(count($v1['user'])==0){
                unset($data[$k1]);
            }else{
                $data[$k1]['count']=count($v1['user']);
            }
        }
        $this->assign('data',$data);
        return $this->fetch();
    }
    //
    public function man()
    {

        $data=input();
        if(!empty($_GET['date'])){
            $data=Db::table('fdz_register')->where('uid',$data['id'])->whereBetweenTime('create_time',$data['date'])->select();
        }else{
            $data=Db::table('fdz_register')->where('uid',$data['id'])->select();
        }

        foreach ($data as $k=>$v)
        {
            $data[$k]['type']=Db::name('basis_type_work')->where('id',$v['type'])->value('name');
        }
        $this->assign('data',$data);
        return $this->fetch();
    }
    //
    public function signtwo()
    {
        $where=[];
        if(input('name')){
            $where[] = ['name','LIKE','%'.input('name').'%'];
        }
        $data=Db::table('fdz_register')->where($where)->select();

        foreach ($data as $k=>$v)
        {
            $data[$k]['user']=Db::table('fdz_userlist')->where('id',$v['uid'])->find();
            $data[$k]['type']=Db::name('basis_type_work')->where('id',$v['type'])->value('name');
        }
        //本公司
        $userinfo = $this->_userinfo;
        foreach ($data as $k1=>$v1)
        {
           if($v1['user']['frameid']!=$userinfo['companyid'])
           {
               unset($data[$k1]);
           }
        }
//        dump($data);
        $this->assign('data',$data);
        return $this->fetch();
    }
}
