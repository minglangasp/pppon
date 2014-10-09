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
       $filedir = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		//wap支持
		global $_mode;
		if($_mode=='wap') $filedir = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
		$GLOBALS['_fields']['position'] = $this->position();
		$filename = $filedir.'/guestbook.htm';
		//diypage支持
		if(isset($_GET['page']) && !empty($_GET['page']) &&!strpos($_GET['page'],'/') && !strpos($_GET['page'],'.') && !strpos($_GET['page'],'_'))
		{
			$filename = $filedir.'/page_'.trim($_GET['page']).'.htm';
		}
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
        $guestbook = M('guestbook');
        $glist = $guestbook->select();
        $this->assign('glist',$glist);
		$this->display($filename);
	}
    public function demopost()
	{
       $filedir = './Public/Tpl/'.$GLOBALS['cfg_df_style'];
		//wap支持
		global $_mode;
		if($_mode=='wap') $filedir = './Public/Wap/'.$GLOBALS['cfg_wap_tpl_default'];
		$GLOBALS['_fields']['position'] = $this->position();
		$filename = $filedir.'/guestbookpost.htm';
		//diypage支持
		if(isset($_GET['page']) && !empty($_GET['page']) &&!strpos($_GET['page'],'/') && !strpos($_GET['page'],'.') && !strpos($_GET['page'],'_'))
		{
			$filename = $filedir.'/page_'.trim($_GET['page']).'.htm';
		}
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
        $guestbook = M('guestbook');
        $glist = $guestbook->select();
        $this->assign('glist',$glist);
		$this->display($filename);
	}
    public function dopost()
	{
        $model = M('guestbook');
		$data['name'] = trim($_POST['Name']);
		$data['qq'] = trim($_POST['Phone']);
		$data['email'] = trim($_POST['Email']);
		$data['content'] = trim($_POST['Message']);
		$data['pubdate'] = time();
		$data['display'] = trim($_POST['display']);
		$data['other'] = trim($_POST['other']);
		$model->add($data);
        return "success";
	}
}