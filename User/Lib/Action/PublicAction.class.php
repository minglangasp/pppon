<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function User组 会员公共模块

    @Filename PublicAction.class.php $

    @Author pengyong $

    @Date 2013-01-11 15:37:50 $
*************************************************************/
class PublicAction extends CommonAction 
{
    public function index()
	{
		jump(U('Index/index'));
	}
	
	public function reg()
	{
		if(USER_LOGINED) $this->error('请先登出~');
		$this->display();
	}
	
	public function doreg()
	{
		global $cfg_basehost,$cfg_indexurl,$cfg_mb_allowreg,$cfg_mb_notallow,$cfg_mb_idmin,$cfg_mb_pwdmin,$cfg_md_mailtest,$cfg_mb_spacesta,$cfg_webname,$cfg_money_reg;
		if($cfg_mb_allowreg==1) $this->error('系统暂停新用户注册!');
		if(strtolower($_SESSION['verify']) <> strtolower($_POST['verify']) && C('SOFT_VERIFY')<>1)  $this->error('验证码错误！');
		$map['username'] = trim($_POST['username']);
		$map['password'] = trim($_POST['password']);
		$map['email'] = trim($_POST['email']);
		$map['sex'] = trim($_POST['sex']);
		$map['birthday'] = trim($_POST['birthday']);
		$map['province'] = trim($_POST['province']);
		$map['city'] = trim($_POST['city']);
		$map['qq'] = trim($_POST['qq']);
		$map['avtar'] = '';	
		if(!empty($cfg_mb_notallow))
		{
			$pattern = explode(',',$cfg_mb_notallow);
			if(in_array($map['username'],$pattern)) $this->error('当前用户名不允许注册,请更换!');
		}
		if(strlen($map['username']) < $cfg_mb_idmin) $this->error('用户帐号最小长度为:'.$cfg_mb_idmin);
		if(strlen($map['password']) < $cfg_mb_pwdmin) $this->error('用户密码最小长度为:'.$cfg_mb_pwdmin);
		if(strcmp($map['password'],trim($_POST['repassword'])) <> 0) $this->error('密码和确认密码不一致!');
		$map['password'] = xmd5($map['password']);
		$model = M('member');
		//检查用户名是否已经注册 2013-4-12 21:29:59
		if($model->where(array('username'=>$map['username']))->find()) $this->error('当前用户名已经注册！');
		if($cfg_md_mailtest==0 && $model->where(array('email'=>$map['email']))->find()) $this->error('email:'.$map['email'].'&nbsp;已经注册过了!');
		$map['status'] = $cfg_mb_spacesta==2 ? 0:1;
		$map['regtime'] = time();
		$map['logintime'] = time();
		$map['loginip'] = get_client_ip();
		//注册赠送积分
		$map['money'] = (int)$cfg_money_reg;
		$map['rankid'] = 1;
		$map['activekey'] = xmd5($map['username'].$map['id'],5);
		$model->add($map);
		//模拟登陆
		$list = $model->where(array('username'=>$map['username']))->find();
		if($cfg_mb_spacesta==0)
		{
			$activeurl = $cfg_basehost.$cfg_indexurl.'/user.php?m=Public&a=active&key='.$list['activekey'];
			$pubdate = date('Y-m-d H:i:s');
			$content =<<<DATA
				<div style="padding: 30px">
				<div>
				<p>您好，<b>{$list['username']}</b> ：</p>
				</div>
				<div style="margin: 6px 0 60px 0;">
				<p>欢迎加入<strong>{$cfg_webname}</strong>！请点击下面的链接来激活您的帐户信息。</p>
				<p><a href="{$activeurl}">{$activeurl}</a></p>
				<p>如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中认证。</p>
				</div>
				<div style="color: #999;">
				<p>发件时间：{$pubdate}</p>
				<p>此邮件为系统自动发出的，请勿直接回复。</p>
				</div>
				</div>
DATA;
			import('ORG.Mail');
			SendMail($list['email'],$cfg_webname.":用户激活邮件",$content,$cfg_webname);
		}
		cookie('uid',$list['id'],time()+3600);
		session('uid',$list['id']);
		cookie('uname',$list['username'],time()+3600);
		cookie('wkcode',xmd5($list['id'].$list['username'],3));
		$this->success('注册成功!',U('Index/myfile'));
	}

