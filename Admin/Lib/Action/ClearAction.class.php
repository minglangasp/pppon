<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组:清理缓存

    @Filename ClearAction.class.php $

    @Author pengyong $

    @Date 2013-01-27 17:55:11 $
*************************************************************/
class ClearAction extends CommonAction
{	
	//清理入口
	public function index()
	{
		$this->clearcache();
	}
	
	//****清理系统缓存****
	public function clearcache()
	{
		//缓存路径
		$Webpath = './Web/Runtime/';
		$Adminpath = './Admin/Runtime/';
		$Userpath = './User/Runtime/';
		if(is_dir($Webpath))
		{
			File::del_dir($Webpath);
		}
		elseif(is_dir($Adminpath))
		{
			File::del_dir($Adminpath);
		}
		elseif(is_dir($Userpath))
		{
			File::del_dir($Userpath);
		}
		//清空前台切换theme的cookie
		cookie('theme',null);
		//清理缓存的模型文件
		$list = scandir('./Web/Lib/Model');
		foreach($list as $v)
		{
			if(substr($v,0,5)=='addon') unlink('./Web/Lib/Model/'.$v);
		}
		$msg = '系统缓存清理完毕!';
		$this->assign('waitSecond',5); 
		$this->assign('jumpUrl',U('Index/main')); 
		$this->success($msg);
	}
}
?>