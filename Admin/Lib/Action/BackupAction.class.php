<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
	
    @function:数据库备份与还原

    @Filename BackupAction.class.php $

    @Author pengyong $

    @Date 2013-01-18 13:11:06 $
*************************************************************/
class BackupAction extends CommonAction
{	
     public function index()
    {
		//安全验证
		$this->checksafeauth();
		$rs = new Model();
		$list = $rs->query("SHOW TABLES FROM "."`".C('DB_NAME')."`");
		$prefix = C('DB_PREFIX');
		$table = array();
        foreach ($list as $k => $v)
		{
			$tablename = current($v);
			if(substr($tablename,0,strlen($prefix)) == $prefix)
			{
				$table[$k] = $tablename;
			}
        }
		$this->assign('tablelist',$table);
		$this->display();
	}
	public function intsql()
	{
		$this->checksafeauth();
		$this->display();
	}
	public function dointsql()
	{
		$this->checksafeauth();
		$sql = trim($_POST['sql']);
		if (empty($sql)) {
			$this->error('SQL语句不能为空！');
		}else{
			$sql = trim(stripslashes($sql));
			$rs = new Model();
			$rs->query($sql);
			$lastsql = $rs->getLastSql();
			$this->assign("waitSecond",5);
			$this->success('SQL语句成功运行!<br>'.$lastsql);
		}
	}
	
	public function repsql()
	{
		$this->checksafeauth();
		$rs = new Model();
		$list = $rs->query('SHOW TABLES FROM '.C('db_name'));
		$tablearr = array();
        foreach ($list as $key => $val) {
            $tablearr[$key] = current($val);
        }
		$this->assign('list_table',$tablearr);	
		$this->display();
	}
	
	//执行批量替换
    public function dorepsql(){
		$this->checksafeauth();
		if(empty($_POST['rpfield'])){
			$this->error("请手工指定要替换的字段！");
		}
		if(empty($_POST['rpstring'])){
			$this->error("请指定要被替换内容！");
		}
		$exptable = str_replace(C('db_prefix'),'',$_POST['exptable']);
		$rs = D($exptable);
		$exptable = C('db_prefix').$exptable;//表
		$rpfield = trim($_POST['rpfield']);//字段
		$rpstring = $_POST['rpstring'];//被替换的
		$tostring = $_POST['tostring'];//替换内容
		$condition = trim(stripslashes($_POST['condition']));//条件
		$condition = empty($condition) ? '' : " where $condition ";
		$rs->execute(" update $exptable set $rpfield = Replace($rpfield,'$rpstring','$tostring') $condition ");
		$lastsql = $rs->getLastSql();
		$this->assign("waitSecond",5);
		$this->success('批量替换完成!SQL执行语句!<br>'.$lastsql);
    }	
	
