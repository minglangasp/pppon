<?php
/***********************************************************
    [WaiKuCms] (C)2012 waikucms.com
    
	@function 模型管理

    @Filename ArcmodelAction.class.php $

    @Author pengyong $

    @Date 2012-12-11 13:04:03 $
*************************************************************/
class ArcmodelAction extends CommonAction
{	
    public function index()
    {
		//安全验证
		$this->checksafeauth();
		import('@.ORG.Page');
		$model = M('arcmodel');
		$count = $model->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->limit($p->firstRow.','.$p->listRows)->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#eb6a5a'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display();
    }
	
	public function import()
	{
		if(isset($_GET['remotedata'])) $this->assign('remotedata',fopen_url($_GET['remotedata']));
		$this->display();
	}
	
	public function doimport()
	{
		header("Content-type:text/html;charset=utf-8"); 
		$mode = $this->_post('mode');
		$checktable = $this->_post('checktable');
		if(!$mode) $this->error('request error!');
		if($mode=='file')
		{
			$filename = $this->_post('filename',false); 
			$filecontent = File::read_file(rtrim($_SERVER["DOCUMENT_ROOT"],'/').$filename);
		}
		else
		{	
			$filecontent = $this->_post('textname',false);
			$filecontent = stripslashes($filecontent);
		}
		$xml = simplexml_load_string($filecontent);
		$map['typename'] =  (string)$xml->typename;
		$map['nid'] =  (string)$xml->nid;
		$map['titlename'] =  (string)$xml->titlename;
		$map['addtable'] =  (string)$xml->addtable;
		if(empty($map['typename']) or empty($map['nid']) or empty($map['titlename']) or empty($map['addtable']))
		{
			$this->error('导入数据有误!导入失败!');
		}
		if(substr($map['addtable'],0,5)<>'addon') $this->error('附加表前缀必须为addon');
		$arcmodel =M('arcmodel');
		if($arcmodel->where("typename='".$map['typename']."'")->find()) $this->error("系统已存在相同内容,typename字段必须唯一!");
		if($arcmodel->where("nid='".$map['nid']."'")->find())$this->error("系统已存在相同内容,nid字段必须唯一!");
		$model = M();
		$result = $model->query("show tables like '".C('DB_PREFIX').$map['addtable']."'");
		if(!empty($result) && $checktable<>1) $this->error('附加数据表已存在!');
		if(!empty($result)) $model->execute('DROP TABLE `'.C('DB_PREFIX').$map['addtable'].'`');
		//新增附加表
		$sql = "CREATE TABLE IF NOT EXISTS `".C('DB_PREFIX').$map['addtable']."` (";
		$sql.= "`id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '文档id',";
		$sql.= "`typeid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '栏目id',";
		//$sql.= "`body` mediumtext COMMENT '正文内容',";
		$sql.= "`redirecturl` varchar(255) NOT NULL COMMENT '跳转url',";
		$sql.="  PRIMARY KEY (`id`),KEY `typeid` (`typeid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$model->execute($sql);
		//分析fieldsetsql
		foreach ($xml->fieldsql->sql as $sql)
		{
			$sql = str_replace('#@__',C('DB_PREFIX'),(string)$sql);
			$model->execute($sql);
		}
		//用户设定的fieldset
		preg_match('/<fieldset>([\s\S]*)<\/fieldset>/',$filecontent,$matches);
		$fieldset = $matches[1];
		//初始化的fieldset
		$basicfieldset = <<<FIELD
		<field name="redirecturl" tag="input" type="text"  id="redirecturl" size="50" group="basic" alt="跳转地址"/>
FIELD;
		$map['fieldset'] = "<fieldset>".$basicfieldset.$fieldset."</fieldset>";
		//新增模型
		$map['status'] = 0;//直接生效
		$arcmodel->add($map);
		//刷新后台生效
		jump(U('Arcmodel/over'));
	}
	
	
	public function status()
	{
		$map['id'] = $this->_get('id');
		$arcmodel = M('arcmodel');
		$list = $arcmodel ->where($map)->find();
		if(!$list) $this->error('模型不存在!');
		if($list['status']==0) {$map['status']=1;}else{$map['status']=0;}
		$arcmodel->save($map);
		//刷新后台生效
		jump(U('Arcmodel/over'));
	}
	public function del()
    {
		$map['id'] = $this->_get('id');
		$arcmodel = M('arcmodel');
		$arcmodellist = $arcmodel ->where($map)->find();
		if(!$arcmodellist) $this->error('模型不存在!');
		//数据备份
		if($_GET['backup'] == 1)
		{
			$rs = new Model();
			$list = $rs->query("SHOW TABLES FROM "."`".C('DB_NAME')."`");
			$filesize = 2048;
			$file ='./Public/Backup/';
			$random = mt_rand(1000, 9999);
			$sql = ''; 
			$p = 1;
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
							$filename = $file.'theme_'.$cfg_df_style.'_'.date('Ymd').'_'.$random.'_'.$p.'.sql';
							File::write_file($filename,$sql);
							$p++;
							$sql='';
						}
					}
				}
			}
			if(!empty($sql))
			{
				$filename = $file.'before-del-model-id-'.$map['id'].'-'.date('Ymd').'_'.$random.'_'.$p.'.sql';
				File::write_file($filename,$sql);
			}
		}
		//删除附加表
		if($_GET['addontable'] == 1) $arcmodel->execute('DROP TABLE `'.C('DB_PREFIX').$arcmodellist['addtable'].'`');
		//删除相关栏目内容
		if($_GET['typedata'] == 1)
		{
			$arctiny = M('arctiny');
			$archive = M('archive');
			$arctype = M('arctype');
			$arctiny->where('modelid='.$map['id'])->delete();
			$archive->where('modelid='.$map['id'])->delete();
			$arctype->where('modelid='.$map['id'])->delete();
		}
		//删除模型表中的记录
		$arcmodel->where($map)->delete();
		//刷新后台生效
		jump(U('Arcmodel/over'));
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
	
	
	//远程安装模型
	public function remoteinstall()
	{
		$url = $this->_get('url');
		if($ext <> '.zip') 
		{
			//兼容旧版本
			$url  = xbase64_decode($url);
			$ext = strtolower(strrchr($url,'.'));
			$filepath = ltrim(strrchr($url,'/'),'/');
			if($ext <> '.zip') $this->error('远程文件格式必须为.zip');
		}
		$content = fopen_url($url);
		if(empty($content)){
			$this->assign('waitSecond',20);
			$this->error('远程获取文件失败!,<a href="'.$url.'" target="_blank">本地下载安装</a>');
		} 
		$filename = substr($filepath,0,-4);
		//检测是否已经安装
		$model = M('arcmodel');
		$id = $model->order('id desc')->getField('id') + 1;
		if($model->where("nid='".$filename."'")->find()) $this->error('系统已安装当前模型！');
		//获取数据并解压缩
		$tplpath = './Public/Model/'.$filename;
		File::write_file($filepath,$content);
		import('ORG.PclZip');
		$zip =  new PclZip($filepath);
		$zip->extract(PCLZIP_OPT_PATH,$tplpath); 
		//删除压缩包
		@unlink($filepath);
		//导入数据
		$sqlfile = $tplpath.'/data.sql';
		if(is_file($sqlfile))
		{
			$sql = explode('###',strtr(File::read_file($sqlfile),array('#@__'=>C('DB_PREFIX'),'__LINE__'=>$id)));
			foreach($sql as $v)
			{
				$model->execute(trim($v));
			}
		}
		//刷新缓存
		jump(U('Arcmodel/over'));
	}
	
	public function over()
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
		$this->display();
	}
}
?>