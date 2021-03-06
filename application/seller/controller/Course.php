<?php

// +----------------------------------------------------------------------
// | 后台首页
// +----------------------------------------------------------------------
namespace app\seller\controller;

use app\common\controller\Sellerbase;
use think\Db;

class Course extends Sellerbase
{
    // 会员列表
    public function courseList()
    {
        if (Request()->isPost()) {
            $map = [];
            if (!empty(input('limit'))) {
                $map['num'] = input('limit');
            }
            if(!empty(input('word'))){
                $word = input('word');
                $map[] = ['title','LIKE',"%{$word}%"];
            }
            $data = model('user')->userList($map);
            $info['code']  = 0;
            $info['count'] = $data['total'];
            $info['data']  = $data['data'];
            $this->ajaxReturn($info);
        }
        return $this->fetch('courseList');
    }
}
