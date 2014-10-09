<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 Tag关键字

    @Filename TagAction.class.php $

    @Author pengyong $

    @Date 2013-01-22 16:51:56 $
*************************************************************/
class TagAction extends CommonAction
{	
    public function index()
    {
		$model = M('tags');
		$count = $model->count();
		import('@.ORG.Page');
		$p = new Page($count,20);
		$p->setConfig('prev','上一页'); 
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%
		<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>");
		$this->assign('page',$p->show());
		$list = $model->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('list',$list);
		$this->display('index');	
    }
	
	public function add()
    {
        $this->display('add');
    }
	
	public function doadd()
    {
		$tags = M('tags');
		$tags->create(); 
		$tags->add();
		$this->success('操作成功!',U('Tag/index'));
    }
	
	public function edit()
    {
		$tags = M('tags');
		$list = $tags->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
        $this->display();
    }
	
	public function doedit()
    {
		$tags = M('tags');
		$tags->create();
		$tags->save();
		$this->success('操作成功!',U('Tag/index'));
    }
	
	public function del()
    {
		$type = M('tags');
		$type->where('id='.$_GET['id'])->delete();
		$this->success('操作成功!',U('Tag/index'));
		
    }
	
	public function delall()
	{
		$id = $_REQUEST['id'];  //获取id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id)?$ids:$id;
		$map['id'] = array('in',$id); 
		$tags = M('tags');
		if(!$id) $this->error('请勾选记录!');
		
		if($_REQUEST['Del'] == '删除')
		{ 
			$tags->where($map)->delete();
			$this->success('操作成功!',U('Tag/index'));
		}
	}
}
?>