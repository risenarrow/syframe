<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;


class Upload extends AdminBase
{
    /*后台上传*/
    public function upload()
    {
        $IMAGE_SIZE=sys_config('IMAGE_SIZE');
        $file = request()->file('file');
        $path = input('mod')?input('mod'):'other';
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['size'=>$IMAGE_SIZE*1024,'ext'=>'jpg,png,gif','type'=>'image/gif,image/png,image/jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads'.DS.$path);
            if($info){
                echo $path.DS.$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }


}
