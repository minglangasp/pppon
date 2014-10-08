<?php
$config= require './Public/Config/config.ini.php';
$web_config=array(
'TMPL_CACHE_ON' => false,//关闭模板缓存
'TOKEN_ON'=>true,  // 是否开启令牌验证
'URL_MODEL' => 0,
'URL_CASE_INSENSITIVE' =>true,//url不区分大小写
'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文件
'TMPL_ACTION_SUCCESS'   => 'Public:success', // 默认成功跳转对应的模板文件
'MAIL_ADDRESS'=>'pengyong881215@126.com', // 邮箱地址
'MAIL_SMTP'=>'smtp.126.com', // 邮箱SMTP服务器
'MAIL_LOGINNAME'=>'pengyong881215', // 邮箱登录帐号
'MAIL_PASSWORD'=>'123456', // 邮箱密码
'MAIL_CHARSET'=>'UTF-8',//编码
'MAIL_AUTH'=>true,//邮箱认证
'MAIL_HTML'=>true,//true HTML格式 false TXT格式
);
return array_merge($config,$web_config);
?>