		//Ajax展示字段信息
    public function ajaxfields(){
		$id = str_replace(C('DB_PREFIX'),'',$_GET['id']);
		if (!empty($id)) {
			$rs = D($id);
			$array = $rs->getDbFields();
			echo "<div style='border:1px solid #ababab;width:500px;background-color:#FEFFF0;margin-top:6px;padding:3px;line-height:160%'>";
			echo "表(".C('DB_PREFIX').$id.")含有的字段：<br>";
			foreach($array as $key=>$val){
				if(!is_int($key)){
					break;
				}
				if (ereg("cfile|username|userpwd|user|pwd",$val)){
					continue;
				}
				echo "<a href=\"javascript:rpfield('".$val."')\">".$val."</a>\r\n";
			}
			echo "</div>";
		}else{
			echo 'no fields';
		}
    }
	public function dobackup()
	{
		if(empty($_POST['ids']))
		{
			$this->error("请选择需要备份的数据库表！");
		}
		$filesize = intval($_POST['filesize']);
		if ($filesize < 512) 
		{
			$this->error("出错了,请为分卷大小设置一个大于512的整数值！");
		}
		$file ='./Public/Backup/';
		$random = mt_rand(1000, 9999);
		$sql = ''; 
		$p = 1;
		foreach($_POST['ids'] as $table)
		{
			$rs = D(str_replace(C('DB_PREFIX'),'',$table));
			$array = $rs->select();
			$sql.= "TRUNCATE TABLE `$table`;\n";
			foreach($array as $value)
			{
				$sql.= $this->insertsql($table, $value);
				if (strlen($sql) >= $filesize*1000) 
				{
					$filename = $file.date('Ymd').'_'.$random.'_'.$p.'.sql';
					File::write_file($filename,$sql);
					$p++;
					$sql='';
				}
			}
		}
		if(!empty($sql))
		{
			$filename = $file.date('Ymd').'_'.$random.'_'.$p.'.sql';
			File::write_file($filename,$sql);
		}
		$this->success("数据库分卷备份已完成,共分成{$p}个sql文件存放！",U("Backup/restore"));
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
	
	//展示还原
    public function restore()
	{
		//安全验证
		$this->checksafeauth();
		$filepath = './Public/Backup/*.sql';
		$filearr = glob($filepath);
		if (!empty($filearr)) 
		{
			foreach($filearr as $k=>$sqlfile)
				{
					preg_match("/([0-9]{8}_[0-9a-z]{4}_)([0-9]+)\.sql/i",basename($sqlfile),$num);
					$restore[$k]['filename'] = basename($sqlfile);
					$restore[$k]['filesize'] = round(filesize($sqlfile)/(1024*1024), 2);
					$restore[$k]['maketime'] = date('Y-m-d H:i:s', filemtime($sqlfile));
					$restore[$k]['pre'] = $num[1];
					$restore[$k]['number'] = $num[2];
					$restore[$k]['path'] = './Public/Backup/';
				}
			$this->assign('list',$restore);
        	$this->display('restore');
		}
		else
		{
			$this->success('没有检测到备份文件,请先备份或上传备份文件到./Public/Backup/',U("Backup/index"));
		}
    }
	
	//导入还原
	public function back()
	{
		$rs = new Model();
		$pre = $_GET['id'];
		$fileid = $_GET['fileid'] ? intval($_GET['fileid']) : 1;
		$filename = $pre.$fileid.'.sql';
		$filepath = './Public/Backup/'.$filename;
		if(file_exists($filepath))
		{
			$sql = File::read_file($filepath);
			$sql = str_replace("\r\n", "\n", $sql); 
			foreach(explode(";\n", trim($sql)) as $query)
			{
				$rs->query(trim($query));
			}
			$this->success('第'.$fileid.'个备份文件恢复成功,准备恢复下一个,请稍等！',U('Backup/back?id='.$pre.'&fileid='.($fileid+1)));
		}
		else
		{
			$this->success("数据库恢复成功！",U("Backup/index"));
		}
		
	}
	//下载还原
	public function down()
	{
		$filepath = './Public/Backup/'.$_GET['id'];
		if (file_exists($filepath))
		{
			$filename = $filename ? $filename : basename($filepath);
			$filetype = trim(substr(strrchr($filename, '.'), 1));
			$filesize = filesize($filepath);
			ob_end_clean();
			header('Cache-control: max-age=31536000');
			header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
			header('Content-Encoding: none');
			header('Content-Length: '.$filesize);
			header('Content-Disposition: attachment; filename='.$filename);
			header('Content-Type: '.$filetype);
			readfile($filepath);exit;
		}
		else
		{
			$this->error('出错了,没有找到分卷文件！');
		}
	}
	//删除分卷文件
	public function del()
	{
		$filename = trim($_GET['id']);
		@unlink('./Public/Backup/'.$filename);
		$this->success($filename.'已经删除！',U("Backup/restore"));
	}
	//删除所有分卷文件
	public function delall()
	{
		if(empty($_POST['ids']))
		{
			$this->error("请先选择备份文件!");
		}
		foreach($_POST['ids'] as $value)
		{
			@unlink('./Public/Backup/'.$value);
		}
		$this->success('批量删除分卷文件成功！',U("Backup/restore"));
	}
	//上传备份文件
	public function upload()
	{
		$this->display('upload');
	}
	//执行上传
	public function doupload()
	{
		//处理文件名,获取原始文件名
		$filename = str_replace(".sql","",$_FILES['url']['name']);
		import('ORG.UploadFile');
		$upload=new UploadFile();
		$upload->maxSize='2048000';  
		$upload->savePath='./Public/Backup/';
		$upload->saveRule= $filename;
		$upload->uploadReplace = true; 
		$upload->allowExts = array('sql');     //准许上传的文件后缀
		if($upload->upload())
		{
			$this->success("上传成功!",U("Backup/restore"));
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}
}
?>