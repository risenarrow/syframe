<?php

/*获取菜单列表*/
function list_menu($parentid=0,$level=0){
    $list = db_base('menu')->where(['parentid'=>$parentid])->select();
    $arr = array();
    foreach($list as $k=>$v){
        $str = "";
        if($level == 1){
            $str = '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp|--';
        }elseif($level > 1){
            $str .= "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
            for($i=1;$i<$level;$i++){
                $str .= "|&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
            }
            $str .= '|--';
        }

        $str .= $v['name'];
        $v['name'] = $str;
        $child = list_menu($v['id'],$level+1);

        $arr[] = $v;
        $arr = array_merge($arr,$child);
    }
    return $arr;
}

/*获取菜单列表*/
function list_priv_menu($parentid=0,$level=0){
    $list = db_base('menu')->where(['parentid'=>$parentid])->select();
    $arr = array();
    foreach($list as $k=>$v){
        $child = list_priv_menu($v['id'],$level+1);
        $v['level'] = $level;
        $v['hassub'] = !empty($child)?1:0;
        $arr[] = $v;
        $arr = array_merge($arr,$child);
    }
    return $arr;
}


/*获取左边导航*/
function left_menu($parentid=0,$level=0){
    $list = db_base('menu')->where(['parentid'=>$parentid,'show'=>1])->select();
    foreach($list as $k=>$v){
        $list[$k]['child'] = left_menu($v['id'],$level+1);
        $list[$k]['level'] = $level;
        $list[$k]['url'] = url($v['m']."/".$v['c']."/".$v['a']);
    }

    return $list;
}

/**
 * 获取顶级id和次级id
 */
function topId_leftId($curModule='',$curController='',$curAction=''){
    $flag = true;
    $topid = 0;
    $leftid = 0;
    $curlink = db_base('menu')->where(['m'=>$curModule,'c'=>$curController,'a'=>$curAction])->find();

    while ($flag&&$curlink){

        if($curlink['parentid'] == '0'){
            $topid = $curlink['id'];
            $flag = false;
        }else{
            $leftid = $curlink['id'];
            $curlink = db_base('menu')->where(['id'=>$curlink['parentid']])->find();
        }
    }
    return array('topid'=>$topid,'leftid'=>$leftid);
}

/*清除缓存*/
function delFileByDir($dir) {
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {

            $fullpath = $dir . "/" . $file;
            if(is_dir($fullpath)) {
                delFileByDir($fullpath);
            }else{
                unlink($fullpath);
            }
        }
    }
    closedir($dh);
}

/**
 *管理员操作日志
 */

function admin_log($admin_id,$type,$admin_name,$des=''){
    $data = array(
        'admin_id'=>$admin_id,
        'type'=>$type,
        'admin_name'=>$admin_name,
        'des'=>$des
    );
    db_base('admin_action')->insert($data);
}

/**
 * 过滤不属于该角色菜单
 * @param $menu 菜单
 */
function check_auth_menu(&$menu){
    $admin = session('admin');
    if($admin['role_id'] == 1){
        return ;
    }
    $menuids = db_base('admin_role_priv')->where(['role_id'=>$admin['role_id']])->column('menu_id');
    foreach($menu as $k=>$v){
        if(!in_array($v['id'],$menuids)){
            unset($menu[$k]);
        }
        if(!empty($v['child'])){
            foreach($v['child'] as $key=>$val){
                if(!in_array($val['id'],$menuids)){
                    unset($menu[$k]['child'][$key]);
                }
            }
        }
    }
}

function check_auth($curModule='',$curController='',$curAction){
    $admin = session('admin');
    if($admin['role_id'] == 1){
        return true;
    }
    $privs = db_base('admin_role_priv')->where(['role_id'=>$admin['role_id']])->select();
    $count = 0;
    foreach($privs as $k=>$v){
        if(strpos($v['m'].'/'.$v['c'].'/'.$v['a'],$curModule.'/'.$curController.'/'.$curAction) !== false){
            $count = 1;
        }
    }
    return $count?true:false;
}