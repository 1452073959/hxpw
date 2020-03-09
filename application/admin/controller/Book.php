<?php

// +----------------------------------------------------------------------
// | 统计报表
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Db;
use think\Paginator;

class Book extends Adminbase{
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