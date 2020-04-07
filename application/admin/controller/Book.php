<?php

// +----------------------------------------------------------------------
// | 统计报表
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;

class Book extends Adminbase{

    //把订单底部文字存起来
    public function test1(){
        $cost_tmp = Db::name('cost_tmp')->field('f_id,order_tfoot')->select();
        Db::startTrans();
        try {
            foreach ($cost_tmp as $k => $v) {
                Db::name('offerlist')->where(['frameid'=>$v['f_id']])->update(['o_remark'=>$v['order_tfoot']]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            echo 'no ok';die;
        }
        echo 'ok';
        

    }

    public function test(){
        echo '暂停使用';die;
        $frame = array_column(Db::name('frame')->select(),null, 'id');
        $offerlist = Db::name('offerlist')->select();
        $data = [];
        foreach($offerlist as $k=>$v){
            if(!isset($data[$frame[$v['frameid']]['name']])){
                $data[$frame[$v['frameid']]['name']] = 0;
            }
            $data[$frame[$v['frameid']]['name']]++;
            
        }
        dump($data);die;

        die;
        $data = [];
        $admin = Db::name('admin')->where(['status'=>1])->select();
        $auth_group = array_column(Db::name('auth_group')->select(),null, 'id');
        foreach($admin as $k=>$v){
            // echo $frame[$v['companyid']]['name'];
            if(!isset($data[$frame[$v['companyid']]['name']][$auth_group[$v['roleid']]['title']]['num'])){
                $data[$frame[$v['companyid']]['name']][$auth_group[$v['roleid']]['title']]['num'] = 1;
                $data[$frame[$v['companyid']]['name']][$auth_group[$v['roleid']]['title']]['is_login'] = 0;
                if($v['token'] || $v['last_login_time']){
                    $data[$frame[$v['companyid']]['name']][$auth_group[$v['roleid']]['title']]['is_login'] = 1;
                }
            }else{
                $data[$frame[$v['companyid']]['name']][$auth_group[$v['roleid']]['title']]['num']++;
                if($v['token'] || $v['last_login_time']){
                    $data[$frame[$v['companyid']]['name']][$auth_group[$v['roleid']]['title']]['is_login']++;
                }
            }
        }
        echo '<style>table{border:1px solid #ccc} td{border:1px solid #ccc;width:100px;text-align:center} </style>';
        echo '<table>';
        echo '<tr><th>分公司</th><th>分总经理</th><th>报价师</th><th>人事</th><th>财务</th><th>工程监理</th><th>仓管</th><th>质检</th><th>工程经理</th></th>';
        foreach($data as $k=>$v){
            echo '<tr>';
            echo '<td>'.$k.'</td>';
            if(isset($v['分总经理'])){
                echo '<td>'.$v['分总经理']['is_login'].'//'.$v['分总经理']['num'].'</td>';
            }else{
                echo '<td>0</td>';
            }
            if(isset($v['报价师'])){
                echo '<td>'.$v['报价师']['is_login'].'//'.$v['报价师']['num'].'</td>';
            }else{
                echo '<td>0</td>';
            }
            if(isset($v['人事'])){
                echo '<td>'.$v['人事']['is_login'].'//'.$v['人事']['num'].'</td>';
            }else{
                echo '<td>0</td>';
            }
            if(isset($v['财务'])){
                echo '<td>'.$v['财务']['is_login'].'//'.$v['财务']['num'].'</td>';
            }else{
                echo '<td>0</td>';
            }

            if(isset($v['工程监理'])){
                echo '<td>'.$v['工程监理']['is_login'].'//'.$v['工程监理']['num'].'</td>';
            }else{
                echo '<td>0</td>';
            }
            if(isset($v['仓管'])){
                echo '<td>'.$v['仓管']['is_login'].'//'.$v['仓管']['num'].'</td>';
            }else{
                echo '<td>0</td>';
            }
            if(isset($v['质检'])){
                echo '<td>'.$v['质检']['is_login'].'//'.$v['质检']['num'].'</td>';
            }else{
                echo '<td>0</td>';
            }
            if(isset($v['工程经理'])){
                echo '<td>'.$v['工程经理']['is_login'].'//'.$v['工程经理']['num'].'</td>';
            }else{
                echo '<td>0</td>';
            }
            
            echo '</tr>';
        }

        echo '</table>';

        dump($data);
    }
    public function index(){
        $data = Db::name('book')->order('sort','asc')->order('id','asc')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function add_book(){
        $data['name'] = input('name');
        $data['tag'] = input('tag');
        $id = input('id');
        if($id){
            $res = Db::name('book')->where(['id'=>$id])->update($data);
        }else{
            $res = Db::name('book')->insert($data);
        }
        if(!$res){
            $this->error('添加失败');
        }
        $this->success('添加成功');
    }

    //批量更新图片
    public function update_img(){
        $id = input('id');
        $tag = Db::name('book')->where(['id'=>$id])->value('tag');
        if(!$tag){
            $this->error('参数错误');
        }
        $path = './uploads/book';
        $myfile = scandir($path.'/'.$tag);
        $file = [];
        foreach ($myfile as $value){
            if($value != '.' && $value != '..'){
                $file[] = $value;
            }
        }
        if(empty($file)){
            $this->error('文件夹没有图片');
        }
        Db::startTrans();
        try {
            Db::name('book_img')->where(['bid'=>$id])->delete();
            foreach($file as $k=>$v){
                $info = [];
                $info['sort'] = 0;
                $info['bid'] = $id;
                $info['img'] = '/'.$tag.'/'.$v;
                Db::name('book_img')->insert($info);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('更新图片成功');
    }

    public function del_book(){
        $id = input('id');
        $res = Db::name('book')->where(['id'=>$id])->delete();
        if(!$res){
            $this->error('删除失败');
        }
        $this->success('删除成功');
    }

    //获取图片列表
    public function book_info(){
        
    }
}