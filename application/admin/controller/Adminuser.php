<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;
use app\admin\model\AdminRole;

class Adminuser extends AdminBase
{
    /*管理员列表*/
    public function index()
    {

        $where = array();
        $tempList =db_base('admin')->where($where)->order('id asc')->paginate(10);
        $list = $tempList->items();

        $roleList = model('AdminRole')->roleList();
        $this->assign('roleList',$roleList);
        $this->assign('page',$tempList->render());
        $this->assign('list',$list);
        return $this->fetch();
    }

    /*添加管理员*/
    public function addadmin(){
        $id = input('id',0,'intval');
        $data = input('data/a');
        $admininfo = db_base('admin')->where(['id'=>$id])->find();
        if(request()->isAjax()){
            if($id){
                if($id == 1&&empty($data['password'])){
                    $this->error('超级管理员只能修改密码');
                }
                if(empty($data['role_id'])){
                    $this->error('角色不能为空');
                }
                if(!empty($data['password'])){
                    $data['password'] = encrypt($data['password'],$admininfo['salt']);
                }else{
                    unset($data['password']);
                }

                $re = db_base('admin')->where(['id'=>$id])->update($data);
                if($re){
                    $this->success('修改成功',url('admin/adminuser/index'));
                }else{
                    $this->error('修改失败');
                }
            }else{
                if(empty($data['role_id'])){
                    $this->error('角色不能为空');
                }
                if(empty($data['admin_name'])){
                    $this->error('管理员账号不能为空');
                }
                if(!is_username($data['admin_name'])){
                    $this->error('管理员账号格式只能为数字和字母，且首位不能是数字,位数为5~20位');
                }
                if(empty($data['password'])){
                    $this->error('管理员密码不能为空');
                }
                if(!is_pwd($data['password'])){
                    $this->error('管理员密码格式不对');
                }
                $data['salt'] = rand_letters(6);
                $data['password'] = encrypt($data['password'],$data['salt']);
                $re = db_base('admin')->insert($data);
                if($re){
                    $this->success('添加成功',url('admin/adminuser/index'));
                }else{
                    $this->error('添加失败');
                }
            }
        }

        $roleList = model('AdminRole')->roleList();

        $this->assign('roleList',$roleList);
        $this->assign('data',$admininfo);
        return $this->fetch();
    }

    /*删除管理员*/
    public function deladmin(){
        $id=input('id');
        if(db_base('admin')->where(['id'=>['in',$id]])->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }


    /*管理员角色列表*/
    public function role(){
        $role = db_base('admin_role')->select();
        $this->assign('role',$role);
        return $this->fetch();
    }

    /*添加角色*/
    public function addEdit(){
        $id = input('id',0,'intval');

        $data = input('post.data/a');

        if(request()->isPost()){

            $this->check_data($data);
            if($id){

                if(db_base('admin_role')->where(array('id'=>$id))->update($data)){
                    $this->success('修改成功',url('admin/adminuser/role'));
                }else{
                    $this->error('修改失败');
                }
            }else{
                if(db_base('admin_role')->insert($data)){
                    $this->success('添加成功',url('admin/adminuser/role'));
                }else{
                    $this->error('添加失败');
                }
            }
        }else{
            $data = db_base('admin_role')->find($id);
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    /*删除角色*/
    public function del(){
        $id=input('id');
        if(db_base('admin_role')->where(['id'=>['in',$id]])->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /*设置角色权限*/
    public function rolePriv(){
        $id = input('id');
        if($id){
            if(request()->isAjax()){
                $menuid = input('menuid/a');
                if(!empty($menuid)){
                    $menuids = implode(',',$menuid);
                    $menus = db_base('menu')->where(['id'=>['in',$menuids]])->select();
                    $re = db_base('admin_role_priv')->where(['role_id'=>$id])->delete();
                    $insertData = [];
                    foreach($menus as $k=>$v){
                        $insertData[] = array(
                            'role_id'=>$id,
                            'm'=>$v['m'],
                            'c'=>$v['c'],
                            'a'=>$v['a'],
                            'menu_id'=>$v['id']
                        );
                    }
                    db_base('admin_role_priv')->insertAll($insertData);
                    $this->success('权限设置成功',url('admin/adminuser/rolepriv',['id'=>2]));
                }else{
                    $this->error('权限设置失败');
                }
            }
            $priv_list = list_priv_menu();
            $this->assign('priv_list',$priv_list);
            $menuids = db_base('admin_role_priv')->where(['role_id'=>$id])->column('menu_id');
            $this->assign('menuids',$menuids);
            $this->assign('id',$id);
            return $this->fetch();
        }else{
            $this->error('请选择角色');
        }
    }





    /*角色检查*/
    private function check_data($data){
        foreach($data as $k=>$v){
            if(empty($data['role_name'])){
               $this->error('角色名称不能为空');
            }
        }
    }


}
