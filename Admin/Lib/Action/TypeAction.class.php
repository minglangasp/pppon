<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 栏目管理

    @Filename TypeAction.class.php $

    @Author pengyong $

    @Date 2013-03-22 02:51:58 $
*************************************************************/
class TypeAction extends CommonAction
{
	public function index()
	{
		//栏目综合显示页面
		$model = M('arctype');
		//获取二叉树结构
		$list = $model->field("id,fid,path,typename,sortrank,concat(path,'-',id) as bpath")->order('bpath asc')->select();
		$js ='function extendtree(){';
		foreach($list as $key=>$value)
		{
			$list[$key]['count'] = count(explode('-',$value['bpath']));
			if($list[$key]['count']==2)
			{
				$js.= "$('#{$value['bpath']}').show();"; 
			}
		}
		$js.= '}';
		//动态js脚本
		$js .= '$(document).ready(function(){';
		$js .='extendtree();';
		$js .='});';
		$this->assign("js",$js);
		$this->assign("list",sorttree($list));
		$this->display();
	}
	
	public function add()
	{
		$list = array();
		$modelname = 'article';
		if(isset($_GET['id']))
		{
			$data['id'] = (int)$_GET['id'];
			$model = M('arctype');
			$list  = $model ->where($data)->find();
			if(!$list) $list = array();
			$this->assign('modelid',$list['modelid']);
			$arcmodel = M('arcmodel');
			$modelname = $arcmodel->where('id='.$list['modelid'])->getField('nid');
		}
		$this->assign('modelname',$modelname);
		$this->assign('selecttreelist',$this->selecttree('add',$list));
		$this->assign('modellist',$this->getarcmodel());
		$this->display();
	}
	
	
	//获取栏目模型
	private function getarcmodel($method='add',$_arr = array())
	{
		$model = M('arcmodel');
		$list  =$model->field('id,nid,typename')->where('status=0')->select();
		if($method=='edit')
		{
			foreach($list as $key=>$value)
			{
				if($list[$key]['id']== $_arr['modelid'])
				{
					$list[$key]['selected'] = " selected='selected'";
				}
			}
		}
		return $list;
	}
	
	//栏目二叉树结构
	private function selecttree($method='add',$_arr=array())
	{
		$model = M('arctype');
		$list = $model->field("*,concat(path,'-',id) as bpath")->order('bpath')->select();
		if($method=='add')
		{
			foreach($list as $key=>$value)
			{
				$list[$key]['count'] = count(explode('-',$value['bpath']));
				if($_arr['id']==$list[$key]['id']) $list[$key]['selected'] = " selected='selected'";
			}
		}
		elseif($method=='edit')
		{
			//屏蔽当前栏目和当前栏目子栏目选择
			$ids = array();
			array_push($ids,$_arr['id']);
			$pathlist = $model->field('id,path')->select();
			foreach($pathlist as $k=>$v)
			{
				//file_put_contents('1.txt',substr($v['path'],0,strlen($_arr['path'].'-'.$_arr['id'])));
				if(substr($v['path'].'-',0,strlen($_arr['path'].'-'.$_arr['id'].'-')) == $_arr['path'].'-'.$_arr['id'].'-')
				{
					array_push($ids,$v['id']);
				}
			}
			foreach($list as $key=>$value)
			{
				if($list[$key]['id']== $_arr['fid'])
				{
					$list[$key]['selected'] = " selected='selected'";
				}
				elseif(in_array($list[$key]['id'],$ids))
				{
					$list[$key]['selected'] = " disabled='disabled'";
				}
				else
				{
					$list[$key]['selected'] ='';
				}
				$list[$key]['count'] = count(explode('-',$value['bpath']));
			}
		}
		return sorttree($list);
	}	
	
