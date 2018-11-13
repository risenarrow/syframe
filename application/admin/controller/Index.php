<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;
class Index extends AdminBase
{
    public function index()
    {
        return $this->fetch();
    }
    public function clear(){
        delFileByDir(ROOT_PATH.'runtime'.'/');
        $this->success('清除缓存成功');
    }
}
