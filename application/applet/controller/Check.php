<?php
// +----------------------------------------------------------------------
// | 质检
// +----------------------------------------------------------------------
namespace app\applet\controller;
use think\Db;
use think\Controller;
use app\applet\controller\UserBase;
 
class Check extends UserBase{

    //质检 客户列表
    public function userlist(){
        $where = [];
        if($this->admininfo['roleid'] != 1 && $this->admininfo['roleid'] != 17){
            $where['check_id'] = $this->admininfo['userid'];
        }
        $where['frameid'] = $this->admininfo['companyid'];
        $where['in_check'] = 1;
        $userlist = array_column(Db::name('userlist')->where($where)->order('sign_bill_time','asc')->select(),null, 'id') ;
        foreach($userlist as $k=>$v){
            $userlist[$k]['sign_bill_time'] = date('Y-m-d');
        }
        $this->json(0,'success',$userlist);
    }

    //申请验收
    public function applyCheck(){
        $uid = input('uid');
        $userinfo = Db::name('userlist')->where(['id'=>$uid])->find();
        if ($userinfo['status'] > '6') {
            $this->json(2,'请耐心等待结算结果');
        }
        if ($userinfo['work_status'] == '待结算') {
            $this->json(2,'工程已结束，可发起结算申请');
        }
        $in_check = $userinfo['in_check'];
        if($in_check == 1){
            $this->json(2,'请耐心等待验收');
        }
        $res = Db::name('userlist')->where(['id'=>$uid])->update(['in_check'=>1,'check_time'=>time()]);
        if($res){
            $this->json(0,'success');
        }else{
            $this->json(2,'申请失败');
        }
    }

    //获取质检流程
    public function getCheckProcess(){
        $datas = [];
        $uid = input('uid');
        $userinfo = Db::name('userlist')->where(['id'=>$uid])->find(); //用户详情
        $cost_tmp = Db::name('cost_tmp')->where(['f_id'=>$userinfo['frameid']])->find();
        if(!$cost_tmp || !$cost_tmp['order_check']){
            //未设置质检流程
            $this->json(2,'none');
        }
        $cost_tmp['order_check'] = json_decode($cost_tmp['order_check'],true);
        foreach ($cost_tmp['order_check'] as $k => $v) {
            if(empty($v[1])){
                $cost_tmp['order_check'][$k][0] = $v[0];
            }else{
                $cost_tmp['order_check'][$k] = $v;
            }
        }
        $datas = $cost_tmp['order_check'];
        $this->json(0,'success',$datas);
    }

    //上传图片
    public function uploadimg(){
        $file = request()->file('file');
        if($file && input('uid')){
            // 10485760 = 10M
            if($file->getInfo()['size'] > 10485760){
                $this->json(2,'图片大小不得超过10M');
            }
            $info = $file->move( './uploads/images');
            if($info){
                // 成功上传后 获取上传信息
                // $info->getSaveName() 储存路径
                $this->json(0,'success',['num'=>input('num'),'img'=>$info->getSaveName()]);
            }else{
                // 上传失败获取错误信息
                $this->json(2,'图片上传失败!');
            }
        }else{
            $this->json(2,'图片上传失败');
        }
    }

    //验收列表 - 某客户的
    public function getCheckByUser(){
        $uid = input('uid');
        $userinfo = Db::name('userlist')->where(['id'=>input('uid')])->find(); //用户详情
        
        $list = Db::name('check')->where(['userid'=>$uid])->select();
        if($userinfo['in_check'] == 1 && $userinfo['work_status'] != '待验收'){
            //待验收状态
            $check = [];
            $check['userid'] = input('uid');
            $check['id'] = 0;
            $check['check_name'] = $userinfo['work_status'];
            $check['time'] = date('Y-m-d',$userinfo['check_time']);
            $check['status'] = 999;//待验收
            $order_check = Db::name('cost_tmp')->where(['f_id'=>$userinfo['frameid']])->value('order_check');
            $order_check = json_decode($order_check,true);
            if(is_array($order_check)){
                $order_check = array_column($order_check, null,0);
            }
            $check['check_content'] = $order_check[$userinfo['work_status']][1];
            $list[] = $check;
        }
        foreach($list as $k=>$v){
            $list[$k]['time'] = date('Y-m-d',strtotime($v['time']));
        }
        if($list){
            $this->json(0,'success',$list);
        }else{
            $this->json(2,'暂无验收记录');
        }
    }

