<?php

// +----------------------------------------------------------------------
// | 统计报表
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Adminbase;
use think\Controller;
use Endroid\QrCode\QrCode;
use think\Db;

class Qrcodes extends Controller{

    //生成二维码
    public function view()
    {
        $user=Db::table('fdz_userlist')-> field(['id','address'])->select();
        foreach ($user as $k=>$v){
            $user[$k]['url']='http://'.$_SERVER['SERVER_NAME'].'/admin/qrcodes/add?id='.$v['id'];
        }

        require'../extend/phpqrcode/phpqrcode.php';
        foreach ($user as $k1=>$v1){
            $name=   parse_url($v1['url'],PHP_URL_QUERY) ;
            header('Content-Type: image/png');
            $qrcode = new \QRcode();
            $level = 'L';// 容错级别：L、M、Q、H
            $size = 4;
            $data=$v1['url'];
            $name='./uploads/qrcode/'.$name.'.png';
            Db::table('fdz_userlist')->where('id',$v1['id']) ->update(['qrcode' => $name]);
            $qrcode->png($data, $name, $level, $size);

        }

        return redirect('/admin/artificial/gcfx_first');
//        $qrcode->png($data,false,$level,$size,2);
//        $imageString = base64_encode(ob_get_contents());
//        ob_end_clean();
//        return "<img src='data:image/png;base64,{$imageString}'  />";
//        return download('image.jpg', 'my.jpg');
    }


    public function add()
    {
        $data=input();
        $construction=Db::table('fdz_userlist')->where('id',$data['id'])->field(['id','address'])->find();
        $worker=Db::name('basis_type_work')->select();
        $this->assign('construction',$construction);
        $this->assign('worker',$worker);
        return $this->fetch();
    }
    public function register()
    {
        $data=input();
        $data['create_time']=date('Y-m-d H:i:s', time());
        $req=Db::table('fdz_register')->whereTime('create_time','today')->where('uid',$data['uid'])->select();
        if(count($req)>1){
            return json(['code' => 2, 'msg' => '今日已提交,请明日再来', 'data' => $data]);
        }
        $res=Db::table('fdz_register')->insert($data);
        if($res){
            return json(['code' => 1, 'msg' => '提交成功', 'data' => $data]);
        }else{
            return json(['code' => 3, 'msg' => '提交失败,请稍后再试', 'data' => $data]);
        }
    }


}