<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class Department extends Model
{

    static public function getCates($pid=0,$uid)
    {
        $department = Department::with('ou')->where('fid',$uid)->select();
        foreach ($department as $k=>$v)
        {
           foreach ($v['ou'] as $k1=>$v1)
           {
            if($v1['status']==2){
                unset($v['ou'][$k1]);
            }
           }
        }
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


