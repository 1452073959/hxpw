<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
namespace app\applet\controller;
use think\Db;
use think\Controller;
use app\applet\controller\UserBase;
 
class User extends UserBase
{
    public function getAdminInfo(){
        $applet_menu = array_column(Db::name('applet_menu')->where(['status'=>1])->order('pid','asc')->order('sort','asc')->order('id','asc')->select(), null,'id') ;
        $menu = [];
        foreach($applet_menu as $k=>$v){
            if($v['auth']){
                $v['auth'] = explode(',', $v['auth']);
                if(!in_array($this->admininfo['roleid'],$v['auth'])){
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
        $this->json(0,'success',['admininfo'=>$this->admininfo,'menu'=>$menu]);
    }

}