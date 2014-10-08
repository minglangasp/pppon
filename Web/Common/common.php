<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Web组 函数库

    @Filename common.php $

    @Author pengyong $

    @Date 2013-01-05 20:27:29 $
*************************************************************/
	/*
		前台网址路由转换
	*/
	function url($model,$id='',$parameter='')
	{
		$root  = $GLOBALS['cfg_rewrite']==0 ? __ROOT__ :__ROOT__.'/index.php';
		$suffix = C('URL_HTML_SUFFIX');
		if($model=='search') return $root.'/search'.'.'.$suffix.$id;
		if($model=='ad') $suffix = 'js';
		//url('plugin','hello/index')  ====>  plugin-hello/index
		if(strpos($id,'/'))
		{
			$ids = explode('/',$id);
			return $root.'/'.$model.'-'.$ids[0].'/'.$ids[1].$suffix.$parameter;
		}
		return $root.'/'.$model.'-'.$id.'.'.$suffix.$parameter;
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
	
	
	//标签直接sql 查询
	function query($sql='')
	{
		$model = M();
		return $model->query($sql);
	}
	
	//中文字符串截取
    function cn_substr($str, $start=0, $length=100, $suffix=true,$charset="utf-8") 
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
		字符串截取函数 wk_substr
		var sourcestr  原始字符串
		var $cutlength 截取字符串长度
		var suffix     携带后缀...
	*/
	function wk_substr($sourcestr,$cutlength,$suffix=true){$returnstr=''; $i=0; $n=0; $str_length=strlen($sourcestr);while (($n<$cutlength) and ($i<=$str_length))  { $temp_str=substr($sourcestr,$i,1); $ascnum=Ord($temp_str); if ($ascnum>=224){ $returnstr=$returnstr.substr($sourcestr,$i,3);$i=$i+3;$n++;}elseif ($ascnum>=192){$returnstr=$returnstr.substr($sourcestr,$i,2); $i=$i+2;$n++;}elseif ($ascnum>=65 && $ascnum<=90){$returnstr=$returnstr.substr($sourcestr,$i,1); $i=$i+1;$n++;}else {$returnstr=$returnstr.substr($sourcestr,$i,1); $i=$i+1;$n=$n+0.5;}} if ($str_length>$cutlength && $suffix){$returnstr = $returnstr . "...";}return $returnstr;}

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
	function load_plugin($name,$group='index')
	{
		$path = './Public/Plugin/'.$name.'/'.$group.'.php';
		set_include_path(__ROOT__);
		C('__PLUGIN__','./Public/Plugin/'.$name);
		include_once $path;
	}
	
	/**
	*  修复浏览器XSS hack的函数
	*
	* @param     string   $val  需要处理的内容
	* @return    string
	*/
	function RemoveXSS($val) 
	{
       $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
       $search = 'abcdefghijklmnopqrstuvwxyz';
       $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
       $search .= '1234567890!@#$%^&*()';
       $search .= '~`";:?+/={}[]-_|\'\\';
       for ($i = 0; $i < strlen($search); $i++) {
          $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
          $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
       }

       $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
       $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
       $ra = array_merge($ra1, $ra2);

       $found = true; 
       while ($found == true) {
          $val_before = $val;
          for ($i = 0; $i < sizeof($ra); $i++) {
             $pattern = '/';
             for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                   $pattern .= '(';
                   $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                   $pattern .= '|';
                   $pattern .= '|(&#0{0,8}([9|10|13]);)';
                   $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
             }
             $pattern .= '/i';
             $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
             $val = preg_replace($pattern, $replacement, $val); 
             if ($val_before == $val) {
                $found = false;
             }
          }
       }
       return $val;
    }
	/**
	*  arclist标签 getall=1 时,typeid='~typeid~'	
	*  获取子栏目 id,返回sql语句一部分 
	* @param     int   $id  typeid
	* @return    string
	*/
	function _arclist_getall_sonid($typeid)
	{
		$arctype = M('arctype');
		$map['id'] = $typeid;
		$bpath = $arctype->field("concat(path,'-',id) as bpath")->where($map)->find();
		$data['path'] = array('like',array('%'.$bpath['bpath']),'OR');
		$typeids = $arctype->field('id')->where($data)->select();
		$tid = $typeid.',';
		foreach($typeids as $v)
		{
			$tid .= $v['id'].',';
		}
		$tid = rtrim($tid,',');
		return $tid;
	}
	/*
		arctype 标签  type=self 获取 当前栏目fid
	*/
	function _arctype_self_getfid($typeid)
	{
		$map['id'] = $typeid;
		$arctype = M('arctype');
		$list = $arctype->where($map)->field('fid')->find();
		return $list['fid'];
	}
	
	/*
		arctype 标签  type=parent 获取 当前栏目fid 的 fid
	*/
	function _arctype_parent_getfid($typeid)
	{
		$map['id'] = $typeid;
		$arctype = M('arctype');
		$list = $arctype->where($map)->field('fid')->find();
		if($list['fid']==0 or empty($list['fid'])) return 0;
		$map['id'] = $list['fid'];
		$list = $arctype->where($map)->field('fid')->find();
		if($list['fid']==0 or empty($list['fid'])) return 0;
		return $list['fid'];
	}
	
	/*
		arclist 标签  动态输出 sql limit 起始位置
	*/
	function _page_limit_a($totalnum,$pagesize)
	{
		$p = isset($_GET['p']) ? (int)$_GET['p']:1;
		$maxpage = ceil($totalnum/$pagesize);
		if($p >= $maxpage) $p = $maxpage;
		if($p<=1) $p = 1;
		return $pagesize * ($p -1); 
	}
	/*
		page 标签  动态输出 分页信息
	*/
	function _page_output($totalnum,$pagesize=20,$linknum=5,$option,$getvar='p')
	{
		if($totalnum < 1 or empty($option) or $pagesize<1) return;
		$p = isset($_GET[$getvar]) ? (int)$_GET[$getvar]:1;
		$maxpage = ceil($totalnum/$pagesize);
		if($p >= $maxpage) $p = $maxpage;
		if($p<=1) $p = 1;
		$limit_a = $pagesize * ($p -1); $limit_b = $pagesize;
		$request_uri = isset($_GET[$getvar]) ? strtr($_SERVER['REQUEST_URI'],array("&{$getvar}=".$_GET[$getvar]=>'',"?{$getvar}=".$_GET[$getvar]=>'')) : $_SERVER['REQUEST_URI'];
		$Delimiter  = strpos($request_uri,'?')!==false ? '&' : '?';
		$pageurl = $request_uri.$Delimiter."{$getvar}=";
		$page['pagesize'] = $pagesize;
		$page['totalnum'] = $totalnum;
		$page['totalpagenum'] = $maxpage;
		$page['nowpagenum'] = $p;
		$page['indexurl'] = $p==1 ? '' : $pageurl.'1';
		$page['indexpage'] = $p==1 ? '<a class="disabled">首页</a>' : '<a href=\''.$pageurl.'1'.'\'/>首页</a>';
		$page['endpage'] = $p==$maxpage ? '<a class="disabled">末页</a>' :'<a href=\''.$pageurl.$maxpage.'\'/>末页</a>';
		$page['endurl'] = $p==$maxpage ? '' :$pageurl.$maxpage;
		$page['nowurl'] = $pageurl.$p;
		$page['nowpage'] = '<a href=\''.$pageurl.$p.'\'/>当前页</a>';
		$page['preurl'] =  $p>1 ?  $pageurl.($p-1) : '';
		$page['prepage'] =  $p>1 ?  '<a href=\''.$pageurl.($p-1).'\'/>上一页</a>' : '<a class="disabled">上一页</a>';
		$page['nextpage'] = $p<$maxpage ?  '<a href=\''.$pageurl.($p+1).'\'/>下一页</a>' : '<a class="disabled">下一页</a>';
		$page['nexturl'] = $p<$maxpage ? $pageurl.($p+1) :'';
		$page['pageinfo'] = '当前第'.$page['nowpagenum'].'页,'.'共'.$page['totalpagenum'].'页,共'.$page['totalnum'].'条记录';
		if($page['totalnum']==0) return;
		$link = ceil($page['nowpagenum']/$linknum);
		$page['linkpage'] = '';
		$page['select'] = "<select onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>";
		for($i=($link-1)*$linknum;$i<=$link*$linknum;$i++)
		{	
			if($i <= $page['totalpagenum'] && $i >0)
			{
				if($i == $page['nowpagenum'])
				{
					$page['linkpage'] .= '<a class="current">'.$i.'</a>';
					$page['select']   .= '<option value=\''.$pageurl.$i.'\' selected=\'selected\'>'.$i.'</option>';
				}
				else
				{
					$page['linkpage'] .= '<a href=\''.$pageurl.$i.'\'>'.$i.'</a>';
					$page['select']   .= '<option value=\''.$pageurl.$i.'\'>'.$i.'</option>';
				}
			}
		}
		$page['select'] .= '</select>';
		$op = explode(',',$option);
		$parseStr='';
		foreach($op as $v)
		{
			$parseStr .= $page[$v];
		}
		return  $parseStr;
	}
	/*
		投票显示函数
	*/
	function _vote_show($id,$show,$barid,$notitle,$showtotal)
	{
		global $_fields;
		//id为空,则尝试获取全局变量voteid
		if(empty($id) && !empty($_fields['voteid'])) $id = (int)$_fields['voteid'];
		if($id<1) return;
		$model = M('vote');
		$map['status'] = 1;
		$map['id'] = $id;
		$list = $model->where($map)->find();
		if(!$list) return;
		if($list['overtime'] < time()) $show=1;
		if($showtotal==1)
		{
			$content = explode("\n",trim($list['content']));
			$total = 0;
			for($i = 0;$i < count($content);$i++)
			{
				$s = explode("=",trim($content[$i]));
				$total += $s[1];
			}
			return $total;
		}
		//已经投过票则直接显示 投票结果
		$cid = cookie('vote_'.$map['id']);
		if(!empty($cid))  $show=1;
		if($show<>1)
		{
			$parseStr   =  '<div class="artvote">';
			$parseStr   .=  '<form action="'.U('Vote/update').'" method="post">';
			$parseStr   .=  '<ul>';
			$parseStr   .=  '<li><h5>'.$list['title'].'</h5></li>';
			$content = explode("\n",trim($list['content']));
			$type = $list['type']==0 ? 'radio' : 'checkbox';
			for($i = 0;$i < count($content);$i++)
			{
				$s = explode("=",trim($content[$i]));
				$parseStr   .=  '<li><input type="'.$type.'"  name="vote[]" value="'.$s[0].'='.$s[1].'">'.$s[0].'</li>';
				$parseStr   .=  '<input type="hidden"  name="key[]" value="'.$i.'"/>';
			}
			$parseStr   .=  '<li><input type="submit" value="投票" class="artsubmit">';	
			$parseStr   .=  '<input type="button" onclick="window.open(\''.url('vote',$list['id']).'\')" value="查看" class="artsubmit"></li>';	
			$parseStr   .=  '<input type="hidden" name="id" value="'.$list['id'].'">';	
			$parseStr   .=  '</form></ul></div>';	
		}
		else
		{
			//输出投票结果[需要提前加载bar.js和jquery.js]
			$parseStr   =  '<!-- 输出投票结果 如果没有显示,请检查是否加载jquery.js-->';
			$parseStr   .=  '<script src=\''.__ROOT__.'/Public/Admin/js/bar.js\'></script>';
			$parseStr   .=  '<div id=\'show\'></div>';
			$parseStr   .=  '<script>var data = [];';
			$content = explode("\n",trim($list['content']));
			$total = 0;
			for($i = 0;$i < count($content);$i++)
			{
				$s = explode("=",trim($content[$i]));
				$parseStr   .=  'data['.$i.'] = [\''.$s[0].'\',\''.$s[1].'\'];';
				$total += $s[1];
			}
			$parseStr   .=  'var bar_'.$barid.' = new bar();';
			$parseStr   .=  'bar_'.$barid.'.id = \''.$barid.'\';';
			if($notitle<>1) $parseStr   .=  'bar_'.$barid.'.title = \''.$list['title'].'总计:'.$total.'票\';';
			$parseStr   .=  'bar_'.$barid.'.bgimg = \'__PUBLIC__/Admin/js/bar_plan.gif\';';
			$parseStr   .=  'bar_'.$barid.'.data = data;';
			$parseStr   .=  'bar_'.$barid.'.show();';
			$parseStr   .=  '</script>';
		}
		return $parseStr;
	}
	/*
		获取客户端操作系统
	*/
	function getOS()
	{
		global $_SERVER;    
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		$agent=strtolower($agent);   
		$os = false;    
		if (eregi('win 9x', $agent) && strpos($agent, '4.90'))
		{    
			$os = 'windows';    
		}    
		else if (eregi('win', $agent) && ereg('98', $agent))
		{    
			$os = 'windows';    
		}    
		else if (eregi('win', $agent) && eregi('nt 5.1', $agent))
		{    
			$os = 'windows';    
		}    
		else if (eregi('win', $agent) && eregi('nt 5', $agent))
		{    
			$os = 'windows';    
		}    
		else if (eregi('win', $agent) && eregi('nt', $agent))
		{    
			$os = 'windows';    
		} 
		else if (eregi('ios', $agent) && eregi('ios', $agent))
		{    
			$os = 'ios';    
		}
		else if (eregi('android', $agent) && eregi('android', $agent))
		{    
			$os = 'android';    
		} 
		else if (eregi('iphone', $agent) && eregi('iphone', $agent))
		{    
			$os = 'iphone';    
		}    
		else if (eregi('win', $agent) && ereg('32', $agent))
		{    
			$os = 'windows 32';    
		}    
		else if (eregi('linux', $agent))
		{    
			$os = 'Linux';    
		}    
		else if (eregi('unix', $agent))
		{    
			$os = 'Unix';    
		}    
		else if (eregi('sun', $agent) && eregi('os', $agent))
		{    
			$os = 'SunOS';    
		}    
		else if (eregi('ibm', $agent) && eregi('os', $agent))
		{    
			$os = 'IBM OS/2';    
		}    
		else if (eregi('Mac', $agent) && eregi('PC', $agent))
		{    
			$os = 'Macintosh';    
		}    
		else if (eregi('PowerPC', $agent))
		{    
			$os = 'PowerPC';    
		}    
		else if (eregi('AIX', $agent))
		{    
			$os = 'AIX';    
		}    
		else if (eregi('HPUX', $agent))
		{    
			$os = 'HPUX';    
		}    
		else if (eregi('NetBSD', $agent))
		{    
			$os = 'NetBSD';    
		}    
		else if (eregi('BSD', $agent))
		{    
			$os = 'BSD';    
		}    
		else if (ereg('OSF1', $agent))
		{    
			$os = 'OSF1';    
		}    
		else if (ereg('IRIX', $agent))
		{    
			$os = 'IRIX';    
		}    
		else if (eregi('FreeBSD', $agent))
		{    
			$os = 'FreeBSD';    
		}    
		else if (eregi('teleport', $agent))
		{    
			$os = 'teleport';    
		}    
		else if (eregi('flashget', $agent))
		{    
			$os = 'flashget';    
		}    
		else if (eregi('webzip', $agent))
		{    
			$os = 'webzip';    
		}    
		else if (eregi('offline', $agent))
		{    
			$os = 'offline';    
		}    
		else 
		{    
			$os = 'Unknown';    
		}    
		return $os;    
	}
	/*
		生成其它应用的地址
	*/
	function App_url($url,$mode='user',$from='index',$urlmode=0)
	{
		$config = C('URL_MODEL');
		C('URL_MODEL',$urlmode);
		$u = U($url);
		C('URL_MODEL',$config);
		return str_ireplace($from.'.php',$mode.'.php',$u);
	}
	/*
		themelist标签获取主题列表
	*/
	function _theme_list($dirname='',$row=10)
	{
		$themearray = F('themelist');
		if(empty($themearray))
		{
			$dir = './Public/Tpl';
			$list = array_slice(scandir($dir),2);
			foreach($list as $k=>$v)
			{
				$t = File::cache('theme','',$dir.'/'.$v.'/',false);
				$t['dirname'] = $v;
				$t['themename'] = empty($t['name']) ? $v :$t['name'];
				$t['themeurl'] = __ROOT__.'/?theme='.$v;
				$t['themelink'] = '<a href=\''.$t['themeurl'].'\'>'.$t['themename'].'</a>';
				$t['_default']  = $v==$GLOBALS['cfg_df_style'] ? 1:0;
				$themearray[$k] = $t;
			}
			F('themelist',$themearray);
		}
		if(!empty($dirname))
		{
			$dirname = explode(',',$dirname);
			foreach($themearray as $k=>$v)
			{
				if(!in_array($v['dirname'],$dirname)) unset($themearray[$k]);
			}
			return $themearray;
		}
		if(count($themearray) > $row) return array_slice($themearray,0,$row);
		return $themearray;
	}
	
	/*
		文档统计函数 archivecount
		@int $typeid, 栏目id
		@int $getall, 是否获取子栏目文档 =1 获取
	*/
	
	function archivecount($typeid=0,$getall=0)
	{
		$model = M('archive');
		$map['arcrank'] = array('in','1,2,3');
		if($typeid==0)  return $model->count();
		if($getall==0) 
		{
			$map['typeid'] = $typeid;
		}
		else
		{	
			$arctype = M('arctype');
			$list = $arctype->field('id')->where('fid='.$typeid)->select();
			foreach($list as $v)
			{
				$id .= $v['id'].',';
			}
			$map['typeid'] = array('in',$id.$typeid);
		}
		return $model->where($map)->count();
	}
	
	/*
		获取用户名称
		getUsernameById
	*/
	function getUsernameById($id='')
	{
		$id = (int) $id;
		if($id==0) return;
		$model = M('member');
		return $model->where('id='.$id)->getField('username');
	}
	/*
		通用字段查询函数 
		getField(要获取的字段,查询的字段,查询的字段值,查询的表)
	*/
	function getField($field='title',$yourfield='id',$yourfieldvalue='',$table='archive')
	{
		$model = M($table);
		$map[$yourfield] = $yourfieldvalue;
		return $model->where($map)->getField($field);
	}
	
	/*
		栏目统计函数 arctypecount
		@int $typeid, 栏目id  id=0 则获取全部栏目数
		@int $type, type=top 顶级栏目, type=self 同级栏目数, type=son 子栏目
		2013-03-13 23:59:41 新增 type=parent 支持父级栏目调用
	*/
	function arctypecount($id=0,$type='son')
	{
		$model = M('arctype');
		if($id==0) return $model->count();
		if($type=='top')
		{
			$map['fid'] = 0;
			return $model->where($map)->count();
		}
		elseif($type=='self')
		{
			$fid = $model->where('id='.$id)->getField('fid');
			$map['fid'] = $fid;
			return $model->where($map)->count();
		}
		elseif($type=='son')
		{
			$map['fid'] = $id;
			return $model->where($map)->count();
		}
		elseif($type=='parent')
		{
			$fid = $model->where('id='.$id)->getField('fid');
			if($fid==0) return $model->where(array('fid'=>0))->count();
			$map['fid']  = $model->where('id='.$fid)->getField('fid');
			return $model->where($map)->count();
		}
		
	}