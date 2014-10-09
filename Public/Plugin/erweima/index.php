<?php
class erweimaPlugin extends Plugin
{
	static function index()
	{
		$plugin =  new Plugin();
		$config = $plugin->config();
		$width = empty($config['erweima_width']) ? 300:(int)$config['erweima_width'];
		$height = empty($config['erweima_height']) ? 300:(int)$config['erweima_height'];
		$url = self::curPageURL();
		return "http://chart.apis.google.com/chart?cht=qr&chs={$width}x{$height}&chl={$url}";
	}
	static function curPageURL() 
	{
		$pageURL = 'http';

		if ($_SERVER["HTTPS"] == "on") 
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
	
		if ($_SERVER["SERVER_PORT"] != "80") 
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} 
		else 
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
}