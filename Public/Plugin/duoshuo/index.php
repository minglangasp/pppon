<?php
class duoshuoPlugin extends Plugin
{
	static function index()
	{
		$plugin = new Plugin();
		$plugin->assign('config',$plugin->config());
		return $plugin->display();
	}
	/* 获取多说的评论数  */
	static function comment()
	{
		return "<span class=\"ds-thread-count\" data-thread-key=\"".C('COOKIE_PREFIX').$GLOBALS['_fields']['id']."\" data-count-type=\"comments\"></span>";
	}
	
	/* 获取多说的新浪微博转发数  */
	static function weibo()
	{
		return "<span class=\"ds-thread-count\" data-thread-key=\"".C('COOKIE_PREFIX').$GLOBALS['_fields']['id']."\" data-count-type=\"weibo_reposts\"></span>";
	}
	
	/* 获取多说的qq微博转发数  */
	static function qq()
	{
		return "<span class=\"ds-thread-count\" data-thread-key=\"".C('COOKIE_PREFIX').$GLOBALS['_fields']['id']."\" data-count-type=\"qqt_reposts\"></span>";
	}
	
	/*站内热评文章*/
	/*
		参数1: short_name  域名 
		参数2: hotlist-range  热评文章范围  0=每天,1=每周,2=每月
		参数3: hotlist-items  热评文章个数  默认值 5条
	*/
	static function hotlist()
	{
		$plugin = new Plugin();
		$config = $plugin->config();
		if(empty($config['hotlist-range']) or $config['hotlist-range']==0) $range = 'daily';
		if($config['hotlist-range']==1) $range = 'weekly';
		if($config['hotlist-range']==2) $range = 'monthly';
		$item = empty($config['hotlist-items']) ? 5:$config['hotlist-items'];
		$str =<<<DATA
		<ul  class="ds-top-threads" data-range="{$range}" data-num-items="{$item}"></ul><script type="text/javascript">var duoshuoQuery = {short_name:"{$config['short_name']}"};(function() {var ds = document.createElement('script');ds.type = 'text/javascript';ds.async = true;ds.src = 'http://static.duoshuo.com/embed.js';ds.charset = 'UTF-8';(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ds);})();</script>
DATA;
		return $str;
	}
	
	/*站内最近访客*/
	/*
		参数1: short_name  域名 
		参数3: visitor-items  访客人数  默认值 5
	*/
	static function visitor()
	{
		$plugin = new Plugin();
		$config = $plugin->config();
		$item = empty($config['visitor-items']) ? 10:$config['visitor-items'];
		$str =<<<DATA
		<ul class="ds-recent-visitors" data-num-items="{$item}"></ul><script type="text/javascript">var duoshuoQuery = {short_name:"{$config['short_name']}"};(function() {var ds = document.createElement('script');ds.type = 'text/javascript';ds.async = true;ds.src = 'http://static.duoshuo.com/embed.js';ds.charset = 'UTF-8';(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ds);})();</script>
DATA;
		return $str;
	}
	
	/*站内最新评论*/
	/*
		参数1: short_name  域名 
		参数2: newlist-items  	最新评论数  默认值 10条
		参数3: newlist-show-avatars  是否显示头像，1：显示，0：不显示
		参数4: newlist-show-time  是否显示时间，1：显示，0：不显示
		参数5: newlist-show-title  是否显示标题，1：显示，0：不显示
		参数6: newlist-show-admin  是否显示管理员的评论，1：显示，0：不显示
		参数7: newlist-excerpt-length  最大显示评论汉字数 默认 70
	*/
	static function newlist()
	{
		$plugin = new Plugin();
		$config = $plugin->config();
		$item = empty($config['newlist-items']) ? 10:$config['newlist-items'];
		$avatars = empty($config['show-avatars']) ? 1:$config['show-avatars'];
		$time = empty($config['show-time']) ? 1:$config['show-time'];
		$title = empty($config['show-title']) ? 1:$config['show-title'];
		$admin = empty($config['show-admin']) ? 1:$config['show-admin'];
		$length = empty($config['excerpt-length']) ? 1:$config['excerpt-length'];
		$str =<<<DATA
		<ul class="ds-recent-comments" data-num-items="{$item}" data-show-avatars="{$avatars}" data-show-time="{$time}" data-show-title="{$title}" data-show-admin="{$admin}" data-excerpt-length="{$length}"></ul><script type="text/javascript">var duoshuoQuery = {short_name:"{$config['short_name']}"};(function() {var ds = document.createElement('script');ds.type = 'text/javascript';ds.async = true;ds.src = 'http://static.duoshuo.com/embed.js';ds.charset = 'UTF-8';(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ds);})();</script>
DATA;
		return $str;
	}
	
}