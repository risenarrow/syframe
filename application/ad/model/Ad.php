<?php
namespace app\ad\model;

use think\Model;

class Ad extends Model
{
    public function pagelist(){
        $pagelist = db_base('ad')->order('sort desc,add_time desc')->paginate(10);;
        return $pagelist;
    }


}