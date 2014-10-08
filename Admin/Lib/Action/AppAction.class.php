<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 APP管理

    @Filename AppAction.class.php $

    @Author pengyong $

    @Date 2013-05-30 02:03:30 $
*************************************************************/
class AppAction extends CommonAction
{
	public function index()
	{
		$this->checksafeauth();
		$root = './';
		$d = scandir($root);
		$list = array();
		foreach($d as $v)
		{
			$filename = rtrim($root,'/').'/'.$v;
			$file = dirname($filename).'/'.strtolower(basename($filename)).'.php';
			if($filename=='./Web') $file = './index.php';
			if(is_dir($filename) && is_file($file) && $v<>'.' && $v<>'..')
			{
				$a['apptime'] = filemtime($file);
				$a['appname'] = basename($filename);
				$a['apppath'] = $file;
				$list[] = $a;
			}
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	public function del()
	{
		$this->checksafeauth();
		$id = strtolower($_GET['id']);
		if(empty($id)) $this->error('request error!');
		if(in_array($id,array('admin','index','web','user'))) $this->error('核心应用不可以删除！');
		if(is_file('./'.$id.'.php') && is_dir('./'.ucfirst($id)))
		{
			@unlink('./'.$id.'.php');
			File::del_dir('./'.ucfirst($id));
		}
		$this->success('操作成功！',U('App/index'));
	}
	
	//远程安装APP
	public function remoteinstall()
	{
		//安全验证 $this->checksafeauth();
		$url = $this->_get('url');
		$ext = strtolower(strrchr($url,'.'));
		$filepath = ltrim(strrchr($url,'/'),'/');
		if($ext <> '.zip') 
		{
			//兼容旧版本
			$url  = xbase64_decode($url);
			$ext = strtolower(strrchr($url,'.'));
			$filepath = ltrim(strrchr($url,'/'),'/');
			if($ext <> '.zip') $this->error('远程文件格式必须为.zip');
		}
		$content = fopen_url($url);
		if(empty($content)) $this->error('远程获取文件失败!');
		$filename = substr($filepath,0,-4);
		File::write_file($filepath,$content);
		import('ORG.PclZip');
		$zip =  new PclZip($filepath);
		$zip->extract(PCLZIP_OPT_PATH,'./'); 
		@unlink($filepath);//删除安装文件
		$this->success('操作成功!',U('App/index'));
	}
}