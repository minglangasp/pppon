<?php
class erweimaPlugin extends Plugin
{
	static function index()
	{
		$plugin = new Plugin();
		return $plugin->display('admin.html');
	}
}
