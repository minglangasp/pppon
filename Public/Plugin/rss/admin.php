<?php
class rssPlugin extends Plugin
{
	static function index()
	{
		$plugin = new Plugin();
		$model = M('arctype');
		$list = $model->field("id,typename,concat(path,'-',id) as bpath")->order('bpath')->select();
		$plugin->assign('list',$list);
		return $plugin->display('admin.html');
	}
}