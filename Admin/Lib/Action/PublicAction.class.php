<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 公共模块,不需要做登陆判断

    @Filename PublicAction.class.php $

    @Author pengyong $

    @Date 2012-11-04 16:43:06 $
*************************************************************/
class PublicAction extends Action
{
	public function login()
	{
		$this->display();
	}
	
	public function checklogin()
	{
		if(strtolower($_SESSION['verify']) <> strtolower($_POST['verify']) && C('SOFT_VERIFY')<>1) 
		{
			$this->error('验证码错误！');
		}
		$model = M('admin');
		$data['name'] = trim($_POST['username']);
		$list = $model->where($data)->find();
		if(!$list)
		{
			$this->error('用户名不存在!');
		}
		if(strcmp(xmd5($_POST['password']),$list['password'])<>0)
		{
			$this->error('用户密码错误!');
		}
		cookie("uid", $list['id'],3600*12);
		cookie("uname", $list['name'],3600*12);
		$str = 'waikucms';
		session('cmsauth',substr(md5(strrev($list['name']).$str.$list['id']),0,10)); 
		$data['id'] = $list['id'];
		
		$data['loginip'] = get_client_ip();
		
		$data['logintime'] = time();
		
		$model->save($data);
		
		$this->success('登陆成功!正在进入系统~~',U('Index/index'));
	}
	public function loginout()
	{
		cookie('uname',null);
		cookie('uid',null);
		session('cmsauth',null); 
		cookie('safeauthset',null);
		$this->success('登出成功!',U('Public/login'));
	}
		
	public function verify()
	{
		import("ORG.Verify");
		$verify = new Verify();
		$verify->display();
	}
	
	public function locking()
	{
		if(IS_POST==false)
		{
			$lockingid = cookie('lockingid');
			if(empty($lockingid)) cookie('lockingid',cookie('uid'),3600*12);
			cookie('uid',null);
			$this->display();
		}
		else
		{
			cookie('uid',cookie('lockingid'),3600*12);
		}
	}
	
	public function lockingverify()
	{
		$session = session('cmsauth');
		$cookie = cookie('lockingid');
		$uname = cookie('uname');
		//状态码2:登陆信息丢失;状态码0:解锁验证失败;状态码1:解锁成功
		if(empty($session) or empty($cookie) or empty($uname)) die('2');
		$pwd = $_POST['pwd'];
		$model = M('admin');
		$password = $model->where('id='.$cookie)->getField('password');
		if(strcmp(xmd5($pwd),$password)==0)die('1');
		echo 0;die(); 
	}
	//安全密码设置
	public function dosafeauthset()
	{
		$session = session('cmsauth');
		$uid = cookie('uid');
		$uname = cookie('uname');
		if(empty($session) or empty($uid) or empty($uname)) die('2');
		if(!empty($_POST['auth']))
		{
			F('safeauth',xmd5($_POST['auth']),COMMON_PATH);
			die('1');
		}
		echo 0;die();
	}
	//安全密码验证
	public function checksafeauthset()
	{
		$session = session('cmsauth');
		$uid = cookie('uid');
		$uname = cookie('uname');
		if(empty($session) or empty($uid) or empty($uname)) die('2');
		$safeauth = F('safeauth','',COMMON_PATH);
		if(strcmp(xmd5($_POST['auth']),$safeauth)==0)
		{
			$timeout = (int)$_POST['timeout'];
			$timeout = $timeout==1? 1: $timeout*60;
			cookie('safeauthset',1,$timeout);
			die('1');
		}
		echo 0;die();
	}
	//uploadify 单独上传验证
	public function doupload()
	{
		if(xmd5(C('COOKIE_PREFIX')) <> $_POST['uploadify'])
		{
			echo 0; die();
		}
		$dirname = isset($_GET['dirname']) ? $_GET['dirname']:'';
		$savePath = empty($dirname) ? './':'./'.trim($dirname,'/').'/';
		//处理文件名,获取原始文件名
		$filename = $_FILES['file_upload']['name'];
		import('ORG.UploadFile');
		$upload = new UploadFile(); 
		$upload->savePath = $savePath;
		$upload->saveRule = $filename;
		$upload->uploadReplace = true; 
		if($upload->upload())
		{
			echo 1;
		}
		else
		{
			echo 0;
		}
	}
}