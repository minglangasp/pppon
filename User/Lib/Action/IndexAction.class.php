<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function User组 用户中心

    @Filename IndexAction.class.php $

    @Author pengyong $

    @Date 2013-01-11 17:01:32 $
*************************************************************/
class IndexAction extends CommonAction 
{
    public function index()
	{
		import('@.ORG.Page');
		$flag = $this->_get('flag',false);
		$map['arcrank'] = array('in','1,2,3');
		if(!empty($flag))  $map['flag'] = array('like','%'.$flag.'%');
		$model = D('ArchiveView');
		$count = $model->where($map)->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->field('litpic,id,typeid,modelid,arcrank,title,flag,color,click,pubdate,mid,username,description')->where($map)->limit($p->firstRow.','.$p->listRows)->order('pubdate desc')->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display();
	}
	
	public function myfile()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		global $member;
		$this->assign('list',$member);
		//用户等级查询
		$group = M('member_rank');
		$grouplist = $group->where(array('id'=>$member['rankid']))->find();
		$this->assign('grouplist',$grouplist);
		$this->assign('remail',cookie('remail'));
		$edit = $this->_get('edit',false);
		if($edit=='base')
		{
			$tpl = 'editmyfile';
		}
		elseif($edit=='password')
		{
			$tpl ='repassword';
		}
		else
		{
			$tpl = 'myfile';
		}
		$member['status']==1 ? $this->display('status'):$this->display($tpl);
	}
	
	public function doeditmyfile()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		if(strtolower($_SESSION['verify']) <> strtolower($_POST['verify']) && C('SOFT_VERIFY')<>1)  $this->error('验证码错误！');
		$map['sex'] = $this->_post('sex',false);
		$map['province'] = $this->_post('province',false);
		$map['city'] = $this->_post('city',false);
		$map['qq'] = $this->_post('qq',false);
		$map['id'] = $this->_post('id',false);
		$avtar = $this->_post('avtar',false);
		if(!empty($avtar)) $map['avtar'] = $avtar;
		$model = M('member');
		$model->save($map);
		$this->success('操作成功!',U('Index/myfile'));
	}
	public function dorepassword()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		if(strtolower($_SESSION['verify']) <> strtolower($_POST['verify']) && C('SOFT_VERIFY')<>1)  $this->error('验证码错误！');
		$map['id'] = cookie('uid');
		$model = M('member');
		$list = $model->field('password')->where($map)->find();
		if(!$list)jump(U('Public/login')); 
		$repassword = $this->_post('repassword',false);
		$password = $this->_post('password',false);
		if(strcmp($password,$repassword) <> 0 ) $this->error('确认密码与密码不一致!');
		if($map['id'] == 1) $this->error('尊贵的超级管理员,请登录管理后台修改帐户密码信息!');
		if(strcmp(xmd5(trim($_POST['oldpassword'])),$list['password']) <>0) $this->error('原始密码不正确!');
		$map['password'] = xmd5($repassword);
		$model->save($map);
		cookie('uid',null);
		cookie('uname',null);
		cookie('wkcode',null);
		$this->success('操作成功,请重新登陆!',U('Public/login'));
	}
	
	public function msg()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		global $member;
		import('@.ORG.Page');
	    $map['toid'] = $member['id'];
		$map['tostatus'] = array('lt',2);
		$model = M('member_msg');
		$count = $model->where($map)->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->where($map)->limit($p->firstRow.','.$p->listRows)->order('pubdate desc')->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display('mymsg');
	}
	
	public function msg_put()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		global $member;
		import('@.ORG.Page');
	    $map['fromid'] = $member['id'];
		$map['fromstatus'] = array('lt',1);//0:已发送,1:删除
		$model = M('member_msg');
		$count = $model->where($map)->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->where($map)->limit($p->firstRow.','.$p->listRows)->order('pubdate desc')->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display('msg_put');
	}
	
	public function msg_send()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		$this->display('msg_send');
	}
	
	public function domsg_send()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		if(strtolower($_SESSION['verify']) <> strtolower($_POST['verify']) && C('SOFT_VERIFY')<>1)  $this->error('验证码错误！');
		global $member;
		$model = M('member');
		$list = $model->where(array('username'=>trim($_POST['username'])))->find();
		if(!$list) $this->error('用户名不存在!');
		if($list['id'] == $member['id']) $this->error('不能发送给自己!');
		$map['title'] = $this->_post('title',false);
		$map['msg'] = htmlspecialchars(stripslashes($_POST['msg']));
		$map['fromid'] = $member['id'];
		$map['fromstatus'] = 0;//已发送
		$map['tostatus'] = 1;//未读
		$map['toid'] = $list['id'];
		$map['pubdate'] = time();
		$msg = M('member_msg');
		$msg->add($map);
		$this->success('操作成功!',U('Index/msg_put'));
	}
	
	public function ajax()
	{
		if(!USER_LOGINED) die();
		global $member;
		$method = $this->_get('method',false);
		if($method=='put')
		{
			$model = M('member_msg');
			$map['id'] = $this->_post('id');
			$map['fromstatus'] = 1;
			$model->save($map);
		}
		elseif($method=='get')
		{
			$model = M('member_msg');
			$map['id'] = $this->_post('id');
			$map['tostatus'] = 2;
			$model->save($map);
		}
	}
	
	public function msg_read()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		global $member;
		$map['id'] = (int)$_GET['id'];
		$model  = M('member_msg');
		$list = $model->where($map)->find();
		if(!$list) $this->error('数据不存在!');
		if($list['fromid']<> $member['id'] && $list['toid']<> $member['id']) $this->error('非法查询~');
		$model2 = M('member');
		$f1 = $model2->field('username')->where(array('id'=>$list['fromid']))->find();
		$f2 = $model2->field('username')->where(array('id'=>$list['toid']))->find();
		$list['fromusername'] = $f1['username'];
		$list['tousername'] = $f2['username'];
		$this->assign("list",$list);
		//自动标记为已读
		if($list['toid'] ==$member['id'] && $list['tostaus']==0)
		{
			$data['tostatus'] = 0;
			$data['id'] = $list['id'];
			$model->save($data);
		}
		$this->display();
	}
	//批处理
	public function delall()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		$method = $this->_get('method',false);
		global $member;
		if($method=='put')
		{
			//批量操作
			$id = $_REQUEST['id'];  //获取文章aid
			$ids = implode(',',$id);//批量获取aid
			$id = is_array($id) ? $ids : $id;
			$map['id'] = array('in',$id);
			if(!$id)$this->error('请勾选记录!');
			$from = empty($_SERVER['HTTP_REFERER']) ? U('Index/msg'):$_SERVER['HTTP_REFERER'];
			$model = M('member_msg');
			if($_REQUEST['Del'] == '删除')
			{
				$data['fromstatus'] = 1;
				$model->where($map)->save($data);
				$this->success('操作成功!',$from);
			}
		}
		elseif($method=='get')
		{
			//批量操作
			$id = $_REQUEST['id'];  //获取文章aid
			$ids = implode(',',$id);//批量获取aid
			$id = is_array($id) ? $ids : $id;
			$map['id'] = array('in',$id);
			if(!$id)$this->error('请勾选记录!');
			$from = empty($_SERVER['HTTP_REFERER']) ? U('Index/msg'):$_SERVER['HTTP_REFERER'];
			$model = M('member_msg');
			if($_REQUEST['Del'] == '删除')
			{
				$data['tostatus'] = 2;
				
			}
			elseif($_REQUEST['Del'] == '未读')
			{
				$data['tostatus'] = 1;
			}
			elseif($_REQUEST['Del'] == '已读')
			{
				$data['tostatus'] = 0;
			}
			$model->where($map)->save($data);
			$this->success('操作成功!',$from);
		}
	}
}