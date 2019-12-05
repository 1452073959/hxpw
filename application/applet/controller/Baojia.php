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
            'create_time' => date('y-m-d H:i:s', time())
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
        $data = $request->get();
        $audit = Db::table('fdz_jiezhi')->where('sid', 0)->select();
        $user = Userappler::with('jiezhi')->all();
        $audit = Jiezhi::with(['offer', 'user'])->where('sid', 0)->where('frameid', $data['freamid'])->select();

        foreach ($audit as $k => $v) {
            $audit[$k]['ys'] = 0;
            foreach ($money = Db::table('fdz_financial')->where('userid', $v['uid'])->select() as $k1 => $v1) {
                $audit[$k]['ys'] += $v1['money'];
                $audit[$k]['borrower'] = Db::table('fdz_financial')->where('userid', $v['uid'])->find();
                $audit[$k]['borrower'] = Db::table('fdz_financial')->where('userid', $v['uid'])->find();
                $audit[$k]['borrower'] = Db::table('fdz_cost_tmp')->where('f_id', $audit[$k]['borrower']['fid'])->value('borrower');
            }

            $audit[$k]['yj'] = 0;
            foreach ($jiezhi = Jiezhi::where('uid', $v['uid'])->select() as $k2 => $v2) {
                $audit[$k]['yj'] += $v2['net_payroll'];
            }
            $audit[$k]['kj'] = $audit[$k]['ys'] * $audit[$k]['borrower'] * 0.01 - $audit[$k]['yj'];
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
    public function update(Request $request)
    {
        $data = $request->post();
        $user = Jiezhi::where('id', $data['id'])->find();
        $user->sid = $data['sid'];
        $user->status = $data['status'];
        if ($data['status'] == 4) {
            $user->cause = $data['cause'];
        }
        $user->gcjltime = date('y-m-d H:i:s', time());
        $res = $user->save();
        if ($res) {
            $this->json(1, 'success', $user);
        }

    }

    public function user(Request $request)
    {
        $data = $request->get();
//        $history=Jiezhi::where('jid',$id)->select();
        $user = Userlist::where('id', $data['uid'])->find();
        $this->json(1, 'success', $user);
    }

    public function history(Request $request)
    {
        $data = $request->get();
        $history = Jiezhi::with(['offer', 'cw', 'gcjl'])->where('uid', $data['uid'])->select();
        $this->json(1, 'success', $history);
    }

    public function money(Request $request)
    {
        $data = $request->get();
        $money = Db::table('fdz_financial')->where('userid', $data['uid'])->select();
        $borrower = Db::table('fdz_financial')->where('userid', $data['uid'])->find();
        $borrower = Db::table('fdz_cost_tmp')->where('f_id', $borrower['fid'])->value('borrower');

        $ys = 0;
        foreach ($money as $k => $v) {
            $ys += $v['money'];
        }
//     已收款
        //可接比率
        //已借
        $jiezhi = Jiezhi::where('uid', $data['uid'])->select();
        $yj = 0;
        foreach ($jiezhi as $k2 => $v2) {
            $yj += $v2['net_payroll'];
        }
        $n['ys'] = $ys;
        $n['yj'] = $yj;
        $n['borrower'] = $borrower;
        $n['kj'] = $n['ys'] * $n['borrower'] * 0.01 - $n['yj'];

        $this->json(1, 'success', $n);
    }


    //工人
    public function getworker(Request $request)
    {
        $data = $request->get();
        $worker = Db::table('fdz_admin_worker')->where('jid', $data['id'])->find();
        if (empty($worker)) {
            $data['water'] = 0;
            $data['electricity'] = 0;
            $data['timber'] = 0;
            $data['tile'] = 0;
            $data['grey'] = 0;
            $data['painter'] = 0;
            $data['rests'] = 0;
        } else {
            $data = $worker;
        }
        $this->json(1, 'success', $data);
    }

    public function setworker(Request $request)
    {
        $data = $request->post();
        $worker = Db::table('fdz_admin_worker')->where('jid', $data['id'])->find();
        $da = [
            'jid' => $data['id'],
            'water' => $data['water'],
            'electricity' => $data['electricity'],
            'timber' => $data['timber'],
            'tile' => $data['tile'],
            'grey' => $data['grey'],
            'painter' => $data['painter'],
            'rests' => $data['rests'],
        ];
        if (empty($worker)) {
            $inserworker = Db::table('fdz_admin_worker')->strict(false)->insert($da);
            if ($inserworker) {
                $this->json(1, 'success', '添加成功');
            }
        } else {
            $inserworker = Db::table('fdz_admin_worker')->where('jid', $data['id'])->update($da);
            if ($inserworker) {
                $this->json(1, 'success', '更新成功');
            }else{
                $this->json(1, 'success', '更新失败');
            }
        }

    }
}
