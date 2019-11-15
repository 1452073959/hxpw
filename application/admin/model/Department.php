<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class Department extends Model
{

    static public function getCates($pid=0,$uid)
    {
        $department = Department::with('ou')->where('fid',$uid)->select();
        if (empty($department)){
            $department = self::select();
        }
        $arr = [];
        foreach($department as $k=>$v){
            if ($v->pid==$pid) {
                $v->children = self::getCates($v->id,$uid);
                $arr[] = $v;
            }
        }
        return $arr;
    }


    public function ou()
    {
        return $this->hasMany(Personnel::class,'did','id');
    }

}


