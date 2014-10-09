<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 管理员管理

    @Filename AdminAction.class.php $

    @Author pengyong $

    @Date 2013-01-07 00:46:20 $
*************************************************************/
class AdminAction extends CommonAction
{	
    public function index()
    {
		$model = M('admin');
		$list = $model->select();
		$this->assign('list',$list);
		$this->display('index');
    }

	public function add()
    {
        $this->display();
    }

	public function doadd()
    {
		$model = M('admin');
		$data['name'] = trim($_POST['username']);
		if($model->where($data)->find()) $this->error('用户名已存在!');
		if(empty($_POST['username']) || empty($_POST['password'])) $this->error('用户或密码不能为空!');
		$data['logintime'] = time();
		$data['loginip'] = get_client_ip();
		$data['password'] = xmd5(trim($_POST['password']));
		$model->add($data);
		$this->success('操作成功!密码为:'.$_POST['password'],U('Admin/index'));
    }

	//修改管理员
	public function edit()
    {
		$model = M('admin');
		$data['id'] = (int)$_GET['id'];
		$list = $model->where($data)->find();
		if(!$list) $this->error('用户不存在!');
		$this->assign('list',$list);
        $this->display();
    }
	
	public function doedit()
    {
		$model = M('admin');
		$data['name'] = trim($_POST['username']);
		$data['id'] = $_POST['id'];
		$data['password'] = xmd5(trim($_POST['password']));
		//同步默认管理员帐号至会员帐户
		if($data['id']==1)
		{
			$map['id'] = $data['id'];
			$map['password'] = $data['password'];
			$map['username'] = $data['name'];
			$member = M('member');
			$member->save($map);
		}
		$model->save($data);
		$this->success('操作成功! 新密码:'.$_POST['password'],U('Admin/index'));
    }
	
	public function del()
    {
		$model = M('admin');
		$data['id'] = (int)$_GET['id'];
		if($data['id']==1) $this->error('默认管理员帐号不允许删除!');
		$list = $model->where($data)->find();
		if($list)
		{
			if($list['name']==cookie('uname')) $this->error('不能删除自己');
		}
		else
		{
			$this->error('用户不存在!');
		}
		$model->where($data)->delete();
		$this->success('操作成功!',U('Admin/index'));
    }
	
	public function ajax()
	{
		$map['name'] = $this->_post('username');
		$model = M('admin');
		if($model->where($map)->find())  die('1');
		die('0');
	}
}
?>