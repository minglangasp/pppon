<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 文件管理

    @Filename FileManageAction.class.php $

    @Author pengyong $

    @Date 2013-02-20 14:47:54 $
*************************************************************/

class FileManageAction extends CommonAction
{

	public function index()
	{
		//安全验证
		$this->checksafeauth();
		import('ORG.String');
		$nowdir = isset($_GET['dir']) ? urldecode($_GET['dir']):'';
		$lastdir = empty($nowdir) ? '' :substr($nowdir ,0,strlen($nowdir) - strlen(strrchr($nowdir,'/')));
		$this->assign("lastdir",$lastdir);
		$base = './';
		$root = empty($nowdir) ? $base: $base.$nowdir;
		//$dir =  File::get_dirs($root);
		$d = scandir($root);
		$list = array();
		foreach($d as $v)
		{
			$filename = rtrim($root,'/').'/'.$v;
			if($v<>'.' && $v<>'..')
			{
				
				if(is_dir($filename))
				{
					$a['filename'] = String::setCharset($v);
					$a['type'] = 'dir';
					$a['filesize'] = File::get_size($root.'/'.$v);
					$b = stat($root.'/'.$v);
					$a['atime'] = $b['atime'];
					$a['mtime'] = $b['mtime'];
					$a['ctime'] = $b['ctime'];
					$a['is_readable'] = is_readable($root.'/'.$v);
					$a['is_writeable'] = is_writeable($root.'/'.$v);
					$a['nowdir'] = urlencode($nowdir.'/'.$v);
					//$a['empty_dir'] = File::empty_dir($root.'/'.$v);
				}
				else
				{
					$a['filename'] = String::setCharset($v);
					$a['type'] = 'file';
					$a['ext'] = substr(strrchr($v,'.'),1);
					$b = stat($root.'/'.$v);
					$a['atime'] = $b['atime'];
					$a['mtime'] = $b['mtime'];
					$a['filesize'] = $b['size'];
					$a['ctime'] = $b['ctime'];
					$a['is_readable'] = is_readable($root.'/'.$v);
					$a['is_writeable'] = is_writeable($root.'/'.$v);
					$a['nowdir'] = urlencode($nowdir.'/'.$v);
				}
				$list[] = $a;
			}
		}
       // echo $list;
       // die();
		$this->assign("list",$list);
		$this->display();
	}	

	public function add()
	{
		//安全验证
		$this->checksafeauth();
		$nowdir = isset($_GET['dir']) ? urldecode($_GET['dir']):'';
		$nowpath =  '.'.$nowdir;
		$this->assign("nowpath",$nowpath);
		$this->display('add');
	}	
	
	public function doadd()
	{
		$from = $_POST['from'];
		$nowpath = $_POST['nowpath'];
		$filename = $_POST['filename'];
		$content = $_POST['content'];
		$type = $_POST['type'];
		$ext = $_POST['ext'];
		if($type=='file')
		{
			if(empty($filename)) $this->error('文件名不能为空!');
			if(strpos($filename,'.') or strpos($filename,'/') or strpos($filename,'"') or strpos($filename,"'") or strpos($filename,"?")) $this->error('文件名称不能包含点,单双引号,问号等字符');
			$filepath = $nowpath.'/'.$filename.$ext;
			MAGIC_QUOTES_GPC==true ? File::write_file($filepath,stripslashes($content)) : File::write_file($filepath,$content);
		}
		if($type=='dir')
		{
			$filepath = $nowpath.'/'.$filename;
			if(empty($filename)) $this->error('文件夹名不能为空!');
			if(strpos($filename,'.') or strpos($filename,'/') or strpos($filename,'"') or strpos($filename,"'") or strpos($filename,"?")) $this->error('文件夹名称不能包含点,单双引号,问号等字符');
			mkdir($filepath);
		}
		$from = empty($_POST['from']) ? U('FileManage/index') : $_POST['from'];
		$this->success('操作成功!',$from);
	}
	
