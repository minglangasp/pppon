<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 文章内链管理

    @Filename ArticleLinkAction.class.php $

    @Author pengyong $

    @Date 2013-01-22 16:51:56 $
*************************************************************/
class ArticleLinkAction extends CommonAction
{	
    public function index()
    {
		$model = M('articlelink');
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
		$articlelink = M('articlelink');
		$articlelink->create(); 
		$articlelink->add();
		$this->success('操作成功!',U('ArticleLink/index'));
    }
	
	public function edit()
    {
		$articlelink = M('articlelink');
		$list = $articlelink->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
        $this->display();
    }
	
	public function doedit()
    {
		$articlelink = M('articlelink');
		$articlelink->create();
		$articlelink->save();
		$this->success('操作成功!',U('ArticleLink/index'));
    }
	
	public function del()
    {
		$type = M('articlelink');
		$type->where('id='.$_GET['id'])->delete();
		$this->success('操作成功!',U('ArticleLink/index'));
		
    }
	
	public function delall()
	{
		$id = $_REQUEST['id'];  //获取id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id)?$ids:$id;
		$map['id'] = array('in',$id); 
		$articlelink = M('articlelink');
		if($_REQUEST['Del'] == '编辑')
		{ 
			for($i = 0;$i < count($_REQUEST['keyid']);$i++)
			{
				$data['url'] = $_REQUEST['url'][$i];
				$data['rank'] = $_REQUEST['rank'][$i];
				$articlelink->where('id='.$_REQUEST['keyid'][$i])->save($data);
			}
			$this->success('操作成功!',U('ArticleLink/index'));
		}
		
		if(!$id) $this->error('请勾选记录!');
		
		if($_REQUEST['Del'] == '删除')
		{ 
			$articlelink->where($map)->delete();
			$this->success('操作成功!',U('ArticleLink/index'));
		}
	}
}
?>