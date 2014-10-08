<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function User组 初始化基类

    @Filename CommonAction.class.php $

    @Author pengyong $

    @Date 2013-01-11 16:04:02 $
*************************************************************/
class CommonAction extends Action
{
	function _initialize() 
	{
		$model = M('config');
		$list = $model->select();
		foreach($list as $v)
		{
			$GLOBALS[$v['varname']] =$v['value'];
		}
		import('ORG.File');
		import('ORG.Plugin');
		//设置发信配置
		C('MAIL_ADDRESS',$GLOBALS['cfg_smtp_usermail']);
		C('MAIL_SMTP',$GLOBALS['cfg_smtp_server']);
		C('MAIL_LOGINNAME',$GLOBALS['cfg_smtp_user']);
		C('MAIL_PASSWORD',$GLOBALS['cfg_smtp_password']);
		//登陆判断
		$this->isLogin() ? define('USER_LOGINED',true) : define('USER_LOGINED',false) ;
		global $cfg_mb_open,$cfg_mb_reginfo;
		if($cfg_mb_open==1) $this->error('系统会员功能已禁用!');
		//缓存用户信息
		if(USER_LOGINED==true)
		{
			$model = M('member');
			$list = $model->where(array('id'=>cookie('uid')))->find();
			$GLOBALS['member'] = $list;
			if($list['status']==1 && !in_array(MODULE_NAME,array('Index','Public'))) jump(U('Index/myfile'));
		}
		
	}
	protected function isLogin()
	{
		$uname = cookie('uname');
		$uid = cookie('uid');
		$sid = session('uid');
		if(strcmp($uid,$sid)<>0) return false;
		$wkcode = cookie('wkcode');
		if(empty($uname) || empty($uid) || empty($wkcode))
		{
			return false;
		}
		if($uid <>1 && $wkcode <> xmd5($uid.$uname,3))
		{
			return false;
		}
		return true;
	}
} 