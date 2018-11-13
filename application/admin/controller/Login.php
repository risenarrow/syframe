<?php
namespace app\admin\controller;
use app\admin\controller\Common;


class Login extends Common
{
    /*管理员登录*/
    public function login()
    {
        $admin_id = session('admin_id');
        if(!empty($admin_id)){
            $this->error('你已登录',url('admin/index/index'));
        }
        if(request()->isAjax()){
            $admin_name = input('admin_name');
            $password = input('password');
            if(empty($admin_name)){
                $this->error('账号不能为空');
            }
            if(empty($password)){
                $this->error('密码不能为空');
            }
            $admininfo = db_base('admin')->where(['admin_name'=>$admin_name])->find();
            if(empty($admininfo) ||encrypt($password,$admininfo['salt']) != $admininfo['password']){
                $this->error('账号或密码不正确');
            }
            if($admininfo['status'] != 1){
                $this->error('账户已被禁用');
            }
            $data['lastlogin_ip'] = get_client_ip();
            $data['lastlogin_time'] = time();
            db_base('admin')->where(['id'=>$admininfo['id']])->update($data);
            session('admin_id',$admininfo['id']);
            session('admin',$admininfo);
            $this->success('登录成功',url('admin/index/index'));
            admin_log($admininfo['id'],1,$admininfo['admin_name'],'登录');
        }
        return $this->fetch();
    }

    public function logout(){
        session('admin_id',null);
        session('admin',null);
        $this->redirect('admin/login/login');exit;
    }


}
