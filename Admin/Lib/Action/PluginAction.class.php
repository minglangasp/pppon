<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 独立插件渲染控制器

    @Filename PluginAction.class.php $

    @Author pengyong $

    @Date 2013-01-30 12:10:16 $
*************************************************************/
class PluginAction extends CommonAction 
{
	public function index()
	{
		//为方便调试, 插件功能模块编译不缓存
		C('TMPL_CACHE_ON',false);
		$name =  $this->_get('name',false);
		if(empty($name)) $this->error('参数错误!');
		$method =  $this->_get('method',false);
		$method = empty($method) ? 'index':$method;
		$path = './Public/Plugin/'.$name.'/admin.php';
		if(file_exists($path))
		{
			$model = M('plugin');
			$map['title'] = $name;
			$list = $model->where($map)->find();
			if(!$list) $this->error('当前插件没有注册!');
			if($list['status']==1) $this->error('当前插件没有启用!');
		}
		else
		{
			$this->error('当前插件无管理功能!');
		}
		echo plugin($name,$method);
	}
}