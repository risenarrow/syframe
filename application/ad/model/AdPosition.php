<?php
namespace app\ad\model;

use think\Model;

class AdPosition extends Model
{
    public function pagelist(){
        $pagelist = db_base('ad_position')->order('id desc')->paginate(10);;
        return $pagelist;
    }
}