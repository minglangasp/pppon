<?php
/****************
安装向导 函数库
$id pengyong.info
2011-11-12 21:01:33
****************/
include '../Core/Extend/Library/ORG/PclZip.class.php';
include '../Core/Extend/Library/ORG/File.class.php';
function RunMagicQuotes(&$str)
{
    if(!get_magic_quotes_gpc()) {
        if( is_array($str) )
            foreach($str as $key => $val) $str[$key] = RunMagicQuotes($val);
        else
            $str = addslashes($str);
    }
    return $str;
}

function gdversion()
{
  //没启用php.ini函数的情况下如果有GD默认视作2.0以上版本
  if(!function_exists('phpinfo'))
  {
      if(function_exists('imagecreate')) return '2.0';
      else return 0;
  }
  else
  {
    ob_start();
    phpinfo(8);
    $module_info = ob_get_contents();
    ob_end_clean();
    if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches)) {   $gdversion_h = $matches[1];  }
    else {  $gdversion_h = 0; }
    return $gdversion_h;
  }
}
function TestWrite($d)
{
    $tfile = '_wkt.txt';
    $d = preg_replace("#\/$#", '', $d);
    $fp = @fopen($d.'/'.$tfile,'w');
    if(!$fp) return false;
    else
    {
        fclose($fp);
        $rs = @unlink($d.'/'.$tfile);
        if($rs) return true;
        else return false;
    }
}
function GetBackAlert($msg,$isstop=0)
{
    global $s_lang;
    $msg = str_replace('"','`',$msg);
    if($isstop==1) $msg = "<script>\r\n<!--\r\n alert(\"{$msg}\");\r\n-->\r\n</script>\r\n";
    else $msg = "<script>\r\n<!--\r\n alert(\"{$msg}\");history.go(-1);\r\n-->\r\n</script>\r\n";
    $msg = "<meta http-equiv=content-type content='text/html; charset={$s_lang}'>\r\n".$msg;
    return $msg;
}
function PclZip_test()
{
	$zip =  new PclZip('data.zip');
	$zip->create('sql-dfdata.txt'); 
	if(!is_file('data.zip')) return false;
	$zip->extract(PCLZIP_OPT_PATH,'./_data');
	if(!is_file('./_data/sql-dfdata.txt')) return false;
	$content = File::read_file('./_data/sql-dfdata.txt');
	if(empty($content)) return false;
	return true;
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
	
	function fopen_url_test()
	{
		//$content =  fopen_url(str_replace('?'.$_SERVER["QUERY_STRING"],'',curPageURL()));
		$content =  fopen_url('http://cloud.waikucms.net');
		if(empty($content)) return false;
		return true;
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
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] .$_SERVER["REQUEST_URI"];
		} 
		else 
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	function app_cache($cmspath)
	{
		fopen_url($cmspath.'/index.php');
		fopen_url($cmspath.'/admin.php');
		fopen_url($cmspath.'/user.php');
		File::del_dir('../Web/Runtime');
		File::del_dir('../Admin/Runtime');
		File::del_dir('../User/Runtime');
	}
?>