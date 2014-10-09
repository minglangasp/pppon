<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 投票浏览

    @Filename VoteAction.class.php $

    @Author pengyong $

    @Date 22013-01-21 23:27:40 $
*************************************************************/
class VoteAction extends CommonAction 
{
	public function index()
	{
		$map['id'] = (int)$_GET['id'];
		$map['status'] = 1;
		$model = M('vote');
		$list = $model->where($map)->find();
		if(!$list) $this->error('当前投票不存在!');
		$list['votestatus'] = $list['overtime']< time() ? '已结束':'正在进行中';
		$list['voteid'] = $list['id'];
		$GLOBALS['_fields'] = $list;
		$filename = './Public/Tpl/'.$GLOBALS['cfg_df_style'].'/vote.htm';
		//wap支持
		global $_mode;
		if($_mode=='wap') $filename = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'].'/vote.htm';
		$GLOBALS['_fields']['position'] = $this->position();
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$this->display($filename);
	}
	
	public function update()
	{	
		//dump($_POST);EXIT;
		if(empty($_POST['vote']) or empty($_POST['key'])) $this->error('请选择投票项!');
		$map['id'] = (int)$_POST['id'];
		$cid = cookie('vote_'.$map['id']);
		if(!empty($cid)) $this->error('您已经投过票了!');
		$map['status'] = 1;
		$model = M('vote');
		$list = $model->where($map)->find();
		if(!$list) $this->error('当前投票不存在!');
		$content = explode("\n",trim($list['content']));
		foreach($_POST['key'] as $k=>$v)
		{
			if(!empty($_POST['vote'][$k])) 
			{
				$s = explode('=',$_POST['vote'][$k]);
				$content[$v] = $s[0].='='.($s[1]+1);
			}
		}
		$data['content'] = implode("\n",$content);
		//dump($data);
		$model->where($map)->save($data);
		cookie('vote_'.$map['id'],time(),3600);
		$this->success('投票成功!',url('vote',$list['id']));
	}
}