<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Web组 公共类

    @Filename CommonAction.class.php $

    @Author pengyong $

    @Date 2013-01-01 17:00:33 $
*************************************************************/
class CommonAction extends Action
{
	function _initialize() 
	{
		header("Content-type:text/html;charset=utf-8");
		import("ORG.File");
		import("ORG.Plugin");
		$model = M('config');
		$list = $model->select();
		foreach($list as $v)
		{
			$GLOBALS[$v['varname']] =$v['value'];
		}
		//临时关闭
		if($GLOBALS['cfg_cmsswitch']==0)
		{
			die($GLOBALS['cfg_cmsswitch_msg']);
		}
		//web主题切换
		if(isset($_GET['theme']) && !empty($_GET['theme']) && is_dir('./Public/Tpl/'.$_GET['theme'])) cookie('theme',$_GET['theme']);
		if(isset($_GET['theme']))
		{
			if(empty($_GET['theme']) or !is_dir('./Public/Tpl/'.$_GET['theme'])) cookie('theme',null);
		}
		$theme = cookie('theme');
		if(!empty($theme)) $GLOBALS['cfg_df_style'] = $theme;
		//网站根路径绝对地址
		$GLOBALS['cfg_cmsurl'] = $GLOBALS['cfg_basehost'] .$GLOBALS['cfg_indexurl'] ;
		$tplpath = __ROOT__.'/Public/Tpl/'.$GLOBALS['cfg_df_style'];
		//相对路径
		$ctplpath = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		if($GLOBALS['cfg_mobile'] <> 1)
		{
			//移动端自动识别
			$OS = getOS();
			$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
			if(in_array($OS,array('iphone','android')))
			{
				if($mode<>'wap')
				{
					$Delimiter  = empty ($_SERVER['QUERY_STRING'])?'?':'&';
					$request_uri = isset($_GET['mode']) ? strtr($_SERVER['REQUEST_URI'],array('&mode='.$_GET['mode']=>'','?mode='.$_GET['mode']=>'')) : $_SERVER['REQUEST_URI'];
					header('location:'.$request_uri.$Delimiter.'mode=wap');
				}
			}
			if($mode=='wap') 
			{
				$GLOBALS['_parameter'] = '?mode=wap'; $GLOBALS['_mode'] = 'wap';
				$tplpath = __ROOT__.'/Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
				$ctplpath = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
			}
		}
		defined('__THEME__') 	or define('__THEME__',$tplpath);
		C('TMPL_PARSE_STRING.__THEME__',__THEME__);
		C('TMPL_PARSE_STRING.__SEARCH__',url('search'));
		if(file_exists($ctplpath.'/theme.php')) $GLOBALS['_tplvars'] = F('theme','',$ctplpath.'/');
		//登录判断
		defined('USER_LOGINED') 	or define('USER_LOGINED',$this->isLogin());
		defined('ADMIN_LOGINED') 	or define('ADMIN_LOGINED',$this->isAdmin());
	}
	//位置导航
	protected function position()
	{
		global $_fields,$cfg_indexname,$cfg_list_symbol,$cfg_wap_tpl_default,$_mode,$_parameter;
		$ref = "<a href='".$GLOBALS['cfg_cmsurl']."'>".$cfg_indexname."</a>";
		if($_mode=='wap') $ref = "<a href='".__ROOT__."/{$_parameter}'>".$cfg_indexname."</a>";
		if(ACTION_NAME=='search' && MODULE_NAME=='Index') $ref.= $cfg_list_symbol.'搜索结果';
		if(ACTION_NAME=='index' && MODULE_NAME=='Vote') $ref.= $cfg_list_symbol.'网站投票';
		if(empty($_fields['typeid'])) return $ref;
		$model = M('arctype');
		$list = $model->field("concat(path,'-',id) as bpath")->where(array('id'=>$_fields['typeid']))->find();
		if(!$list) return $ref;
		$map['id'] = array('in',strtr(ltrim($list['bpath'],'0-'),array('-'=>',')));
		$list = $model->field("concat(path,'-',id) as bpath,typename,id")->where($map)->order('bpath')->select();
		foreach($list as $k=>$v)
		{
			$typelink = url('list',$v['id'],$_parameter);
			$ref.= $cfg_list_symbol."<a href='{$typelink}'>{$v['typename']}</a>";
		}
		if(!empty($_fields['id']))
		{
			$arcurl = url('view',$_fields['id'],$_parameter);
			$ref.= $cfg_list_symbol."<a href='{$arcurl}'>{$_fields['title']}</a>";
		}
		return $ref;
	}
	
	protected function isLogin()
	{
		$uname = cookie('uname');
		$uid = cookie('uid');
		$suid = session('uid');
		$wkcode = cookie('wkcode');
		if(empty($uname) || empty($uid) || empty($wkcode))
		{
			return false;
		}
		if($uid <>1 && $wkcode <> xmd5($uid.$uname,3))
		{
			return false;
		}
		if(strcmp($suid,$uid)<>0)
		{
			return false;
		}
		return true;
	}
	
	protected function isAdmin()
	{
		$uname = cookie('uname');
		$uid = cookie('uid');
		$suid = session('uid');
		if(empty($uname) or empty($uid))
		{
			return false;
		}
		if(session('cmsauth')<>substr(md5(strrev(cookie('uname')).'waikucms'.cookie('uid')),0,10))
		{
			return false;
		}
		if(strcmp($suid,$uid)<>0)
		{
			return false;
		}
		return true;
	}
}