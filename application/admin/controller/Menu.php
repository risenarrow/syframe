<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;


class Menu extends AdminBase
{
    /*菜单列表*/
    public function index()
    {
         $menulist = list_menu();
         $this->assign('menulist',$menulist);
        return $this->fetch();
    }


    public function addEdit(){
        $id = input('id',0,'intval');
        $parentid = input('parentid',0,'intval');
        $data = input('post.data/a');

        if(request()->isPost()){

            $this->check_data($data);
            if($id){

               if(db_base('menu')->where(array('id'=>$id))->update($data)){
                    $this->success('修改成功',url('admin/Menu/index'));
               }else{
                   $this->error('修改失败');
               }
            }else{
                if(db_base('menu')->insert($data)){
                    $this->success('添加成功',url('admin/Menu/index'));
                }else{
                    $this->error('添加失败');
                }
            }
        }else{
            $data = db_base('menu')->find($id);
            $this->assign('data',$data);
            $list_menu = list_menu();
            $this->assign('list_menu',$list_menu);
            $this->assign('parentid',$parentid);
            return $this->fetch();
        }
    }

    public function del(){
        $id=input('id');
        $menuinfo = db_base('menu')->where(['parentid'=>$id])->find();
        if(!empty($menuinfo)){
            $this->error('改菜单下还有子菜单，请先删除子菜单');
        }
        if(db_base('menu')->where(['id'=>$id])->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }


    private function check_data($data){
        foreach($data as $k=>$v){
            if(empty($data['name'])){
               $msg = '菜单名称不能为空';
            }elseif(empty($data['m'])){
                $msg = '模块名不能为空';
            }elseif(empty($data['c'])){
                $msg = '控制器名不能为空';
            }elseif(empty($data['a'])){
                $msg = '方法名不能为空';
            }
        }
    }

}
