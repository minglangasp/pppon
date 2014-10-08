<?php
class guestbookPlugin extends Plugin
{
	static function index()
	{
		$plugin = new Plugin();
		return $plugin->display('admin.html');
	}
}
