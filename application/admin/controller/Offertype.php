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
    //首页
    public function index()
    {
        $res = Db::name('offer_type')->select();
		$tree = [];//树状数据
		foreach($res as $key =>$value){
			if($value['pid'] === 0){
				$tree[$key] = $value;
				unset($res[$key]);
				foreach($res as $k=>$v){
					if($v['pid'] == $value['id']){
						$tree[$key]['son'][] = $v;
					}
				}
			}
		}
		// dump($tree);
        $this->assign('data',$tree);   
        return $this->fetch();
    }

    //列表单字段修改
    public function singlefield_edit()
    {
        if ($this->request->isPost()) {
            $receive = $this->request->param();
            $data[$receive['field']] = $receive['value'];
            if(Db::name('offer_type')->where('id', $receive['id'])->update($data)){
                 Result(0,'单字段编辑成功'); 
            }else{
                Result(1,'编辑失败了！'); 
            }
        } else {
           Result(1,'获取字段信息失败'); 
        }

    }


    //批量添加空间类型
    public function addroom()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param(); 
            $time = time();
            //获取全部工种pid=0
            $gz = Db::name('offer_type')->field('id')->where('pid','0')->group('name')->select();
            if($gz){
                foreach($data['name'] as $k=>$v){
                    $insert = [];
                    foreach($gz as $k1=>$v1){
                        $insert[$k1]['name'] = $v ?: '';
                        $insert[$k1]['other'] = $data['other'][$k] ?: '';
                        $insert[$k1]['pid'] = $v1['id'];
                        $insert[$k1]['addtime'] = $time;
                    }
                    Db::name('offer_type')->insertAll($insert);
                }
            }else{
                $this->error('请先添加工种');
            }
            $this->success('添加成功',url("Offertype/index"));
        }else{
            return $this->fetch();
        }
    }
	//添加单个空间类型
	public function addoneroom(){
        if ($this->request->isPost()) {
            $data = $this->request->param(); 
                $insert['name'] = $data['name'] ?: '';
                $insert['other'] = $data['other'] ?: '';
                $insert['pid'] = $data['pid'];
                $insert['addtime'] = time();
                $re = Db::name('offer_type')->insertGetId($insert);
                $rooms = Db::name('offer_type')->where('pid','=',$data['pid'])->select();//改空间类型的其他数据
            if($re){
                $this->success('添加成功',url("Offertype/index"),['id'=>$re,'res'=>$rooms]);
            }else{
                $this->error('添加失败');
            }
        }
    }
	//添加工种
	public function addconditions()
	{
	    if ($this->request->isPost()) {
	        $data = $this->request->param();
            $time = time();

            Db::startTrans();
            try{
                foreach($data['addname'] as $k=>$v){
                    $insert = [];
                    $insert['name'] = $v ?: '';
                    $insert['other'] = $data['addother'][$k] ?: '';
                    $insert['addtime'] = $time;
                    $insert['pid'] = 0;
                    $re = Db::name('offer_type')->insertGetId($insert);
                    //添加单个成功  获取其他所有空间类型
                    $kongjian = Db::name('offer_type')->field('name,other')->where('pid','>','0')->group('name')->select();
                    if($kongjian){
                        foreach($kongjian as $k=>$v){
                            $kongjian[$k]['pid'] = $re;
                            $kongjian[$k]['addtime'] = $time;
                        }
                        Db::name('offer_type')->insertAll($kongjian);
                    }
                }
                Db::commit();
            }catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error('添加失败');
            }
            $this->success('添加成功',url("Offertype/index"));
	    }else{
	        return $this->fetch();
	    }
	}
	//修改工种
	public function editconditions()
	{
	    if ($this->request->isPost()) {
	        $data = $this->request->param();     
	        foreach($data['editname'] as $k=>$v){
	            $insert['name'] = $v ?: '';
	            $insert['other'] = $data['editother'][$k] ?: '';
	            $re = Db::name('offer_type')->update($insert);
	        }
	        if($re){
	            $this->success('添加成功',url("Offertype/index"));
	        }else{
	            $this->error('添加失败');
	        }
	    }else{
	        return $this->fetch();
	    }
	}
	
    //编辑
    public function edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

           $re = Db::name('offer_type')->where('id','=',$data['id'])->update( ['name'=>$data['name'],'other'=>$data['other']] );
 
            if ($re) {
                $this->success("编辑成功！", url("Offertype/index"));
            } else {
                $this->error('编辑失败！');
            }
        } else {
            $request = request();
            $id = $request->param('id');
            $rs = Db::name('offer_type')->where(["id" => $id])->find();     
            $this->assign("data", $rs);
            return $this->fetch();
        }

    }

    /**
     * 删除
     */
    public function delete()
    {
        $id = input('id');
        if (empty($id)) {
            $this->error('ID错误');
        }
        $result = Db::name('offer_type')->where(["pid" => $id])->select();
        if ($result) {
			foreach($result as $key=>$value){
				Db::name("offer_type")->delete($value['id']);
			}
        }
		$re = Db::name("offer_type")->delete($id);
        if ($re) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

}
