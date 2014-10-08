<?php
$config= require './Public/Config/config.ini.php';
$web_config=array(
'TMPL_ACTION_ERROR'     => './Public/Common/Tpl/error.htm',
'TMPL_ACTION_SUCCESS'     => './Public/Common/Tpl/success.htm',
'URL_MODEL'=>1,//pathinfo mode
'URL_CASE_INSENSITIVE' =>true,//url不区分大小写
//'DB_SQL_BUILD_CACHE' => true,//sql解析缓存
'TAGLIB_PRE_LOAD' => 'wk',
'URL_HTML_SUFFIX'=>'html',
'URL_ROUTER_ON'   => true, //开启路由
'TMPL_PARSE_STRING'  =>array( 
  '__TPL__' => __ROOT__.'/Public/Tpl', 
  '__WAP__' => __ROOT__.'/Public/Wap', 
 ),
'URL_ROUTE_RULES' => array(
  '/^search/'=>'Search/index',
  '/^record-(\d+)$/'=>'Index/record?pubdate=:1',
 '/^list-(\d+)$/' => 'List/index?id=:1',
 '/^special-(\d+)$/' => 'Index/special?id=:1',
 '/^view-(\d+)$/' => 'View/index?id=:1',
 '/^vote-(\d+)$/' => 'Vote/index?id=:1',
 '/^ad-(\d+)(\.js)?$/' => 'Index/ad?id=:1',
 '/^plugin-([a-z]+)(\/([a-z]+))?$/' => 'Plugin/index?name=:1&method=:3',
 ),
);
return array_merge($config,$web_config);
?>