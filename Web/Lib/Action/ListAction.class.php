<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 列表

    @Filename ListAction.class.php $

    @Author pengyong $

    @Date 2013-01-02 16:47:19 $
*************************************************************/
class ListAction extends CommonAction 
{
	public function index()
	{
		$map['id'] = (int)$this->_get('id');
		$model  = M('arctype');
		$list = $model->field('id as typeid,modeltype,fid,modelid,linkurl,typename as title,typename,seotitle,keywords,description,content,tempindex,templist,waptempindex,waptemplist')->where($map)->find();
		if(empty($list)) $this->error('栏目ID不存在!');
		/****
		$arctinymodel  = M('arctiny');
		$arctinylist = $arctinymodel->where(array('id'=>$lists['typeid']))->find();
		if(!$arctinylist) $this->error('文档ID不存在!');
		$arcmodel = M('arcmodel');
		$arcmodellist = $arcmodel->field('addtable')->where(array('id'=>$arctinylist['modelid']))->find();
		if(!$this->mkmodel($arcmodellist['addtable'])) $this->error('附加表不存在!');
		$model  = D($arcmodellist['addtable'].'View');
		$list = $model->where($map)->find();
		****/
		
		$GLOBALS['_fields'] = $list;
		$GLOBALS['_fields']['position'] = $this->position();
		$tplpath = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		if($list['modeltype']==2)
		{
			if($GLOBALS['cfg_jump_once']==0)
			{
				header('Location:'.$list['linkurl']);
			}
			else
			{
				$this->assign("linkurl",$list['linkurl']);
				$this->display('./Public/Common/Tpl/link.htm');
			}
			die();
		}
		if($list['modeltype']==0) $filename = str_replace('{style}',$tplpath,$list['templist']); 
		if($list['modeltype']==1) $filename = str_replace('{style}',$tplpath,$list['tempindex']);
		//wap支持
		global $_mode;
		if($_mode=='wap')
		{
			$tplpath = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
			if($list['modeltype']==0) $filename = str_replace('{wapstyle}',$tplpath,$list['waptemplist']); 
			if($list['modeltype']==1) $filename = str_replace('{wapstyle}',$tplpath,$list['waptempindex']);
		}
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		//dump($GLOBALS['_fields']);exit;
		$this->display($filename);
	}
}