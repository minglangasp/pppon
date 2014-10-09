<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 首页

    @Filename IndexAction.class.php $

    @Author pengyong $

    @Date 2013-01-01 23:43:06 $
*************************************************************/
class GuestBookAction extends CommonAction 
{
    public function index()
   {
		import('@.ORG.Page');
		$typeid = $this->_get('typeid');
		$modelid = $this->_get('modelid');
		$arcrank = $this->_get('arcrank');
		$unarcrank = $this->_get('unarcrank');
		$flag = $this->_get('flag');
		$kwd = $this->_post('kwd');
		//get 传值 搜索支持
		
		$model = M('guestbook');
		$count = $model->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->field('id,name,qq,email,content,pubdate,display,other')->limit($p->firstRow.','.$p->listRows)->order('pubdate desc')->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->assign('map',$map);
		$this->display("index");
   }
    public function edit()
    {
        $map['id'] = $this->_get('id');
		if($map['id']==0) $this->error('参数不正确!');
		$Guestbookmodel = M('guestbook');
		$guestbook = $Guestbookmodel->where($map)->find();
		if(!$guestbook) $this->error('guestbooklist表数据不存在!');
		
		//附加表字段
		$this->assign('guestbook',$guestbook);
		
		$this->display('edit');
    }
    public function doedit()
	{
        $model = M('guestbook');
		$data['id'] = trim($_POST['id']);
        $data['name'] = trim($_POST['name']);
		$data['qq'] = trim($_POST['qq']);
		$data['email'] = trim($_POST['email']);
		$data['content'] = trim($_POST['content']);
		$data['pubdate'] = time($_POST['pubdate']);
		$data['display'] = trim($_POST['display']);
		$data['other'] = trim($_POST['other']);
        
		$model->save($data);
        //5.成功跳转
		$_from = $this->_post('_from');
		$_from = empty($_from) ? U('Guestbook/index'):$_from;
		$this->success('操作成功!',$_from);
	}
    public function del()
   {
		$data['id'] = $this->_get('id');
		$method = $this->_get('method');
		$from = empty($_SERVER['HTTP_REFERER']) ? U('Guestbook/index'):$_SERVER['HTTP_REFERER'];
		$arctiny = M('guestbook');
		$tinylist = $arctiny->where($data)->find();
		if($tinylist)
		{
			if($method=='truedel')
			{
				//直接删除
				$arctiny->where($data)->delete();
			}
			elseif($method=='redel')
			{
				//回收站还原
				$data['arcrank'] = 1; 
				$archive->save($data);
			}
			else
			{
				//回收站回收
				$data['arcrank'] = 8; 
				$archive->save($data);
			}
			$this->success('操作成功!',$from);
		}
		else
		{
			$this->error('数据不存在!');
		}
   }
   
   
   
   public function delall()
   {
	   //批量操作
		$id = $_REQUEST['id'];  //获取文章aid
		//$ids = implode(',',$id);//批量获取aid
		//$id = is_array($id) ? $ids : $id;
		$map['id'] = array('in',$id);
       if(is_array($id)){
                $where = 'id in('.implode(',',$id).')';
                }else{
                $where = 'id='.$id;
        }
		if(!$id)$this->error('请勾选记录!');
		$from = empty($_SERVER['HTTP_REFERER']) ? U('Index/main'):$_SERVER['HTTP_REFERER'];
		if($_REQUEST['Del'] == '隐藏')
		{
			$archive = M('guestbook');
			$map['display'] = '1';
			$archive->where($where)->save($map);
			$this->success('操作成功!',$from);
		}
		elseif($_REQUEST['Del'] == '删除')
		{
			$archive = M('guestbook');
			//$archive->delete($map);            
            //dump($where);
            //die();
            $archive->where($where)->delete();
			$this->success('操作成功!',$from);
		}
		elseif($_REQUEST['Del'] == '显示')
		{
			$archive = M('guestbook');
			$map['display'] = '0';
			$archive->where($where)->save($map);
			$this->success('操作成功!',$from);
		}
   }
    
}