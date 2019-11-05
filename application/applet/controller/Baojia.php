<?php

namespace app\applet\controller;

use app\applet\model\Userappler;
use think\Controller;
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
            'create_time' => date('y-m-d h:i:s', time())
        ];

        $res = Db::table('fdz_jiezhi')->data($data)->insert();
        if ($res) {
            return json(['code' => 1, 'msg' => '成功', 'data' => $data]);
        } else {
            return json(['code' => 2, 'msg' => '失败']);
        }
    }

    public function audit()
    {
        //获取未审核的订单
        $audit = Db::table('fdz_jiezhi')->where('sid', 0)->select();
        $user = Userappler::with('jiezhi')->all();
        $audit = Jiezhi::with(['offer', 'user'])->where('sid', 0)->all();
//      if($audit['jid'])
        if ($audit) {
            return json(['code' => 1, 'msg' => '成功', 'data' => $audit]);
        } else {
            return json(['code' => 2, 'msg' => '失败']);
        }

    }

    //审核订单
    public function update(Request $request, $id)
    {
        $data = $request->get();
        $user = Jiezhi::get($data['id']);
        $user->sid = $data['sid'];
        $user->status = $data['status'];
        $user->save();
        dump($user);
    }

    public function history($id)
    {
        $history=Jiezhi::where('jid',$id)->select();
        $this->json(0,'success',$history);
    }

}