	private function getdata($method='add')
	{
		$data = array();
		$data['fid'] = trim($_POST['fid']);
		$data['typename'] = trim($_POST['typename']);
		$data['modelid'] = trim($_POST['modelid']);  
		$data['sortrank'] = trim($_POST['sortrank']);      
		$data['display'] = trim($_POST['display']);             
		$data['litpic'] = trim($_POST['litpic']);
		$data['seotitle'] = trim($_POST['seotitle']);
		$data['keywords'] = trim($_POST['keywords']);
		$data['modeltype'] = trim($_POST['modeltype']);
		$data['linkurl'] = trim($_POST['linkurl']);
		$data['description'] = trim($_POST['description']);
		$data['tempindex'] = trim($_POST['tempindex']);
		$data['templist'] = trim($_POST['templist']);
		$data['temparticle'] = trim($_POST['temparticle']);
		$data['waptempindex'] = trim($_POST['waptempindex']);
		$data['waptemplist'] = trim($_POST['waptemplist']);
		$data['waptemparticle'] = trim($_POST['waptemparticle']);
		if (get_magic_quotes_gpc()) 
		{
			$data['content'] = stripslashes($_POST['content']);
		} 
		else 
		{
			$data['content'] = $_POST['content'];
		}
		if($method=='edit')
		{
			$data['id'] = trim($_POST['id']);
		}
		$model = M('arctype');
		if($data['fid']=='0')
		{
			$data['path']='0';
		}
		else
		{
			$list = $model->where('id='.$data['fid'])->find();
			if(!$list) return false;
			$data['path'] = $list['path'].'-'.$data['fid'];
		}
		return $data;
	}
	public function doadd()
	{
		$data =  $this->getdata();
		if(empty($data['typename'])) $this->error('栏目名称不能为空！');
		if(empty($data['sortrank'])) $this->error('栏目排序不能为空！');
		//检测批量添加选项
		$typename = explode(',',rtrim($data['typename'],','));
		$sortrank = explode(',',rtrim($data['sortrank'],','));
		$model = M('arctype');
		if(count($typename)==1)
		{
			$data['typename'] = $typename[0];
			$data['sortrank'] = (int)$sortrank[0];
			$model->add($data);
		}
		else
		{
			foreach($typename as $k=>$v)
			{
				$data['sortrank'] = isset($sortrank[$k])? (int)$sortrank[$k]:(int)$sortrank[0];
				$data['typename'] = $typename[$k];
				$model->add($data);
			}
		}
		$from = empty($_POST['_from'])? U('Type/index'):$_POST['_from'];
		$this->success('操作成功!',$from);
	}
	
	public function edit()
	{
		$data['id'] = (int)$_GET['id'];
		if($data['id']==0)
		{
			$this->error('参数不正确!');
		}
		$model = M('arctype');
		$list = $model->where($data)->find();
		$this->assign("list",$list);
		$this->assign("selecttreelist",$this->selecttree('edit',$list));
		$this->assign("modellist",$this->getarcmodel('edit',$list));
		$this->display();
	}
	
	public function doedit()
	{
		$model = M('arctype');
		$data = $this->getdata('edit');
		if(empty($data['typename'])) $this->error('栏目名称不能为空！');
		if(empty($data['sortrank'])) $this->error('栏目排序不能为空！');
		//需要判断,当栏目下有子栏目的时候,子栏目的path是跟着在变动的
		$list = $model->field('id')->where('fid='.$data['id'])->select();
		if($list)
		{
			//存在子栏目,则子栏目的path 也需要变动
			foreach($list as $k=>$v)
			{
				$v['path'] = $data['path'].'-'.$data['id'];
				$model->save($v);
			}
		}
		$model->save($data);
		$this->success('操作成功!',U('Type/index'));
	}
	
	public function del()
	{
		//删除栏目
		$data['id'] = (int)$_GET['id'];
		if($data['id']==0) $this->error('参数不正确!');
		$model = M('arctype');
		if(!$model->where($data)->find()) $this->error('栏目ID不存在!');
		if($model->where('fid='.$data['id'])->find()) $this->error('当前栏目有子栏目.不能删除!');
		//当前栏目下有文章也不行,得提示先把文章给删除
		$arctiny = M('arctiny');
		if($arctiny->where("typeid=".$data['id'])->find()) $this->error('当前栏目下还有文章，请先删除所有的文章！');
		$model->where($data)->delete();
		$this->success('操作成功!',U('Type/index'));
	}
	
	public function delall()
	{
		$model = M('arctype');
		//dump($_POST);exit;
		for($i=0;$i< count($_POST['id']);$i++)
		{
			$model->where('id='.$_POST['id'][$i])->setField('sortrank',$_POST['sortrank'][$i]);
		}
		$this->success("操作成功!",U('Type/index'));
	}
}