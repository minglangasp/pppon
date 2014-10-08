<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 函数库

    @Filename common.php $

    @Author pengyong $

    @Date 2012-11-04 17:02:30 $
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
	
	//统计代码
	function tongji()
	{
		echo "<div style='display:none;'><script src='http://s14.cnzz.com/stat.php?id=4727005&web_id=4727005' language='JavaScript'></script>";
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

	
	//crc32 hash检测; 文件上传二次改名 便于hash检测,防止文件重传
	function crc32_file($filename){return hash_file('crc32', $filename);}
	function xbase64_decode($str){return strrev(base64_decode(strrev($str)));}
	
	//获取文档总数
	function archive_gettotal($id='',$method='arctype')
	{
		$arctinymodel = M('arctiny');
		if($method=='arctype') $map['typeid'] = $id;
		if($method=='arcmodel') $map['modelid'] = $id;
		return $arctinymodel->where($map)->count();
	}
	
	//文章列表解析flag
	function parseflag($flag)
	{
		$flagarray = array('h'=>'头条','c'=>'推荐','f'=>'幻灯','a'=>'特荐','s'=>'滚动','b'=>'加粗','p'=>'图片','j'=>'跳转');
		$flag = strtr($flag,$flagarray);
		return strtr($flag,array(','=>' '));
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
		生成其它应用的地址
	*/
	function App_url($url,$mode='user',$from='admin')
	{
		$config = C('URL_MODEL');
		C('URL_MODEL',0);
		$u = U($url);
		C('URL_MODEL',$config);
		return str_ireplace($from.'.php',$mode.'.php',$u);
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
		
	/**
	 +----------------------------------------------------------
	 * 字节格式化 把字节数格式为 B K M G T 描述的大小
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function byte_format($size, $dec=2)
	{
		$a = array("B", "KB", "MB", "GB", "TB", "PB");
		$pos = 0;
		while ($size >= 1024) {
			 $size /= 1024;
			   $pos++;
		}
		return round($size,$dec)." ".$a[$pos];
	}
	
	//获取模板类型名称
function gettplname($filename)
{
	switch($filename)
	{
		case 'index.htm':
			return '网站首页模板';
			break;
		case 'footer.htm':
			return '网站底部模板';
			break;
		case 'head.htm':
			return '网站头部模板';
			break;
		case 'search.htm':
			return '搜索页模板';
			break;
		case 'article_article.htm':
			return '文章模型文章页';
			break;
		case 'list_article.htm':
			return '文章模型列表页';
			break;
		case 'index_article.htm':
			return '文章模型频道页';
			break;
		case 'vote.htm':
			return '投票展示页';
			break;
		case 'theme.php':
			return '主题配置数据文件';
			break;
		case 'theme.xml':
			return '主题配置文件';
			break;
		case 'theme.jpg':
			return '主题缩略图';
			break;
		case 'theme.png':
			return '主题缩略图';
			break;
		case 'theme.gif':
			return '主题缩略图';
			break;
		case 'demo.sql':
			return '演示数据文件';
			break;
	}
	$f = ltrim(strrchr($filename,'.'),'.');
	switch($f)
	{
		case 'js':
			return 'js脚本文件';
			break;
		case 'php':
			return 'php脚本文件';
			break;
		case 'css':
			return '层叠样式表';
			break;
		case 'jpg':
			return 'jpg图片';
			break;
		case 'gif':
			return 'gif图片';
			break;
		case 'png':
			return 'png图片';
			break;
		case 'zip':
			return 'zip压缩包';
			break;
		case 'rar':
			return 'rar压缩包';
			break;
		case 'html':
			return '模板文件';
			break;
		case 'htm':
			return '网页文件';
			break;
		case 'ico':
			return 'ico图标';
			break;
		case 'pdf':
			return 'PDF文档';
			break;
		case 'ppt':
			return 'PPT文档';
			break;
		case 'doc':
			return 'DOC文档';
			break;
		case 'txt':
			return 'TXT文档';
			break;
		case 'xls':
			return 'XLS文档';
			break;
		case 'wmv':
			return 'wmv视频文件';
			break;
		case 'swf':
			return 'flash文件';
			break;
		case 'wma':
			return 'wma音频文件';
			break;
		case 'mp3':
			return 'mp3音频文件';
			break;
		case 'flv':
			return 'flv视频文件';
			break;
		case 'mp4':
			return 'mp4视频文件';
			break;
		default:
			return '未知文件';
			break;
	}
}
	
	// 获取时间颜色:24小时内为红色
	function getcolordate($type='Y-m-d H:i:s',$time,$color='red')
	{
		if((time()-$time)>86400)
		{
			return date($type,$time);
		}
		else
		{
			return '<font color="'.$color.'">'.date($type,$time).'</font>';
		}
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
	function load_plugin($name,$group='admin')
	{
		$path = './Public/Plugin/'.$name.'/'.$group.'.php';
		set_include_path(__ROOT__);
		C('__PLUGIN__','./Public/Plugin/'.$name);
		include_once $path;
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
	
	//栏目排序函数
	function sorttree($tree)
	{
		$model = M('arctype');
		$list = $model->getField('id,sortrank');
		$d = array();
		foreach($tree as $k=>$v)
		{
			$c = array();
			$b = explode('-',$v['bpath']);
			foreach($b as $bb)
			{
				if($bb==0)
				{
					$c[] = '0';
				}
				else
				{
					$x = $list[$bb]<10 ? '0'.$list[$bb]:$list[$bb];
					$c[] = $x.','.$bb;
				}
			}
			$tree[$k]['bsortpath'] = implode(',',$c);
			$d[$k] = implode(',',$c);
		}
		array_multisort($d,SORT_ASC,$tree);
		return $tree;
	}