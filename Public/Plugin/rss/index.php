<?php

class rssPlugin extends Plugin
{
	static function index()
	{
		header("Content-type: text/xml");
		$plugin = new Plugin();
		return $plugin->display();
	}
	
	static function arctype()
	{
		header("Content-type: text/xml");
		$plugin = new Plugin();
		$tid = isset($_GET['tid']) ? (int)$_GET['tid']:0;
		if($tid==0) return 	$plugin->error('request error!');
		$model = M('arctype');
		$list = $model->where('id='.$tid)->find();
		if(!$list) return $plugin->error('栏目ID不存在!');
		$list['typeid'] = $list['id'];
		$GLOBALS['_fields'] = $list;
		return $plugin->display('arctype.html');
	}
}