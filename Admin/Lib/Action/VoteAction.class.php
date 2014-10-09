<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
	
    @function:Admin组 投票管理

    @Filename VoteAction.class.php $

    @Author pengyong $

    @Date 2011-11-23 10:33:32 $
*************************************************************/
class VoteAction extends CommonAction
{	
     public function index()
    {
		import('@.ORG.Page');
		$model = M('vote');
		$count = $model->count();
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
		$this->display();	
    }
	
	public function add()
    {
        $this->display();
    }
	
	public function doadd()
    {
		$data = 'title,status,content,type,starttime,overtime';
		$map = $this->getdata($data);
		if($map['overtime'] < $map['starttime']) $this->error('结束时间不得小于开始时间!');
		$map['starttime'] = $this->parsetime($map['starttime']);
		$map['overtime'] = $this->parsetime($map['overtime']);
		$model=M('vote');
		$model->add($map);
		$this->success('操作成功!',U('Vote/index'));
    }

	   public function edit()
    {
		$vote = M('vote');
		$list = $vote->where('id='.$_GET['id'])->find();
		$this->assign('list',$list);
        $this->display();
    }
	
	public function doedit()
    {
		$data = 'id,title,status,content,type,starttime,overtime';
		$map = $this->getdata($data);
		if($map['overtime'] < $map['starttime']) $this->error('结束时间不得小于开始时间!');
		$map['starttime'] = $this->parsetime($map['starttime']);
		$map['overtime'] = $this->parsetime($map['overtime']);
		$model = M('vote');
		$model->save($map);
		$this->success('操作成功!',U('Vote/index'));
    }
	
	private function getdata($data)
	{
		$map = array();
		$data = explode(',',$data);
		foreach($data as $v)
		{
			$map[$v] = trim($_POST[$v]);
		}
		return $map;
	}
	
	//时间解析处理,返回时间戳
   private function parsetime($str)
   {
		$str =  explode(" ",$str);
		$ymd = explode("-",$str[0]);
		$his = explode(":",$str[1]);
		return mktime($his[0], $his[1], $his[2], $ymd[1], $ymd[2], $ymd[0]);
   }
	
	public function del()
    {
		$model = M('vote');
		$map['id'] = $_GET['id'];
		if(!$model->where($map)->find()) $this->error('数据不存在!');
		$model->where($map)->delete();
		$this->success('操作成功!',U('Vote/index'));	
    }
	
	public function status()
	{
		$model = M('vote');
		$map['id'] = $_GET['id'];
		$list = $model->where($map)->find();
		if(!$list) $this->error('数据不存在!');
		$data = $list['status']==0 ? 1:0;
		$model->where($map)->setField('status',$data); 
		$this->redirect('index');
	}
	
	public function delall()
	{
		$id = $_REQUEST['id'];  //获取id
		$ids = implode(',',$id);//批量获取id
		$id = is_array($id) ? $ids : $id;
		$map['id'] = array('in',$id); 
		if(!$id)
		{
			$this->error('请勾选记录!');
		}
		
		$model = M('vote');
		
		if($_REQUEST['Del'] == '删除') 
		{ 
			$model->where($map)->delete();
			$this->success('操作成功!',U('Vote/index'));
		}
		
		if($_REQUEST['Del'] == '隐藏')
		{
			$data['status'] = 0;
			$model->where($map)->save($data);
			$this->success('操作成功!',U('Vote/index'));
		}
		
		if($_REQUEST['Del']=='显示')
		{
			$data['status'] = 1;	
			$model->where($map)->save($data);
			$this->success("操作成功!",U('Vote/index'));
		}
	}
	
	public function show()
	{
		$model = M('vote');
		$list = $model->where('id='.$_GET['id'])->find();
		$strs = explode("\n",trim($list['content']));
		$total = 0;
		for($i = 0;$i < count($strs);$i++)
		{
			$s = explode("=",trim($strs[$i]));
			$data[$i]['num'] = $s[1];
			$data[$i]['title'] = $s[0];
			$total += $s[1];
		}
		$this->assign('votetitle',$list['title']);
		$this->assign('totalnum',$total);
		$this->assign('list',$data);
		$this->display();
	}
}
?>