    //验收详情 某一条
    public function getCheckById(){
        $id = input('id');
        $info = Db::name('check')->where(['id'=>$id])->find();
        if(!$info){
            $this->json(2,'参数错误');
        }
        $imgs = Db::name('check_img')->where(['cid'=>$id])->select();
        if($imgs){
            foreach($imgs as $k=>$v){
                $info['img'][] = $this->getImgSrc($v['img']);
            }
        }
        $this->json(0,'success',$info);
    }

    //验收操作
    public function comfirmCheck(){
        // var_dump(input());
        $data = [];
        $data['status'] = input('switch')?1:0;
        $data['remark'] = input('remark');
        $data['userid'] = input('uid');
        $userinfo = Db::name('userlist')->where(['id'=>input('uid')])->find(); //用户详情
        $data['frameid'] = $userinfo['frameid'];
        $data['adminid'] = $this->admininfo['userid'];

        $work_status = $userinfo['work_status'];
        //质检流程 json数组
        $order_check = Db::name('cost_tmp')->where(['f_id'=>$userinfo['frameid']])->value('order_check');
        $order_check = json_decode($order_check,true);
        if(is_array($order_check)){
            $order_check = array_column($order_check, null,0);
        }else{
            $this->json(2,'验收流程有误，请联系管理员');
        }
        if(isset($order_check[$userinfo['work_status']])){
            $data['check_name'] = $userinfo['work_status'];
            $data['check_content'] = $order_check[$userinfo['work_status']][1];
        }else{
            $data['check_name'] = $userinfo['work_status'];
            $this->json(2,'验收流程发生改变，请联系管理员');
        }

        // 获取下一个流程
        if($data['status']){
            //获取需要施工的所有工种
            $oid = $userinfo['oid'];
            $type_of_work = Db::name('order_project')->where(['o_id'=>$oid])->field('type_of_work')->group('type_of_work')->select();
            $type_of_work = array_unique(array_column($type_of_work,'type_of_work'));
            //只取前面2个字
            foreach($type_of_work as $k=>$v){
                $type_of_work[$k] = mb_substr($v, 0, 2);
            }
            $next_check = '待结算';
            $is = true;
            foreach($order_check as $k=>$v){
                if($k == $userinfo['work_status']){
                    $is = false;//前面的流程不要
                    continue;
                }
                if($is){
                    continue;
                }
                if(in_array($k, $type_of_work)){
                    $next_check = $k;
                    break;
                }

            }
        }
        //按顺序找到下一个流程 , 后面流程改成工种, 所有定义的流程(工种)可能项目没有 所以重写 , (如果以后改成按流程不按工种, 这个方法又可以用了)
        //下面 Db::name('userlist')->where(['id'=>input('uid')])->update(['work_status'=>$next_check[0],'in_check'=>0]);   $next_check 是数组 取下标0
        // if($data['status']){
        //     //验收通过 获取下一个流程
        //     $next_check = '';
        //     foreach($order_check as $k=>$v){
        //         if($k == $userinfo['work_status']){
        //             $next_check = next($order_check);
        //             if(!$next_check){
        //                 $next_check = '待结算';
        //             }
        //         }else{
        //             next($order_check);
        //         }
        //     }
        // }
        
        $img = [];
        Db::startTrans();
        try {
            //保存验收记录
            $cid = Db::name('check')->insertGetId($data);
            //保存图片
            if(input('img')){
                foreach(input('img') as $k=>$v){
                    $info = [];
                    $info['img'] = $v;
                    $info['cid'] = $cid;
                    $img[] = $info;
                }
                Db::name('check_img')->insertAll($img);
            }
            //修改工地信息
            if($data['status']){
                //验收通过
                if($next_check == '待结算'){
                    Db::name('userlist')->where(['id'=>input('uid')])->update(['work_status'=>'待结算','in_check'=>0,'status'=>7]);
                }else{
                    Db::name('userlist')->where(['id'=>input('uid')])->update(['work_status'=>$next_check,'in_check'=>0]);
                }
                
            }else{
                Db::name('userlist')->where(['id'=>input('uid')])->update(['in_check'=>2]);
                //验收不通过
            }
            // 提交事务
            Db::commit();
            $this->json(0,'验收成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->json(0,'验收失败');
        }
    }
}