<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;


function db_base($name=''){
    if(!empty($name)){
        return Db::name($name);
    }
}

function sys_config($name=''){
    $sys_config = cache('sys_config');
    if(empty($sys_config['IMAGE_SIZE'])){
        $sys_config = db_base('config')->select();
        $tempname = array_column($sys_config,'name');
        $sys_config =  array_combine($tempname,$sys_config);
        cache('sys_config',$sys_config);
    }

    return $name ?  $sys_config[$name]['value']:$sys_config;
}

function upload_one_thumb_js($id,$url,$imgid,$inputid)
{
    $imagesize = sys_config('IMAGE_SIZE');
    echo <<<eof
    <script>
 
     //实例化一个plupload上传对象
    var uploader_{$id} = new plupload.Uploader({
        browse_button : '{$id}', //触发文件选择对话框的按钮，为那个元素id
        url : '{$url}', //服务器端的上传页面地址
        flash_swf_url : SWF, //swf文件，当需要使用swf方式进行上传时需要配置该参数
        silverlight_xap_url : XAP, //silverlight文件，当需要使用silverlight方式进行上传时需要配置该参数
        filters: {
                  mime_types : [ //只允许上传图片和zip文件
                    { title : "Image files", extensions : "jpg,gif,png" }, 
                  ],
                  max_file_size : '{$imagesize}kb', //最大只能上传400kb的文件
                  prevent_duplicates : true //不允许选取重复文件
                },
         
	
    });    

    //在实例对象上调用init()方法进行初始化
    uploader_{$id}.init();
     uploader_{$id}.bind('FileUploaded',function(uploader,file,response){
        $("#{$imgid}").attr('src',UPLOADPATH+response.response).show();
        $("#{$inputid}").val(response.response);
     });
     uploader_{$id}.bind('FilesAdded',function(uploader_{$id},file){
             uploader_{$id}.start();
     });
     
     
 
     
    </script>
eof;
}
/*密码加密方法*/
function encrypt($password,$salt){
    return md5(md5($password.$salt).config('encrypt'));
}

/**
 * 随机获取字母
 * @param int $num  位数
 * @return string
 */
function rand_letters($num=6){
    $result = "";
    for($i=0;$i<$num;$i++){
        $result .= chr(rand(97,122));
    }
    return $result;
}

/**
 * 检查密码格式
 * @param $password
 * @return bool
 */

function is_pwd($password){
    if(!preg_match('/^[0-9a-zA-Z\_\+\!\^\$]{6,20}$/',$password)){
        return false;
    }else{
        return true;
    }
}

function is_username($username){
    if(!preg_match('/^[^0-9]/',$username)){
        return false;
    }
    if(!preg_match('/^[0-9a-zA-Z]{5,20}$/',$username)){
        return false;
    }else{
        return true;
    }
}



/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}



