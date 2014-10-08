<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 首页

    @Filename IndexAction.class.php $

    @Author pengyong $

    @Date 2013-01-01 23:43:06 $
*************************************************************/
class IndexAction extends CommonAction 
{
    public function index()
	{
		$filedir = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		//wap支持
		global $_mode;
		if($_mode=='wap') $filedir = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
		$GLOBALS['_fields']['position'] = $this->position();
		$filename = $filedir.'/index.htm';
		//diypage支持
		if(isset($_GET['page']) && !empty($_GET['page']) &&!strpos($_GET['page'],'/') && !strpos($_GET['page'],'.') && !strpos($_GET['page'],'_'))
		{
			$filename = $filedir.'/page_'.trim($_GET['page']).'.htm';
		}
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$this->display($filename);
	}
	public function nav()
	{
		$filedir = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		//wap支持
		global $_mode;
		if($_mode=='wap') $filedir = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
		$GLOBALS['_fields']['position'] = $this->position();
		$filename = $filedir.'/nav.htm';
		//diypage支持
		if(isset($_GET['page']) && !empty($_GET['page']) &&!strpos($_GET['page'],'/') && !strpos($_GET['page'],'.') && !strpos($_GET['page'],'_'))
		{
			$filename = $filedir.'/page_'.trim($_GET['page']).'.htm';
		}
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$this->display($filename);
	}
    public function background()
	{
		$filedir = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		//wap支持
		global $_mode;
		if($_mode=='wap') $filedir = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
		$GLOBALS['_fields']['position'] = $this->position();
		$filename = $filedir.'/background.htm';
		//diypage支持
		if(isset($_GET['page']) && !empty($_GET['page']) &&!strpos($_GET['page'],'/') && !strpos($_GET['page'],'.') && !strpos($_GET['page'],'_'))
		{
			$filename = $filedir.'/page_'.trim($_GET['page']).'.htm';
		}
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$this->display($filename);
	}
    public function music()
	{
		$filedir = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		//wap支持
		global $_mode;
		if($_mode=='wap') $filedir = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
		$GLOBALS['_fields']['position'] = $this->position();
		$filename = $filedir.'/music.htm';
		//diypage支持
		if(isset($_GET['page']) && !empty($_GET['page']) &&!strpos($_GET['page'],'/') && !strpos($_GET['page'],'.') && !strpos($_GET['page'],'_'))
		{
			$filename = $filedir.'/page_'.trim($_GET['page']).'.htm';
		}
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$this->display($filename);
	}
	public  function htmlindex()
	{
		if(session('cmsauth')<>substr(md5(strrev(cookie('uname')).'waikucms'.cookie('uid')),0,10))  $this->error('没有权限！');
		$filedir = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		//wap支持
		global $_mode;
		if($_mode=='wap') $filedir = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
		$GLOBALS['_fields']['position'] = $this->position();
		$filename = $filedir.'/index.htm';
		//diypage支持
		if(isset($_GET['page']) && !empty($_GET['page']) &&!strpos($_GET['page'],'/') && !strpos($_GET['page'],'.') && !strpos($_GET['page'],'_'))
		{
			$filename = $filedir.'/page_'.trim($_GET['page']).'.htm';
		}
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$this->buildHtml('index','./',$filename);
		$this->error('成功生成静态首页：index.html');
	}
	
	public function ad()
	{
		$map['id'] = (int)$_GET['id']; if($map['id']==0) die();
		$map['status'] = 1;
		$model = M('ad');
		$list = $model->field('content')->where($map)->find();
		if(!$list) die();
		$content = strtr($list['content'],array("'"=>"\\'"));
		//提前渲染解析内容
		$content = $this->fetch('',$content.' ');
		$js = "document.write('{$content}');";
		die($js);
	}
	
	public function special()
	{
		$map['id'] = $this->_get('id');
		$model = M('special');
		$list =  $model->where($map)->find();
		if(!$list) $this->error('专题不存在!');
		$filename = str_replace('{style}','./Public/Tpl/'.$GLOBALS['cfg_df_style'],$list['tempindex']);
		//wap支持
		global $_mode;
		if($_mode =='wap') $filename = str_replace('{wapstyle}','./Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'],$list['waptempindex']);
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$GLOBALS['_fields'] = $list;
		$this->display($filename);
	}
	
	public function record()
	{
		$pubdate  = $this->_get('pubdate'); 
		$GLOBALS['_fields']['pubdate']  = mktime(0,0,0,substr($pubdate,-2),1,substr($pubdate,0,4));
		$filename = './Public/Tpl/'.$GLOBALS['cfg_df_style'].'/record.htm';
		//wap支持
		global $_mode;
		if($_mode=='wap') $filename = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'].'/record.htm';
		$GLOBALS['_fields']['position'] = $this->position();
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$this->display($filename);
	}
}