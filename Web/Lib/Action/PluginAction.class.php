<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 独立插件渲染控制器

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
		$path = './Public/Plugin/'.$name.'/index.php';
		if(!file_exists($path)) $this->error('当前插件不存在!');
		echo plugin($name,$method);
	}
}