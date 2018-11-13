<?php
namespace app\admin\model;

use think\Model;

class AdminRole extends Model
{
    public function roleList(){
        $list = db_base('admin_role')->select();
        $ids = array_column($list,'id');
        $rolelist = array_combine($ids,$list);
        return $rolelist;
    }


}