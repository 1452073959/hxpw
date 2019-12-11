<?php

// +----------------------------------------------------------------------
// | 统计报表
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\AdminUser;
use app\admin\model\PickingMaterial;
use app\admin\model\Userlist;
use app\common\controller\Adminbase;
use app\common\model\Demo;
use think\Db;
use think\Paginator;
use think\Request;

class Statistical extends Adminbase
{
    //业绩统计
    public function results_index()
    {
        return $this->fetch();
    }

    //领料统计
    public function picking_index()
    {
        $where = [];
        $condition = [];
        if($this->_userinfo['roleid'] != 1){
            $where[] = ['frameid','=',$this->_userinfo['companyid']];
        }
        if(input('username')){
            $where[] = ['customer_name','like','%'.input('username').'%'];
        }
        if(input('jlname')){
            $jid = Db::name('admin')->where('name','like','%'.input('jlname').'%')->select();
            if($jid){
                $where[] = ['jid','in',array_column($jid, 'userid')];
            }else{
                $where[] = ['jid','=',-1];
            }
        }
        if(input('begin_time') && input('end_time')){
            $condition = array(['sign_bill_time','>',strtotime(input('begin_time'))],['sign_bill_time','<',strtotime('+1 day',strtotime(input('end_time')))]);
        }
        
        $where[] = ['status','in',[3,4,5,6,7]];
        $datas = Db::name('userlist')->where($where)->where($condition)->paginate(15,false,['query'=>request()->param()])->each(function($item, $key){
            $item['all_material_money'] = Model('offerlist')->get_material_list($item['oid'],2)['total_money'];
            $item['status1'] = 0; //未审核辅材
            $item['status23'] = 0;//待领辅材
            $item['status4'] = 0;//已领辅材
            $item['type1'] = 0;//定点
            $item['type2'] = 0;//自购
            $picking_material = Db::name('picking_material')->where(['oid'=>$item['oid'],'status'=>[1,2,3,4]])->select();
            $picking_order = Db::name('picking_order')->where(['userid'=>$item['id']])->select();
            if($picking_order){
                foreach($picking_order as $k=>$v){
                    switch ($v['type']) {
                        case '1':
                            $item['type1'] += $v['money'];
                            break;
                        case '2':
                            $item['type2'] += $v['money'];
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
            if($picking_material){
                foreach($picking_material as $k=>$v){
                    switch ($v['status']) {
                        case '1':
                            $item['status1'] += $v['total_money'];
                            break;
                        case '2':
                            $item['status23'] += $v['total_money'];
                            break;
                        case '3':
                            $item['status23'] += $v['actual_total_money'];
                            break;
                        case '4':
                            $item['status4'] += $v['actual_total_money'];
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
            return $item;
        });
        $admins = array_column($datas->items(),'jid');
        // var_dump($datas->items());die;
        $admins = array_column(Db::name('admin')->where(['userid'=>$admins])->select(),null, 'userid') ;
        $this->assign('datas', $datas);
        $this->assign('admins', $admins);
        return $this->fetch();
    }

    //实际领料详情 公司仓库
    public function actual_picking_ck(){
        $where = [];
        $where['userid'] = input('uid');
        $userinfo = Db::name('userlist')->where(['id'=>input('uid')])->find();
        //仓库领料
        $datas = Db::name('picking_material')->where($where)->order('id','asc')->paginate(15,false,['query'=>request()->param()])->each(function($item, $key){
            $item['addtime'] = date('Y-m-d',strtotime($item['addtime']));
            // $item['info'] = Db::name('picking_material_info')->where(['pmid'=>$item['id']])->order('id','asc')->select();
            // foreach($item['info'] as $k1=>$v1){
            //     $item['info'][$k1]['total'] = round($v1['price']*$v1['num'],2);
            // }
            return $item;
        });
        $admins = array_column($datas->items(),'adminid');
        $admins = array_column(Db::name('admin')->where(['userid'=>$admins])->select(),null, 'userid') ;
        $this->assign('datas', $datas);
        $this->assign('admins', $admins);
        $this->assign('userinfo', $userinfo);
        return $this->fetch();
    }

    //实际领料详情 定点,自购
    public function actual_picking_dz(){
        $where = [];
        $where['userid'] = input('uid');
        $userinfo = Db::name('userlist')->where(['id'=>input('uid')])->find();
        //定点,自购
        $datas = Db::name('picking_order')->where($where)->order('id','asc')->paginate(15,false,['query'=>request()->param()])->each(function($item, $key){
            $img = Db::name('picking_order_img')->where(['poid'=>$item['id']])->order('id','desc')->select();
            foreach($img as $k1=>$v2){
                $item['img'][] = $this->getImgSrc($v2['img']);
            }
            return $item;
        }); 
        $admins = array_column($datas->items(),'adminid');
        $admins = array_column(Db::name('admin')->where(['userid'=>$admins])->select(),null, 'userid') ;
        $this->assign('datas', $datas);
        $this->assign('userinfo', $userinfo);
        $this->assign('admins', $admins);
        return $this->fetch();
    }
    //自购/订单领料图片
    public function buying(Request $request)
    {
        $data=$request->get();
        $buying = Db::table('fdz_picking_order_img')->where('poid',$data['uid'])->find();
        $buyingimg=$buying['img'];
        return response( "<img src='$buyingimg'>");
    }

//    合计
    public function total(Request $request)
    {
        $data=$request->get();
        $userinfo = Userlist::with(['user','gcjl','zj'])->where('id',$data['uid'])->find();
        $buying=Db::table('fdz_picking_order')->where('userid',$data['uid'])->select();
        $summation=0;
        foreach ($buying as $key=>$value)
        {
            $summation+=$value['money'];
        }
        $history=PickingMaterial::where('userid',$data['uid'])->where('status',4)->select();
        $reality=[];
        foreach ($history as $k=>$v)
        {
            $history[$k]['details']=Db::table('fdz_picking_material_info')->where('pmid',$v['id'])->select();
            foreach ($history[$k]['details'] as $k1=>$v1){
                $reality[]=$v1;
            }
        }
        $amount=0;
        foreach ($reality as $k1=>$v1){
            $amount+=$v1['price']*$v1['actual_num'];
        }
//        dump($userinfo);
        $this->assign('userinfo',$userinfo);
        $this->assign('reality',$reality);
        $this->assign('summation',$summation);
        $this->assign('amount',$amount);
        return $this->fetch();
    }
    //工程派单
    public function send_order_index()
    {
        return $this->fetch();
    }

    //监理管理
    public function supervision_index()
    {
        $user = $this->_userinfo;
        $user=AdminUser::with('Supervision')->where('roleid',13)->where('companyid',$user['companyid'])->select();
        $this->assign('user',$user);
        return $this->fetch('supervision_index1');
    }
    //监理工地
    public function construction(Request $request)
    {
        $construction=$request->get();
        $construction=Userlist::where('jid',$construction['id'])->select();
        $this->assign('construction',$construction);
        return $this->fetch();
    }

    //在施工地
    public function in_word()
    {

        $where = [];
        if (!empty($_GET['customer_name'])) {
            $where[] = ['customer_name', 'like', "%{$_GET['customer_name']}%"];
        }
        if (!empty($_GET['jid'])) {
            $where[] = ['jid', 'in', "{$_GET['jid']}"];
        }

        $order = Userlist::with('profile', 'user', 'picking')->where($where)->where('status','>=',3)->where('oid','>','0')->paginate(10,false,['query'=>request()->param()]);
        foreach ($order as $k => $v) {
            $order[$k]['total_picking'] = 0;
            if (!empty($v['picking'])) {
                foreach ($v['picking'] as $k1 => $v1) {
                    $order[$k]['total_picking'] += $v1['actual_total_money'];
                }
            }
        }


        foreach ($order as $k2 => $v2) {

            if (!$v2['profile']) {
                continue;
            }
            $order[$k2]['order_info'] = model('offerlist')->get_order_info($v2['profile']['id'], 2);
        }
        $user = Db::table('fdz_admin')->where('roleid', '13')->select();
        $this->assign('order', $order);
        $this->assign('users', $user);

        return $this->fetch();
    }
    //领料记录
    public function history(Request $request)
    {
        $data=$request->get();
        $history=PickingMaterial::with(['user','client','user1'])->where('userid',$data['uid'])->select();
        $this->assign('history',$history);

      return  $this->fetch();
    }
    //领料详情

    public function particulars(Request $request)
    {
        $data=$request->get();
        $particulars=Db::table('fdz_picking_material_info')->where('pmid',$data['pmid'])->select();
         $this->assign('particulars',$particulars);
        return $this->fetch();
    }





    public function change_array($array)
    {
        foreach ($array as $k => $v) {
            //若$v仍为数组 则调用自身
            if (is_array($v)) {
                $this->change_array($v);
            } else {
                $this->arr[] = $v;
            }
        }
        return $this->arr;

    }

    //模拟返回数据
    public function return_data()
    {
        $data = [
            [
                "td1" => "商务经理",
                "td2" => "张三",
                "td3" => "101900",
                "td4" => "51%",
                "td5" => "1250",
                "td6" => "8",
                "td7" => "2",
                "td8" => "6",
                "td9" => "0",
                "td10" => "0",
                "td11" => "0",
            ],
            [
                "td1" => "报价师",
                "td2" => "李四",
                "td3" => "56900",
                "td4" => "49%",
                "td5" => "850",
                "td6" => "7",
                "td7" => "2",
                "td8" => "2",
                "td9" => "1",
                "td10" => "2",
                "td11" => "0",
            ],
            [
                "td1" => "商务经理",
                "td2" => "小明",
                "td3" => "12900",
                "td4" => "45%",
                "td5" => "850",
                "td6" => "3",
                "td7" => "0",
                "td8" => "1",
                "td9" => "0",
                "td10" => "2",
                "td11" => "0",
            ],
            [
                "td1" => "商务经理",
                "td2" => "黄五",
                "td3" => "99800",
                "td4" => "47%",
                "td5" => "1150",
                "td6" => "11",
                "td7" => "2",
                "td8" => "3",
                "td9" => "1",
                "td10" => "4",
                "td11" => "2",
            ],
            [
                "td1" => "合计",
                "td2" => "",
                "td3" => "271500",
                "td4" => "48%",
                "td5" => "1025",
                "td6" => "29",
                "td7" => "6",
                "td8" => "12",
                "td9" => "3",
                "td10" => "8",
                "td11" => "2",
            ]
        ];
        echo json_encode(array('code' => 0, 'count' => count($data), 'data' => $data, 'msg' => 'ok'));
    }

    public function return_department_data()
    {
        echo '{"code":0,"msg":"ok","data":[{"id":0,"name":"\u5e7f\u5dde\u5206\u516c\u53f8","other":"\u5206\u516c\u53f8","levelid":3,"pid":-1,"status":0},{"id":1,"fid":152,"name":"\u8425\u4e1a\u90e8","pid":0,"info_pid":"0","sort":0,"remark":"\u8425\u4e1a\u90e8","addtime":"1567051211"},{"id":15,"fid":152,"name":"\u8bbe\u8ba1\u4e8c\u90e8","pid":3,"info_pid":"0-3","sort":0,"remark":"","addtime":"1567238498"},{"id":3,"fid":152,"name":"\u8bbe\u8ba1\u90e8","pid":0,"info_pid":"0","sort":0,"remark":"\u8bbe\u8ba1\u90e8","addtime":"1567051255"},{"id":5,"fid":152,"name":"\u8bbe\u8ba1\u4e00\u90e8","pid":3,"info_pid":"0-3","sort":0,"remark":"","addtime":"1567051357"},{"id":6,"fid":152,"name":"\u5e02\u573a\u90e8","pid":1,"info_pid":"0-1","sort":0,"remark":"","addtime":"1567051561"},{"id":7,"fid":152,"name":"\u5de5\u7a0b\u90e8","pid":0,"info_pid":"0","sort":0,"remark":"","addtime":"1567070315"},{"id":8,"fid":152,"name":"\u5de5\u7a0b\u4e00\u90e8","pid":7,"info_pid":"0-7","sort":0,"remark":"","addtime":"1567070339"},{"id":10,"fid":152,"name":"\u4ed3\u5e93","pid":8,"info_pid":"0-7-8","sort":0,"remark":"","addtime":"1567130232"},{"id":11,"fid":152,"name":"\u65bd\u5de5","pid":8,"info_pid":"0-7-8","sort":0,"remark":"","addtime":"1567130313"},{"id":12,"fid":152,"name":"\u884c\u653f\u90e8","pid":0,"info_pid":"0","sort":0,"remark":"","addtime":"1567147708"},{"id":13,"fid":152,"name":"\u4eba\u529b\u90e8","pid":12,"info_pid":"0-12","sort":0,"remark":"","addtime":"1567147718"},{"id":14,"fid":152,"name":"\u4f1a\u8ba1","pid":12,"info_pid":"0-12","sort":0,"remark":"","addtime":"1567152792"}],"count":13}';
    }

    //获取图片完整路径 并判断图片是否存在 不存在找到替换
    public function getImgSrc($src,$path="uploads/images/",$http='http://'){

        if(file_exists($path.$src) && $src != '/' && !empty($src)){
            $src = str_replace('\\','/',$src);
            return $http.$_SERVER['HTTP_HOST'].'/'.$path.$src;
        }else{
            return $http.$_SERVER['HTTP_HOST']."/static/imgs/logo1.png";
        }
    }
}