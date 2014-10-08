<?php
class guestbookPlugin extends Plugin
{
	static function index()
	{
		$plugin =  new Plugin();
		$config = $plugin->config();
		$plugin->assign('config',$config);
		$GLOBALS['_fields']['id'] = C('COOKIE_PREFIX').'guestbook';
		$GLOBALS['_fields']['title'] = $config['title'];
		$GLOBALS['_fields']['description'] = $config['description'];
		$GLOBALS['_fields']['mid'] = 'admin';
		if($config['tpltype']==1)	
		{
			$path = strtr($config['tplpath'],array('{style}'=>'./Public/Tpl/'.$GLOBALS['cfg_df_style'].'/'));
			return $plugin->display($path);
		}
		return $plugin->display();
	}
	static function link()
	{
		return "<a href='".url('Plugin','guestbook')."'>留言本</a>";
	}

}