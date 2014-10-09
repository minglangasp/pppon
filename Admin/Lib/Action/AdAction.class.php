<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 广告管理

    @Filename AdAction.class.php $

    @Author pengyong $

    @Date 2011-11-23 09:59:26 $
*************************************************************/
class AdAction extends CommonAction
{	
    public function index()
    {
		import('@.ORG.Page');
		$map['id'] = array('gt',0);
		$gid = $this->_get('gid');
		$title = $this->_get('title');
		if(isset($_GET['status'])) $map['status'] = $_GET['status'];
		if(!empty($gid)) $map['gid'] = $gid;
		if(!empty($title)) $this->assign('title',urldecode($title));
		$model = D('AdView');
		$fenye = 20;
		$count = $model->where($map)->count();
		$p = new Page($count,$fenye);
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$list = $model->where($map)->order('pubdate desc')->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->grouplist();
		$this->display();	
    }
	
	private function grouplist()
	{
		$model = M('ad_group');
		$this->assign('grouplist',$model->select());
	}
	
	public function add()
    {
		$model = M('ad_group');
		if(!$model->find()) $this->error('请先创建广告分类!');
		$this->grouplist();
		$this->display('add');
    }
	
	public function groupadd()
	{
		$this->display();
	}
	
	
	public function dogroupadd()
	{
		$map['name'] = $this->_post('name');
		$model  = M('ad_group');
		if($model->where($map)->find())$this->error('广告分类名称已存在!');
		$model->add($map);
		$this->tosuccess();
	}
	
	public function groupedit()
	{
		$map['id'] = $this->_get('id');
		$model = M('ad_group');
		$list = $model->where($map)->find();
		if(!$list) $this->error('广告分类不存在!');
		$this->assign('list',$list);
		$this->display();
	}
	
	
	public function dogroupedit()
	{
		$map['name'] = $this->_post('name');
		$map['id'] = $this->_post('id');
		$model  = M('ad_group');
		if($model->where($map)->find())$this->error('广告分类名称已存在!');
		$model->save($map);
		$this->tosuccess();
	}
	
	public function dogroupdel()
	{
		$map['gid'] = $this->_get('id');
		if($map['gid']==1) $this->error('默认分类不能删除!');
		$model = M('ad');
		if($model->where($map)->find()) $this->error('当前分类下存在广告!');
		$group = M('ad_group');
		$group ->where(array('id'=>$map['gid']))->delete();
		$this->tosuccess();
	}
	
	public function edit()
    {
		$model = M('ad');
		$map['id']  = $this->_get('id');
		$list = $model->where($map)->find();
		$this->assign('list',$list);
		$this->grouplist();
		$this->display();
    }
	
	public function doedit()
    {
		$model = M('ad');
		$data = $this->getdata('edit');
		$model->save($data);
		$this->tosuccess();
    }
	
	public function doadd()
    {
		$model = M('ad');
		$data = $this->getdata('add');
		$model->add($data);
		$this->tosuccess();
    }
	
	private function getdata($method='add')
	{
		if($method=='edit') $data['id'] = $this->_post('id');
		$data['title'] = $this->_post('title');
		//使用stripslashes 反转义,防止服务器开启自动转义
		$data['content'] = stripslashes($_POST['content']);
		$data['pubdate'] = time();
		$data['gid'] = $this->_post('gid');
		$data['status'] = $this->_post('status');
		return $data;
	}
	
	public function del()
    {
		$model = M('ad');
		$map['id'] = $this->_get('id');
		if(!$model->where($map)->find()) $this->error('ID不存在!');
		$model->where($map)->delete();
		$this->tosuccess();
    }
	
	public function status()
	{
		$model = M('ad');
		$map['id'] = $this->_get('id');
		$list = $model->field('status')->where($map)->find();
		$list['status']==0 ? $model->where($map)->setField('status',1) : $model->where($map)->setField('status',0);
		$this->tosuccess();
	}

	
	public function delall()
	{
		$id = $_REQUEST['id']; 
		$ids = implode(',',$id);
		$id = is_array($id) ? $ids : $id;
		$map['id'] = array('in',$id); 
		if(!$id) $this->error('请勾选数据!');
		$model = M('ad');
		if(!isset($_REQUEST['Del'])) return;
		if($_REQUEST['Del'] == '显示') $data['status'] = 1;
		if($_REQUEST['Del'] == '隐藏') $data['status'] = 0;
		$model->where($map)->save($data);
		$this->tosuccess();
	}
	
	private function tosuccess($model ='Ad/index')
	{
		$from  =  !empty($_POST['_from']) && $_GET['from']<>'frame' ? $_POST['_from'] : U($model) ;
		$this->success('操作成功!',$from);
	}
	
	public function js()
	{
		$map['id'] = $this->_get('id');
		$map['status'] = 1;
		$model = M('ad');
		$list = $model->where($map)->find();
		if(!$list) $this->error('广告不存在或被隐藏!');
		$this->assign('list',$list);
		$jscode  ='<script language=\'javascript\' charset=\'utf-8\' src=\'http://'.$_SERVER['SERVER_NAME'].url('ad',$map['id']).'\'></script>';
		$tagcode = htmlspecialchars('<wk:myad id=\''.$map['id'].'\'><field name=\'content\'/></wk:myad>');
		$this->assign('jscode',$jscode);
		$this->assign('tagcode',$tagcode);
		$this->display();
	}
}
?>