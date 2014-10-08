<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 会员管理

    @Filename MemberAction.class.php $

    @Author pengyong $

    @Date 2012-12-27 17:02:56 $
*************************************************************/
class MemberAction extends Action
{
	public function index()
	{
		if(isset($_GET['status']))  $map['status'] = $this->_get('status');
		if(isset($_GET['logintime'])) $map['logintime'] = array('egt',time() - $_GET['logintime'] * 3600);
		if(isset($_GET['regtime'])) $map['regtime'] = array('egt',time() - $_GET['regtime'] * 3600);
		if(isset($_GET['rankid'])) $map['rankid'] = $this->_get('rankid');
		if(isset($_POST['username']))
		{
			//模糊搜索标题
			if(!empty($_POST['username']))
			{
				$username = explode(' ',$_POST['username']);
				foreach($username as $v)
				{
					$map['username'][] = array('like','%'.$v.'%');
				}
				$map['username'][] = 'or';
			}
		}
		import('@.ORG.Page');
		$model = D('MemberView');
		$count = $model->where($map)->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model->getLastSql();exit;
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>");
		$this->assign('page',$p->show());
		$this->assign("list",$list);
		$this->display();
	}
	
	public function add()
	{
		$model = M('member_rank');
		$ranklist = $model->field('id,rankname,rankmoney,groupid')->select();
		$this->assign("ranklist",$ranklist);
		$this->display();
	}
	
	public function doadd()
	{
		$model = M('member');
		$map = $this->getdata('username,password,sex,status,birthday,province,city,money,email,qq,rankid,avtar');
		if(empty($map['avtar'])) $map['avtar'] = __ROOT__.'/Public/User/img/avtar_big.jpg';
		$map['logintime'] = time();
		$map['regtime'] = time();
		$map['loginip'] = get_client_ip();
		$map['password'] = xmd5($map['password']);
		if($model->where("username='".$map['username']."'")->find()) $this->error('会员名称已存在!');
		$model->add($map);
		$this->success('操作成功!',U('Member/index'));
	}
	
	public function edit()
	{
		$map['id'] = $this->_get('id');
		$model = M('member');
		$list = $model->where($map)->find();
		if(!$list) $this->error('查询不到数据,请检查!');
		$this->assign("list",$list);
		$rankmodel = M('member_rank');
		$ranklist = $rankmodel->field('id,rankname,rankmoney,groupid')->select();
		$this->assign("ranklist",$ranklist);
		$this->display();
	}
	
	public function doedit()
	{
		$map = $this->getdata('id,username,password,sex,birthday,status,city,province,money,email,qq,rankid,avtar');
		$model = M('member');
		$list = $model->where(array('id'=>$map['id']))->find();
		if(!$list) $this->error('查询不到数据,请检查!');
		$list2 = $model->where(array('username'=>$map['username']))->find();
		if($list2 && $list2['username']<>$list['username']) $this->error('当前用户名已被注册使用!');
		$map['password'] = empty($map['password']) ? $list['password'] : xmd5($map['password']);
		if(empty($map['avtar'])) $map['avtar'] = __ROOT__.'/Public/User/img/avtar_big.jpg';
		$model->save($map);
		//积分变动则更新积分等级
		$this->doupdaterank($list['id']);
	}
	
	//积分归零
	public function clearmoney()
	{
		$model = M('member');
		$map['money'] = 0;
		$model->where('id>0')->save($map);
		$this->success('操作成功!',U('Member/index'));
	}
	//短消息清理
	public function clearmsg()
	{
		$method = isset($_GET['method']) ? $_GET['method']:'';
		$model = M('member_msg');
		$map['id'] = array('gt',0);
		if($method <> 'all')
		{
			$map['fromstatus'] = 1;
			$map['tostatus'] = 2;
		}
		$model->where($map)->delete();
		$this->success('操作成功!',U('Member/index'));
	}
	
	//更新用户等级
	public function doupdaterank($id=0)
	{
		$beginid = $this->_post('beginID');
		$endid = $this->_post('endID');
		$map['id'] = array(array('egt',$beginid),array('elt',$endid));
		if($id >0) $map['id'] = $id;
		$model = M('Member');
		$list = $model->where($map)->select();
		$rank = M('member_rank');	
		foreach($list as $k=>$v)
		{
			$rmlist = $rank->field('groupid')->where(array('id'=>$v['rankid']))->find();
			if($rmlist['groupid']==1)
			{
				$data['rankmoney'] = array('gt',$v['money']);
				$rlist = $rank->field('id')->where($data)->order('id asc')->find();
				$map2['rankid'] = $rlist ? $rlist['id']-1: 1;
				$map2['id'] = $v['id'];
				$model->save($map2);
			}
		}
		$this->success("操作成功!",U('Member/index'));
	}
	//审核
	public function status()
	{
		$map['id'] = $this->_get('id',false);
		if($map['id']==1) $this->error('超级管理员帐户禁止修改状态!');
		$model =M('member');
		$list = $model->field('status')->where($map)->find();
		if(!$list)$this->error('查询不到数据,请检查!');
		$map['status'] = $list['status']==0 ? 1:0;
		$model->save($map);
		$this->success("操作成功!",U('Member/index'));
	}
	
	public function rank()
	{
		if(isset($_GET['groupid'])) $map['groupid'] = $this->_get('groupid');
		if(isset($_POST['rankname']))
		{
			//模糊搜索等级名称
			if(!empty($_POST['rankname']))
			{
				$rankname = explode(' ',$_POST['rankname']);
				foreach($rankname as $v)
				{
					$map['rankname'][] = array('like','%'.$v.'%');
				}
				$map['rankame'][] = 'or';
			}
		}
		$model = M('member_rank');
		if(empty($map)) $map['id'] = array('gt',0);
		$list = $model->where($map)->select();
		$this->assign("list",$list);
		$this->display();
	}
	
	public function dorank()
	{
		$model = M('member_rank');
		for($i=0;$i< count($_POST['id']);$i++)
		{
			$map['id'] = $_POST['id'][$i];
			$map['rankname'] = $_POST['rankname'][$i];
			$map['rankmoney'] = $_POST['rankmoney'][$i];
			$map['rankimg'] = $_POST['rankimg'][$i];
			$model->save($map);
		}
		$this->success("操作成功!",U('Member/rank'));
	}
	
	public function addrank()
	{
		$this->display();
	}
	
	public function del()
	{
		$map['id'] = $this->_get('id',false);
		if($map['id']==1) $this->error('管理员关联帐户,禁止删除!');
		$model = M('member');
		$model->where($map)->delete();
		$this->success('操作成功!',U('Member/index'));
	}
	public function doaddrank()
	{
		$map = $this->getdata('groupid,rankname,rankmoney,rankimg'); 
		$model = M('member_rank');
		$model->add($map);
		$this->success("操作成功!",U('Member/rank'));
	}
	
	private function getdata($data)
	{
		$data = explode(',',$data);
		$map = array();
		foreach($data as $v)
		{
			$map[$v] = $_POST[$v];
		}
		return $map;
	}
	public function ajax()
	{
		$map['username'] = $this->_post('username',false);
		$model = M('member');
		if($model->where($map)->find())  die('1');
		die('0');
	}
}