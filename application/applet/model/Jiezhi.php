<?php
namespace app\applet\model;

use think\Model;

class Jiezhi extends Model
{
    protected $pk = 'id';
    protected $table = 'fdz_jiezhi';
    protected $autoWriteTimestamp = 'datetime';
    public function user()
    {
        return $this->belongsTo('Userappler','jid','userid');
    }

    public function offer()
    {
        return $this->belongsTo('Offerlist','uid','id');
    }
}