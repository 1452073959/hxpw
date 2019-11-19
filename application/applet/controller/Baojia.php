<?php

namespace app\applet\controller;

use app\admin\model\Userlist;
use app\applet\model\Userappler;
use think\Controller;
use think\db\Where;
use think\Request;
use Db;
use app\applet\model\Jiezhi;

class Baojia extends UserBase
{
//提交借支订单
    public function index(Request $request)
    {
        //接收


        $data = $request->post();
        $data = ['money' => $data['money'],
            'shroff' => $data['shroff'],
            'so' => $data['so'],
            'uid' => $data['uid'],
            'jid' => $data['jid'],
            'frameid' => $data['frameid'],
            'status' => 1,
            'create_time' => date('y-m-d h:i:s', time())
        ];

        $res = Db::table('fdz_jiezhi')->data($data)->insert();
        if ($res) {
            return json(['code' => 1, 'msg' => '成功', 'data' => $data]);
        } else {
            return json(['code' => 2, 'msg' => '失败']);
        }
    }

    public function audit(Request $request)
    {
        //获取未审核的订单
        $data=$request->get();
        $audit = Db::table('fdz_jiezhi')->where('sid', 0)->select();
        $user = Userappler::with('jiezhi')->all();
        $audit = Jiezhi::with(['offer', 'user'])->where('sid', 0)->where('frameid',$data['freamid'])->select();

        foreach ($audit as $k=>$v){
            $audit[$k]['ys']=0;
            foreach ($money=Db::table('fdz_financial')->where('userid',$v['uid'])->select() as $k1=>$v1){
                $audit[$k]['ys'] += $v1['money'];
            }
            $audit[$k]['yj']=0;
            foreach ($jiezhi=Jiezhi::where('uid',$v['uid'])->select() as $k2=>$v2){
                $audit[$k]['yj'] += $v2['net_payroll'];
            }
        }

//      if($audit['jid'])
        if ($audit) {
            return json(['code' => 1, 'msg' => '成功', 'data' => $audit]);
        } else {
            return json(['code' => 2, 'msg' => '失败']);
        }

    }

    public function noaudit()
    {

    }

    //审核订单
    public function update(Request $request, $id)
    {
        $data = $request->post();

        $user = Jiezhi::where('id',$data['id'])->find();
        $user->sid = $data['sid'];
        $user->status = $data['status'];
        if($data['id']==4){
            $user->cause = $data['cause'];
        }
        $user->gcjltime= date('y-m-d h:i:s', time());
        $res=$user->save();
        if($res){
            $this->json(1,'success',$res);
        }

    }

    public function user(Request $request)
    {
        $data = $request->get();
//        $history=Jiezhi::where('jid',$id)->select();
        $user=Userlist::where('id',$data['uid'])->find();
        $this->json(1,'success',$user);
    }

   public function history(Request $request)
   {
       $data = $request->get();
       $history=Jiezhi::with(['offer','cw','gcjl'])->where('uid',$data['uid'])->select();
       $this->json(1,'success',$history);
   }

   public function money(Request $request)
   {
//       echo 123;
       $data = $request->get();
       $money=Db::table('fdz_financial')->where('userid',$data['uid'])->select();
        $ys=0;
       foreach ($money as $k=>$v)
       {
           $ys += $v['money'];
       }
//     已收款

       //已借
       $jiezhi=Jiezhi::where('uid',$data['uid'])->select();
       $yj=0;
       foreach ($jiezhi as $k2=>$v2)
       {
           $yj+=$v2['net_payroll'];
       }
       $n['ys']=$ys;
       $n['yj']=$yj;
     $this->json(1,'success',$n);
   }

}
