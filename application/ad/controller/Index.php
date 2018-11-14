<?php
namespace app\ad\controller;
use app\admin\controller\AdminBase;
use app\ad\model\Ad;
use app\ad\model\AdPostion;

class Index extends AdminBase
{
    /*广告列表*/
    public function index()
    {

         $pagelist = model('Ad')->pagelist();
        $adlist = $pagelist->items();
        foreach($adlist as $k=>$v){
            $adlist[$k]['position_name'] = db_base('ad_position')->where(['id'=>$v['position_id']])->value('name');
        }
         $this->assign('page',$pagelist->render());
         $this->assign('adlist',$adlist);
         return $this->fetch();
    }


    /*广告位列表*/
    public function position(){
        $pagelist = model('AdPosition')->pagelist();
        $this->assign('page',$pagelist->render());
        $this->assign('ad_position_list',$pagelist->items());
        return $this->fetch();
    }

    /*广告位编辑或添加*/
    public function posAddEdit(){
        $id = input('id',0,'intval');
        $data = input('post.data/a');

        if(request()->isPost()){
            $this->check_pos_data($data);
            if($id){
                if(db_base('ad_position')->where(array('id'=>$id))->update($data)){
                    $this->success('修改成功',url('ad/Index/position'));
                }else{
                    $this->error('修改失败');
                }
            }else{
                if(db_base('ad_position')->insert($data)){
                    $this->success('添加成功',url('ad/Index/position'));
                }else{
                    $this->error('添加失败');
                }
            }
        }else{
            $data = db_base('ad_position')->find($id);
            $this->assign('data',$data);
            return $this->fetch();
        }
    }


    /*删除广告位*/
    public function posDel(){
        $ids = input('ids');
        if(db_base('ad_position')->where(['id'=>['in',$ids]])->delete()){
            $this->success('删除成功',url('ad/Index/position'));
        }else{
            $this->error('删除失败');
        }
    }

    /*广告编辑或添加*/

    public function addEdit(){
        $id = input('id',0,'intval');
        $data = input('post.data/a');
        if(request()->isPost()){
            $this->check_data($data);
            if($id){
               if(db_base('ad')->where(array('id'=>$id))->update($data)){
                    $this->success('修改成功',url('ad/Index/index'));
               }else{
                   $this->error('修改失败');
               }
            }else{
                $data['add_time'] = time();
                if(db_base('ad')->insert($data)){
                    $this->success('添加成功',url('ad/Index/index'));
                }else{
                    $this->error('添加失败');
                }
            }
        }else{
            $data = db_base('ad')->find($id);
            $position = db_base('ad_position')->select();
            $this->assign('data',$data);
            $this->assign('position',$position);
            return $this->fetch();
        }
    }

    public function del(){
        $ids = input('ids');
        if(db_base('ad')->where(['id'=>['in',$ids]])->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }


    private function check_pos_data($data){
        foreach($data as $k=>$v){
            if(empty($data['name'])){
                $this->error('菜单名称不能为空');
            }

        }
    }

    private  function check_data($data){
        foreach($data as $k=>$v){
            if(empty($data['position_id'])){
                $this->error('广告位不能为空');
            }elseif(empty($data['title'])){
                $this->error('广告标题不能为空');
            }elseif(empty($data['des'])){
                $this->error('广告描述不能为空');
            }

        }
    }




}