	public function active()
	{
		$map['activekey'] = $this->_get('key',false);
		if(empty($map['activekey'])) return;
		$model = M('member');
		$list = $model->where($map)->find();
		if(!$list) $this->error('帐户信息不存在!');
		if($list['status']==1)
		{
			$map['status'] = 0;
			$map['id'] = $list['id'];
			$model->save($map); 
		}
		$this->success('帐户信息已成功激活!',U('Index/myfile'));
	}
	
	public function reemail()
	{
		$map['id']  = $this->_get('mid',false);
		$model = M('member');
		$list = $model->where($map)->find();
		if(!$list) $this->error('用户信息不存在!');
		if($list['status']==0) $this->success('用户已经激活了~',U('Index/myfile'));
		if($GLOBALS['cfg_mb_spacesta']<>0)  $this->error('当前系统采用人工审核方式激活帐户!');
		global $cfg_basehost,$cfg_indexurl,$cfg_webname;
		$activeurl = $cfg_basehost.$cfg_indexurl.'/user.php?m=Public&a=active&key='.$list['activekey'];
			$pubdate = date('Y-m-d H:i:s');
			$content =<<<DATA
				<div style="padding: 30px">
				<div>
				<p>您好，<b>{$list['username']}</b> ：</p>
				</div>
				<div style="margin: 6px 0 60px 0;">
				<p>欢迎加入<strong>{$cfg_webname}</strong>！请点击下面的链接来激活您的帐户信息。</p>
				<p><a href="{$activeurl}">{$activeurl}</a></p>
				<p>如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中认证。</p>
				</div>
				<div style="color: #999;">
				<p>发件时间：{$pubdate}</p>
				<p>此邮件为系统自动发出的，请勿直接回复。</p>
				</div>
				</div>
DATA;
			import('ORG.Mail');
			SendMail($list['email'],$cfg_webname.":用户激活邮件",$content,$cfg_webname);
			jump(U('Index/myfile?remail=1'));
	}
	public function login()
	{
		if(USER_LOGINED)  $this->error('您已经登陆过了~');
		$this->display();
	}
	
	public function dologin()
	{
		if(strtolower($_SESSION['verify']) <> strtolower($_POST['verify']) && C('SOFT_VERIFY')<>1)  $this->error('验证码错误！');
		$map['username'] = trim($_POST['username']);
		$model = M('member');
		$list = $model->where($map)->find();
		if(!$list) $this->error('用户信息不存在!');
		$map['password'] = trim($_POST['password']);
		if(strcmp(xmd5($map['password']),$list['password']) <> 0) $this->error('密码不正确!');
		//更新用户信息 patch 2013-4-12 21:50:43
		$model->where('id='.$list['id'])->setField(array('logintime'=>time(),'loginip'=>get_client_ip()));
		//更新cookie
		cookie('uid',$list['id'],time()+3600);
		session('uid',$list['id']);
		cookie('uname',$list['username'],time()+3600);
		cookie('wkcode',xmd5($list['id'].$list['username'],3));
		$url = !empty($_POST['fromurl']) ? $_POST['fromurl']:U('Index/myfile');
		$this->success('登陆成功!',$url);
	}
	
	public function loginout()
	{
		cookie('uid',null);
		cookie('uname',null);
		cookie('wkcode',null);
		$url = !empty($_GET['fromurl']) ? $_GET['fromurl']:U('Public/login');
		$this->success('登出成功!',$url);
	}
	
	public function verify()
	{
		import("ORG.Verify");
		$verify = new Verify();
		$verify->display();
	}

}