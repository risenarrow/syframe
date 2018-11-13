<?php
//配置文件
return [
        'view_replace_str' =>array(
            '__STATIC__'=>'/static/admin/',
            '__PUBLIC__'=>'/static/',
            '__UPLOAD__'=>'/public/uploads/',
        ),
        'config_type' =>array(
            '1'=>'基本',
            '2'=>'系统',
            '3'=>'扩展'
        ),
        'form_type'=>array(
            '1'=>'字符',
            '2'=>'数字',
            '3'=>'文本',
            '4'=>'图片',
            '5'=>'枚举',
            '6'=>'数组'
        ),
];