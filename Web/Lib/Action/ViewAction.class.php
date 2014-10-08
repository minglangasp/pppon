<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 文章浏览

    @Filename ViewAction.class.php $

    @Author pengyong $

    @Date 2013-01-02 23:24:39 $
*************************************************************/
class ViewAction extends CommonAction 
{
	public function index()
	{
		global $cfg_mb_open;
		$map['id'] = $_GET['id'];
		$arctinymodel  = M('arctiny');
		$arctinylist = $arctinymodel->where($map)->find();
		if(!$arctinylist) $this->error('文档ID不存在!');
		$arcmodel = M('arcmodel');
		$arcmodellist = $arcmodel->field('addtable')->where(array('id'=>$arctinylist['modelid']))->find();
		if(!$this->mkmodel($arcmodellist['addtable'])) $this->error('附加表不存在!');
		$model  = D($arcmodellist['addtable'].'View');
		$list = $model->where($map)->find();
		if(in_array('j',explode(',',$list['flag'])))
		{
			if($GLOBALS['cfg_jump_once']==0)
			{
				header('Location:'.$list['redirecturl']);
			}
			else
			{
				$this->assign("linkurl",$list['redirecturl']);
				$this->display('./Public/Common/Tpl/link.htm');
			}
			die();
		}
		if($list['arcrank']==2)
		{
			if(!USER_LOGINED && !ADMIN_LOGINED)
			{
				$this->error('当前文档需要会员登录!');
			}
		}
		elseif($list['arcrank']==4 or $list['arcrank']==8)
		{
			if(ADMIN_LOGINED == false)
			{
				$this->error('文档没有被审核通过!');
			}
		}
		//栏目链接 typelink
		$list['typeurl'] = url('list',$list['typeid']);
		$list['typelink'] = '<a href='.$list['typeurl'].'>'.$list['typename'].'</a>';
		//文章链接 arclink
		$list['arcurl'] = url('view',$list['id']);
		$list['arclink'] =  '<a href='.$list['arcurl'].'>'.$list['title'].'</a>';
		//tag关键词
		$list['tags'] = '';
		if(!empty($list['keywords']))
		{	
			$tags = explode(',',$list['keywords']);
			foreach($tags as $v)
			{
				$list['tags'] .="<a href='".url('search')."?tag=1&keyword={$v}'>{$v}</a>&nbsp;"; 
			}
		}
		//上一页,下一页
		$list['prearticle'] = $this->updownarticle($list['pubdate'],$list['typeid'],'up');
		$list['nextarticle'] = $this->updownarticle($list['pubdate'],$list['typeid'],'down');
		//内链替换
		$list['body'] = $this->articlelink($list['body']);
		$GLOBALS['_fields'] = $list;
		$GLOBALS['_fields']['position'] = $this->position();
		//文章内分页处理
		$body = explode('<hr style="page-break-after:always;" class="ke-pagebreak" />',$list['body']);
		$pagenum = (int)$_GET['p'];
		$totalpagenum = count($body);
		$pagenum = $pagenum > $totalpagenum ? $totalpagenum:$pagenum;
		$pagenum = $pagenum < 1 ? 1: $pagenum;
		$request_url = substr($_SERVER['REQUEST_URI'],0,-strlen(strrchr($_SERVER['REQUEST_URI'],'?')));
		if($totalpagenum > 1)
		{
			$pageurl = $request_url.'?p=';
			$GLOBALS['_fields']['paging'] = '';
			//上一页
			if($pagenum==1)
			{
				$GLOBALS['_fields']['paging'] .= '<a class="disabled">上一页</a>';
			}
			else
			{
				$lastpagenum = $pagenum -1; 
				$GLOBALS['_fields']['paging'] .= "<a href='{$pageurl}{$lastpagenum}'>上一页</a>";
			}
			//link页 1 2 3 4 5
			for($i=1;$i<=$totalpagenum;$i++)
			{
				if($pagenum==$i)
				{
					$GLOBALS['_fields']['paging'] .= "<a class='current'>{$i}</a>";
				}
				else
				{
					$GLOBALS['_fields']['paging'] .= "<a href='{$pageurl}{$i}'>{$i}</a>";
				}
				
			}
			//下一页 
			if($pagenum==$totalpagenum)
			{
				$GLOBALS['_fields']['paging'] .= '<a class="disabled">下一页</a>';
			}
			else
			{
				$nextpagenum = $pagenum +1; 
				$GLOBALS['_fields']['paging'] .= "<a href='{$pageurl}{$nextpagenum}'>下一页</a>";
			}
			//全文阅读
			$GLOBALS['_fields']['paging']  .= "<a href='{$pageurl}all'>全文阅读</a>";
			
			$GLOBALS['_fields']['body'] = $body[$pagenum-1];
		}
		//全文阅读判断
		if(isset($_GET['p']) && $_GET['p']=='all')
		{
			$GLOBALS['_fields']['paging']  = "<a href='{$pageurl}1' class='current'>分页阅读</a>";
			$GLOBALS['_fields']['body'] = $list['body'];
		}
		$filename ='./Public/Tpl/'.$GLOBALS['cfg_df_style'].str_replace('{style}','',$list['temparticle']);
		//wap支持
		global $_mode;
		if($_mode=='wap') $filename ='./Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'].str_replace('{wapstyle}','',$list['waptemparticle']);
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		//dump($GLOBALS['_fields'] );exit;
		//浏览次数处理
		$archive = M('archive');
		$archive->where(array('id'=>$map['id']))->setInc('click');
		$this->display($filename);
	}
	
	private function mkmodel($table)
	{
		$filepath = LIB_PATH.'Model/'.$table.'ViewModel.class.php';
		if(file_exists($filepath)) return true;
		$truetablename = C('DB_PREFIX').$table;
		$database = C('DB_NAME');
		$model  = M();
		$sql = "SELECT  COLUMN_NAME FROM  `information_schema`.`COLUMNS` where `TABLE_SCHEMA`='{$database}'  and  `TABLE_NAME`='{$truetablename}' order by COLUMN_NAME;";
		$list = $model->query($sql);
		if(!$list) return false;
		$fields = '';
		foreach($list as $v)
		{
			if($v['COLUMN_NAME'] != 'id' && $v['COLUMN_NAME'] != 'typeid')
			{
				$fields.= ",'".$v['COLUMN_NAME']."'";
			}
		}
		$fields  = ltrim($fields,',');
		$fields .= ",'_on'=>'archive.id={$table}.id'), ";
		$addonsql = "'{$table}'=>array({$fields}";
		$content = <<<DATA
		<?php
			class {$table}ViewModel extends ViewModel 
			{
				public \$viewFields = array
				(
					'archive'=>array('id','typeid','flag','modelid','senddate',
					'pubdate','click','keywords','description','money','arcrank',
					'writer','title','shorttitle','color','source',
					'litpic','voteid','mid','_type'=>'LEFT'), 
					'arctype'=>array('typename','temparticle','waptemparticle','_on'=>'archive.typeid=arctype.id','_type'=>'LEFT'),
					'member'=>array('username','_on'=>'archive.mid=member.id'),
					{$addonsql}
				);
			}
DATA;
		File::write_file($filepath,$content);
		return true;
   }
   //上下篇
   private function updownarticle($pubdate,$typeid,$name='up')
   {
		$map['typeid'] = $typeid; 
		if($name=='up') 
		{
			$map['pubdate'] = array('lt',$pubdate);
			$order = 'pubdate desc';
		}
		if($name=='down')
		{
			$map['pubdate'] = array('gt',$pubdate);
			$order = 'pubdate asc';
		}		
		$map['arcrank']  = array('in','1,2,3');
		$model = M('archive');
		$list = $model->field('title,id')->where($map)->order($order)->find();
		if(!$list) return;
		//wap支持
		global $_parameter;
		$list['arcurl'] = url('view',$list['id'],$_parameter);
		return "<a href='{$list['arcurl']}'>{$list['title']}</a>";
   }
   
   //内链替换
   private function articlelink($data)
   {
		$model = M('articlelink');
		$list = $model->order('rank desc')->select();
		if(!$list) return $data;
		foreach($list as $v)
		{
			$link = '<a href=\''.$v['url'].'\'>'.$v['title'].'</a>';
			$data =  $v['num']==0 ? strtr($data,array($v['title']=>$link)) : preg_replace('#'.$v['title'].'#',$link,$data,$v['num']);
		}
		return $data;
   }
   
}