	public function edit()
	{
		//安全验证
		$this->checksafeauth();
		$nowdir = isset($_GET['dir']) ? urldecode($_GET['dir']):'';
		if(empty($nowdir)) $this->error('参数不正确!');
		$file = '.'.$nowdir;
		$this->assign('filename',$file);
		if(!file_exists($file)) $this->error('文件不存在！');
		import('ORG.String');
		$this->assign("content",String::setCharset(File::read_file($file)));
		C('TMPL_PARSE_STRING.__ROOT__','__ROOT__');
		C('TMPL_PARSE_STRING.__APP__','__APP__');
		C('TMPL_PARSE_STRING.__PUBLIC__','__PUBLIC__');
		$this->display();
	}
	
	public function getdown()
	{
		$nowdir = isset($_GET['dir']) ? urldecode($_GET['dir']):'';
		if(empty($nowdir)) $this->error('参数不正确!');
		$file_path = '.'.$nowdir;
		if(is_file($file_path))
		{
			$file_name = iconv("utf-8","gb2312",basename($file_path)); 
			$fp = fopen($file_path,"r"); 
			$file_size = filesize($file_path); 
			//下载文件需要用到的头 
			Header("Content-type: application/octet-stream"); 
			Header("Accept-Ranges: bytes"); 
			Header("Accept-Length:".$file_size); 
			Header("Content-Disposition: attachment; filename=".$file_name); 
			$buffer=1024; 
			$file_count=0; 
			//向浏览器返回数据 
			while(!feof($fp) && $file_count<$file_size){ 
			$file_con=fread($fp,$buffer); 
			$file_count+=$buffer; 
			echo $file_con;
			} 
			fclose($fp); 
		}
		elseif(is_dir($file_path))
		{
			$name = basename($file_path);
			import('ORG.PclZip');
			$zip =  new PclZip(basename($file_path).'.zip');
			if($zip->create(ltrim($file_path,'./'),PCLZIP_OPT_REMOVE_PATH, ltrim($file_path,'./')))
			{
				$file_name = iconv("utf-8","gb2312",basename($file_path).'.zip'); 
				$fp = fopen(basename($file_path).'.zip',"r"); 
				$file_size = filesize(basename($file_path).'.zip'); 
				//下载文件需要用到的头 
				Header("Content-type: application/octet-stream"); 
				Header("Accept-Ranges: bytes"); 
				Header("Accept-Length:".$file_size); 
				Header("Content-Disposition: attachment; filename=".$file_name); 
				$buffer=1024; 
				$file_count=0; 
				//向浏览器返回数据 
				while(!feof($fp) && $file_count<$file_size){ 
				$file_con=fread($fp,$buffer); 
				$file_count+=$buffer; 
				echo $file_con;
				} 
				fclose($fp); 
				unlink(basename($file_path).'.zip');
			}
		}
		else
		{
			$this->error('文件或目录不存在!');
		}
	}
	

	public function rename()
	{	
		//安全验证
		$this->checksafeauth();
		$nowdir = isset($_GET['dir']) ? urldecode($_GET['dir']):'';
		if(empty($nowdir)) $this->error('参数不正确!');
		$file = '.'.$nowdir;
		$this->assign('filename',$file);
		$isdir = strpos(strrchr($nowdir,'/'),'.')!==false ?1:0;//1为文件,0为dir
		$this->assign('isdir',$isdir);
		if(!file_exists($file)) $this->error('文件不存在！');
		import('ORG.String');
		$this->display();
	}
	
