<?php
$config= require './Public/Config/config.ini.php';
$admin_config=array(
	'URL_MODEL'             => 0,
	'TMPL_CACHE_ON' => false,//关闭模板缓存
	'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => 'Public:success', // 默认成功跳转对应的模板文件
);
return array_merge($config,$admin_config);
?>