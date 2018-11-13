<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;


class Setting extends AdminBase
{
    /*设置表单*/
    public function setting()
    {

        $type_id = input('type_id',1,'intval');

        $where = array();

        $type_id && $where['type_id'] = $type_id;

        $tempList =db_base('config')->where($where)->order('sort asc')->paginate(10);
        $configList = $tempList->items();
        foreach($configList as $k=>$v){
            if($v['form_type'] == 5||$v['form_type'] == 6){
                    /*取出配置项，格式化为数组*/
                    $list = explode("\r\n",$v['config_item']);

                    foreach($list as $key=>$val){
                        $list[$key] = explode('|',$val);
                        /*获取默认值，使第三个元素变为1*/
                        if($v['form_type'] == 6){
                            if(isset($v['value'])){
                                $checkVal = explode(",",$v['value']);
                                $list[$key][2] = in_array($list[$key][0],$checkVal)?1:0;
                            }else{
                                $checkVal = explode(",",$v['default_val']);
                                $list[$key][2] = in_array($list[$key][0],$checkVal)?1:0;
                            }
                        }elseif($v['form_type'] == 5){
                            if(isset($v['value'])){
                                $list[$key][2] = $v['value']==$list[$key][0]?1:0;
                            }else{
                                $list[$key][2] = $v['default_val']==$list[$key][0]?1:0;
                            }
                        }
                    }

                    $configList[$k]['list_arr'] = $list;

            }
        }

        $this->assign('page',$tempList->render());

        $configType = config('config_type');

        $this->assign('type_id',$type_id);

        $this->assign('configType',$configType);

        $this->assign('configList',$configList);

        return $this->fetch();
    }

    /*保存设置*/
    public function settingEdit(){
        if(request()->isAjax()){
            $data = input('data/a');
            $type_id = input('type_id/d');
            $form_type_arr = [];
            $configlist = db_base('config')->where(['type_id'=>$type_id])->select();
            foreach($configlist as $k=>$v){
                $form_type_arr[$v['name']] = $v['form_type'];
            }
            foreach($data as $k=>$v){
                if($form_type_arr[$k] == 6){
                    $v = implode(',',$v);
                }
                db_base('config')->where(['name'=>$k])->update(['value'=>$v]);
            }
            $this->success('保存成功',url('admin/setting/setting',array('type_id'=>$type_id)));
        }
    }


    public function configTypes()
    {
        $type_id = input('type_id',0,'intval');

        $where = array();

        $type_id && $where['type_id'] = $type_id;

        $configList = db_base('config')->where($where)->paginate(10);

        // 获取分页显示
        $page = $configList->render();

        $this->assign('page',$page);
        $configType = config('config_type');

        $type = config('form_type');

        $this->assign('type',$type);

        $this->assign('type_id',$type_id);

        $this->assign('configType',$configType);

        $this->assign('configList',$configList);

        return $this->fetch();
    }

    public function addEdit(){
        $id = input('id',0,'intval');

        $data = input('post.data/a');

        if(request()->isPost()){

            $this->check_data($data);
            if($id){

               if(db_base('config')->where(array('id'=>$id))->update($data)){
                    $this->success('修改成功',url('admin/setting/configTypes'));
               }else{
                   $this->error('修改失败');
               }
            }else{
                if(db_base('config')->insert($data)){
                    $this->success('添加成功',url('admin/setting/configTypes'));
                }else{
                    $this->error('添加失败');
                }
            }
        }else{
            $configTypes = config('config_type');
            $this->assign('configTypes',$configTypes);
            $form_type = config('form_type');
            $this->assign('form_type',$form_type);
            $data = db_base('config')->find($id);
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    public function del(){
        $id=input('ids');
        if(db_base('config')->where(['id'=>['in',$id]])->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }


    private function check_data($data){
        if(empty($data['name'])){
           $this->error('配置名称不能为空') ;
        }elseif(empty($data['title'])){
            $this->error('配置标题不能为空');
        }elseif(empty($data['type_id'])){
            $this->error('配置类型不能为空');
        }
    }

}