	public function dorename()
	{
		$prefilename = $_POST['prefilename'];
		$newfilename = $_POST['newfilename'];
		if($prefilename==$newfilename) $this->error('不能和原文件或文件夹同名!');
		if(substr($newfilename,0,1)<>'.') $this->error('重命名路径不正确!');
		$isdir = $_POST['isdir'];
		if($isdir==0 && strpos(strrchr($newfilename,'/'),'.')!==false) $this->error('重命名文件夹格式不正确!');
		if($isdir==1 && !strpos(strrchr($newfilename,'/'),'.')) $this->error('重命名文件格式后缀不能为空!');
		rename(iconv('UTF-8','GBK',$prefilename), iconv('UTF-8','GBK',$newfilename));
		$from = empty($_POST['from']) ? U('FileManage/index') : $_POST['from'];
		$this->success('操作成功!',$from);
	}
	
	public function doedit()
	{
		$filename = $_POST['filename'];
		$content = $_POST['content'];
		if(empty($filename)) $this->error('文件路径不正确!');
		MAGIC_QUOTES_GPC==true ? File::write_file($filename,stripslashes($content)) : File::write_file($filename,$content);
		$from = empty($_POST['from']) ? U('FileManage/index') : $_POST['from'];
		$this->success('操作成功!',$from);
	}
	
	public  function del()
	{
		$nowdir = isset($_GET['dir']) ? urldecode($_GET['dir']):'';
		if(empty($nowdir)) $this->error('参数不正确!');
		$file = '.'.$nowdir;
		if(!file_exists($file)) $this->error('文件不存在！');
		if(is_dir($file))
		{
			File::del_dir($file);
		}
		else
		{
			@unlink($file);
		}
	}
	//文件上传组件
	public function upload()
	{
		$dir = isset($_GET['dir'])? $_GET['dir'] : '';
		$this->assign('dirname',$dir);
		$this->display();
	}
	//解压缩
	public function unzip()
	{
		//安全验证
		$this->checksafeauth();
		$dir = isset($_GET['dir'])? $_GET['dir'] : '';
		if(empty($dir)) $this->error('参数不正确!');
		if(strtolower(strrchr(strrchr($dir,'/'),'.'))<>'.zip') $this->error('文件格式为ZIP方可解压缩!');
		$this->assign('filename',$dir);
		$dir = dirname($dir);
		if($dir=='\\') $dir ='/';  
		$this->assign('unzipdir',$dir);
		$this->display();
	}
	//执行解压缩
	public function dounzip()
	{
		$filename = $_POST['filename'];
		$unzipdir = $_POST['unzipdir'];
		if(empty($filename) or strtolower(strrchr(basename($filename),'.'))<>'.zip') $this->error('参数不正确!');
		if(strpos(strrchr($unzipdir,'/'),'.')) $this->error('解压路径必须为文件夹!');
		import('ORG.PclZip');
		$zip =  new PclZip(ltrim($filename,'/'));
		$zip->extract(PCLZIP_OPT_PATH,ltrim($unzipdir,'/'));
		$from = empty($_POST['from']) ? U('FileManage/index') : $_POST['from'];
		$this->success('操作成功!',$from);
	}
	//打包
	public function zip()
	{
		//安全验证
		$this->checksafeauth();
		$dir = isset($_GET['dir'])? $_GET['dir'] : '';
		$this->assign('zipdir',$dir);
		$this->display();
	}
	//执行打包
	public function dozip()
	{
		import('ORG.PclZip');
		$filename = $_POST['filename'];
		$zipdir = $_POST['zipdir'];
		$zip = new PclZip(ltrim($filename,'/'));
		if($zipdir=='/')
		{
			//打包根目录
			$rootdir = scandir('./');
			$dd ='';
			foreach($rootdir as $v)
			{
				if($v<>'.' && $v<>'..')
				{
					$dd .= $v.',';
				}
			}
			$zip->create(rtrim($dd,','));
		}
		else
		{
			$zip->create(ltrim($zipdir,'/'),PCLZIP_OPT_REMOVE_PATH,ltrim($zipdir,'/'));
		}
		$from = empty($_POST['from']) ? U('FileManage/index') : $_POST['from'];
		$this->success('操作成功!',$from);
	}
	
}