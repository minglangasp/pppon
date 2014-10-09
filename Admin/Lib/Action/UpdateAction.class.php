<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 在线升级模块

    @Filename UpdateAction.class.php $

    @Author pengyong $

    @Date 2013-02-18 18:59:19 $
*************************************************************/

class UpdateAction extends CommonAction
{
	public function index()
	{
		if($this->isPost())
		{
			$version = (int)$_POST['version'];
			$msg = fopen_url('http://cloud.waikucms.net/update.php?version='.$version);
			$data['version'] = fopen_url('http://cloud.waikucms.net/update.php?fullversion=1');
			$data['msg'] = $msg<>0 && $msg<>1 ? 2:$msg; 
			//$data['msg'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}
	
	public function shouquan()
	{
		if($this->isPost())
		{
			$domain = $_POST['domain'];
			$msg = fopen_url('http://cloud.waikucms.net/domainauth.php?domain='.$domain);
			echo $msg;die();
		}
		echo 0;die();
	}
	
	public function update()
	{	
		$date  = date('YmdHis');
		$logcontent = '歪酷CMS在线更新日志###';
		$logcontent .= '更新时间:'.date('Y-m-d H:i:s').'###';
		$logcontent .= '系统原始版本:'.C('SOFT_VERSION').'###';
		$backupall = isset($_GET['backupall']) ? $_GET['backupall']:0;
		$backupsql = isset($_GET['backupsql']) ? $_GET['backupsql']:0;
		$logcontent .= '正在执行系统版本检测...###';G('run1');
		$msg = fopen_url('http://cloud.waikucms.net/update.php?version='.substr(C('SOFT_VERSION'),-8));
		$msg = $msg<>0 && $msg<>1 ? 2:$msg; 
		//$msg=1;
		if($msg==0) $this->error('当前系统已经是最新版!');
		$nowversion = fopen_url('http://cloud.waikucms.net/update.php?fullversion=1');
		if($msg==2) $this->error('更新检测失败!');
		$updateurl = fopen_url('http://cloud.waikucms.net/update.php?updateurl=1');
		$logcontent .= '系统更新版本:'.$nowversion.'###';
		$logcontent .= '系统版本检测完毕,区间耗时:'.G('run1','end1').'s'.'###';
		//清理缓存
		$logcontent .='清理系统缓存...###';G('run2');
		$this->clear();
		$logcontent .='清理系统缓存完毕!,区间耗时:'.G('run2','end2').'s'.' ###';
		import('ORG.PclZip');
		File::mk_dir('./_update');
		File::mk_dir('./_update/'.$date);
		if($backupall==1)
		{
			//备份整站
			$logcontent .='开始备份整站内容...###';G('run3');
			$backupallurl = './_update/'.$date.'/backupall.zip';
			$zip = new PclZip($backupallurl);  
			$zip->create('Public/Config,Admin,User,Web,Core,index.php,admin.php,user.php'); 
			$logcontent .='成功完成整站数据备份,备份文件路径:<a href=\''.__ROOT__.ltrim($backupallurl,'.').'\'>'.$backupallurl.'</a>, 区间耗时:'.G('run3','end3').'s'.' ###';
		}
		if($backupsql==1)
		{
			//备份数据库
			$logcontent .='准备执行数据库备份...###';G('run4');
			$backupsqlurl = $this->backupsql($date);
			$logcontent .='成功完成系统数据库备份,备份文件路径:'.$backupsqlurl.', 区间耗时:'.G('run4','end4').'s'.' ###';
		}
		//获取更新包
		$logcontent .='开始获取远程更新包...###';G('run5');
		$updatedzipurl = './_update/'.$date.'/update.zip';
		File::write_file($updatedzipurl,fopen_url($updateurl));
		$logcontent .='获取远程更新包成功,更新包路径:<a href=\''.__ROOT__.ltrim($updatedzipurl,'.').'\'>'.$updatedzipurl.'</a>'.'区间耗时:'.G('run5','end5').'s'.'###';
		//解压缩更新包
		$logcontent .='更新包解压缩...###';G('run6');
		$zip = new PclZip($updatedzipurl);
		$zip->extract(PCLZIP_OPT_PATH,'./'); 
		$logcontent .='更新包解压缩成功...'.'区间耗时:'.G('run6','end6').'s'.'###';
		//更新数据库
		$updatesqlurl = './update.sql';
		if(is_file($updatesqlurl))
		{
			$logcontent .='更新数据库开始...###';G('run7');
			if(file_exists($updatesqlurl))
			{
				$rs = new Model();
				$sql = File::read_file($updatesqlurl);
				$sql = str_replace("\r\n", "\n", $sql); 
				foreach(explode(";\n", trim($sql)) as $query)
				{
					$rs->query(trim($query));
				}
			}
			unlink($updatesqlurl);
			$logcontent .='更新数据库完毕...'.'区间耗时:'.G('run7','end7').'s'.'###';
		}
		//系统版本号更新
		G('run8');
		$config = File::read_file('./Public/Config/config.ini.php');
		$config = str_replace(C('SOFT_VERSION'),$nowversion,$config);
		File::write_file('./Public/Config/config.ini.php',$config);
		$logcontent .='更新系统版本号,记录更新日志,日志文件路径:<a href=\''.__ROOT__.'/_update/'.$date.'/log.txt\'>./_update/'.$date.'/log.txt</a>,';
		$logcontent .='区间耗时:'.G('run8','end8').'s';
		//beta 20130221 to 20120226 临时更新设置,下次更新则省略这段代码
		if(!strpos($config,'SOFT_VERIFY'))
		{
			$config = str_replace("'DEFAULT_CHARSET'=>'utf8',","'DEFAULT_CHARSET'=>'utf8','SOFT_VERIFY'=>0,",$config);
			File::write_file('./Public/Config/config.ini.php',$config);
		}
		//记录更新日志
		File::write_file('./_update/'.$date.'/log.txt',$logcontent);
		//清空cookie
		cookie('updatenotice',null);
		//跳转到更新展示页面
		$this->success('更新完毕!',U('Update/over?date='.$date));
	}
	
	public function over()
	{
		$date = isset($_GET['date']) ? $_GET['date']:0;
		$dir = './_update/'.$date;
		if(!is_dir($dir)) $this->error('未检测到更新内容!');
		$content = File::read_file($dir.'/log.txt');
		$this->assign('log',explode('###',$content));
		$this->clear();
		$this->display();
	}
	
	public function clear()
	{
		//缓存路径
		$Webpath = './Web/Runtime';
		$Adminpath = './Admin/Runtime';
		$Userpath = './User/Runtime';
		File::del_dir($Webpath);
		File::del_dir($Adminpath);
		File::del_dir($Userpath);
		//清空前台切换theme的cookie
		cookie('theme',null);
		//清理缓存的模型文件
		$list = scandir('./Web/Lib/Model');
		foreach($list as $v)
		{
			if(substr($v,0,5)=='addon') unlink('./Web/Lib/Model/'.$v);
		}
	}
	
	public function backupsql($date)
	{
		//数据备份
		$rs = new Model();
		$list = $rs->query("SHOW TABLES FROM "."`".C('DB_NAME')."`");
		$filesize = 2048;
		$file ='./Public/Backup/';
		$random = mt_rand(1000, 9999);
		$sql = ''; 
		$p = 1;
		$url = '';
		foreach($list as $k => $v)
		{
			$table = current($v);
			//仅备份当前系统的数据库表
			$prefix = C('DB_PREFIX');
			if(substr($table,0,strlen($prefix)) == $prefix)
			{
				$rs = D(str_replace(C('DB_PREFIX'),'',$table));
				$array = $rs->select();
				$sql.= "TRUNCATE TABLE `$table`;\n";
				foreach($array as $value)
				{
					$sql.= $this->insertsql($table, $value);
					if (strlen($sql) >= $filesize*1000) 
					{
						$filename = $file.'update_'.$date.'_'.date('Ymd').'_'.$random.'_'.$p.'.sql';
						$url.= "<a href='{$filename}'>".$filename .'</a>,';
						File::write_file($filename,$sql);
						$p++;
						$sql='';
					}
				}
			}
		}
		if(!empty($sql))
		{
			$filename = $file.'update_'.$date.'_'.date('Ymd').'_'.$random.'_'.$p.'.sql';
			$url.= "<a href='{$filename}'>".$filename .'</a>,';
			File::write_file($filename,$sql);
		}
		return $url;
	}
	
	//生成SQL备份语句
	public function insertsql($table, $row)
	{
		$sql = "INSERT INTO `{$table}` VALUES ("; 
		$values = array(); 
		foreach ($row as $value) 
		{
			$values[] = "'" . mysql_real_escape_string($value) . "'"; 
		}
		$sql .= implode(', ', $values) . ");\n"; 
		return $sql;
	}
	//ajax 设置cookie,下次不再自动提醒更新
	public function applycookie()
	{
		cookie('updatenotice',1);
	}
	//升级管理
	public function manage()
	{
		if(!is_dir('./_update'))  $this->error('当前无更新数据!');
		$base = './_update';
		$list = scandir($base);
		$updatelist = array();
		foreach($list as $v)
		{
			$d  = array();
			if($v<>'.' && $v<>'..')
			{
				$content = File::read_file($base.'/'.$v.'/log.txt');
				if(!empty($content))
				{
					$c = explode('###',$content);
					$d['pubdate'] = str_replace('更新时间:','',$c[1]);
					$d['preset'] = str_replace('系统原始版本:','',$c[2]);
					$d['nowset'] = str_replace('系统更新版本:','',$c[4]);
					$d['date'] = $v;
					$updatelist[] = $d;
				}
			}
		}
		$this->assign('updatelist',$updatelist);
		$this->display();
	}
	
	public function showover()
	{
		$date = isset($_GET['date']) ? $_GET['date']:0;
		$dir = './_update/'.$date;
		if(!is_dir($dir)) $this->error('未检测到更新内容!');
		$content = File::read_file($dir.'/log.txt');
		$this->assign('log',explode('###',$content));
		$this->display();
	}
}