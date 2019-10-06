<?php
// +----------------------------------------------------------------------
// | 首页
// +----------------------------------------------------------------------
namespace app\applet\controller;
use think\Db;
use think\Controller;
use app\applet\controller\Base;
 
class Index extends Base{
    public function login(){
        $username = input('username');
        $pwd = input('pwd');
        $admininfo = Db::name('admin')->where(['username'=>$username])->find();
        if($admininfo['password'] == md5($pwd)){
            $token = $this->setToken($admininfo['userid']);
            if($token){
                $applet_menu = array_column(Db::name('applet_menu')->where(['status'=>1])->order('pid','asc')->order('sort','asc')->order('id','asc')->select(), null,'id') ;
                $menu = [];
                foreach($applet_menu as $k=>$v){
                    if($v['auth']){
                        $v['auth'] = explode(',', $v['auth']);
                        if(!in_array($admininfo['roleid'],$v['auth'])){
                            continue;
                        }else{
                            unset($v['auth']);
                        }
                    }else{
                        continue;
                    }
                    
                    if($v['pid'] == 0){
                        $menu[$v['id']] = $v;
                    }else{
                        if(isset($menu[$v['pid']])){
                            $menu[$v['pid']]['child'][] = $v;
                        }
                    }
                }
                $auth_group = array_column(Db::name('auth_group')->select(),null, 'id');
                $admininfo['role_name'] = $auth_group[$admininfo['roleid']]['title'];
                $this->json(0,'登录成功',['token'=>$token,'admininfo'=>$admininfo,'menu'=>$menu]);
            }else{
                 $this->json(1,'获取token失败');
            }
            
        }else{
            $this->json(1,'账号/密码错误');
        }
    }
}