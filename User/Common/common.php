<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function User组 函数库

    @Filename common.php $

    @Author pengyong $

    @Date 2013-01-11 16:49:48 $
*************************************************************/
	/*
		js直接跳转
	*/
	
	function jump($url)
	{
		$html = '<script>';	
		$html .= "window.location.href='".$url."';";
		$html .= '</script>';
		die($html);
	}

	/*
	获取当前完整路径url
	*/
	
	function curPageURL() 
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


	
	//会员密码加密规则
	function xmd5($str,$pattern='wkcms',$num=1)
	{
		$ref = md5(strrev($str.$pattern));
		for($i=1;$i<=$num;$i++)
		{
			$ref = md5(strrev($ref.$pattern));
		}
		return $ref;
	}
	
	
	
	/*
		前台网址路由转换
	*/
	function url($model,$id='',$parameter='')
	{
		$root  = $GLOBALS['cfg_rewrite']==0 ? __ROOT__ :__ROOT__.'/index.php';
		$config = include('./Web/Conf/config.php');
		$suffix = $config['URL_HTML_SUFFIX'];
		if($model=='search')  return $root.'/search'.'.'.$suffix.$id;
		if($model=='ad') $suffix = 'js';
		//url('plugin','hello/index')  ====>  plugin-hello/index
		if(strpos($id,'/'))
		{
			$ids = explode('/',$id);
			return $root.'/'.$model.'-'.$ids[0].'/'.$ids[1].$suffix.$parameter;
		}
		return $root.'/'.$model.'-'.$id.'.'.$suffix.$parameter;
	}
	
	
	/*
		中文字符串截取
	*/
    function cn_substr($str, $start=0, $length, $charset="utf-8", $suffix=true) 
	{
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice.'...' : $slice;
    }
	
	/*	
		挂载插件
		var name: 插件名
		var method: 插件执行方法
		var parameter: 附带参数,多个参数逗号隔开
	*/
	function plugin($name,$method='index',$parameter='')
	{
		$map['title'] = $name;
		$map['status'] = 0;
		$model = M('plugin');
		if(!$model->where($map)->find())  return ;
		load_plugin($name);
		$parameter = explode(',',$parameter);
		return call_user_func_array(array($name.'Plugin',$method),$parameter);
	}
	
	//导入插件类
	function load_plugin($name,$group='user')
	{
		$path = './Public/Plugin/'.$name.'/'.$group.'.php';
		set_include_path(__ROOT__);
		C('__PLUGIN__','./Public/Plugin/'.$name);
		include_once $path;
	}
	
	/*
		生成其它应用的地址
	*/
	function App_url($url,$mode='index',$from='user')
	{
		$config = C('URL_MODEL');
		C('URL_MODEL',0);
		$u = U($url);
		C('URL_MODEL',$config);
		return str_ireplace($from.'.php',$mode.'.php',$u);
	}
	
	/*
		获取远程文件内容
	*/
	function fopen_url($url)  
	{  

		if (function_exists('file_get_contents')) 
		{  
			$file_content = @file_get_contents($url); 
		} 
		elseif (ini_get('allow_url_fopen') && ($file = @fopen($url, 'rb')))
		{  
			$i = 0;  
			while (!feof($file) && $i++ < 1000) 
			{  
				$file_content .= strtolower(fread($file, 4096));  
			}  
			fclose($file);  

		} 
		elseif (function_exists('curl_init')) 
		{  
			$curl_handle = curl_init();  
			curl_setopt($curl_handle, CURLOPT_URL, $url);  
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT,2);  
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,1);  
			curl_setopt($curl_handle, CURLOPT_FAILONERROR,1);  
			curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Trackback Spam Check'); //引用垃圾邮件检查 
			$file_content = curl_exec($curl_handle);  
			curl_close($curl_handle);  
		}
		else 
		{  
			$file_content = '';  
		}  
		return $file_content;  
	} 
?>
