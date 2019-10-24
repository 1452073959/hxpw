<?php
namespace app\admin\model;

use think\Model;

class Userlist extends Model
{

    public function profile()
    {
        return $this->hasOne(Offerlist::class,'id','oid');
    }

    public function user()
    {
        return $this->belongsTo(AdminUser::class,'jid','userid');
    }

    public function picking()
    {
        return $this->hasMany(PickingMaterial::class,'userid','id');
    }


}