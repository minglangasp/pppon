<?php
@set_time_limit(0);
error_reporting(E_ALL || ~E_NOTICE);
$verMsg = ' V1.0 Release UTF-8';
$s_lang = 'utf-8';
$dfDbname = 'ppponv1.0utf8';
$errmsg = '';
$insLockfile = dirname(__FILE__).'/install_lock.txt';
$rnd_cookieEncode = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(1000,9999).chr(mt_rand(ord('A'),ord('Z')));
define('WKROOT',preg_replace("#[\\\\\/]Install#", '', dirname(__FILE__)));
define('WKCONFIG',dirname(__FILE__).'/../Public/Config/');
define('WKBASIC',dirname(__FILE__).'/../Web/Conf/');
header("Content-Type: text/html; charset={$s_lang}");
require_once('functions.php');
foreach(Array('_GET','_POST','_COOKIE') as $_request)
{
    foreach($$_request as $_k => $_v) ${$_k} = RunMagicQuotes($_v);
}
if(file_exists($insLockfile))
{
    exit(" 程序已运行安装，如果你确定要重新安装，请先从FTP中删除 Install/install_lock.txt！");
}
if(empty($step))
{
    $step = 1;
}
/*------------------------
使用协议书
function _1_Agreement()
------------------------*/
if($step==1)
{
    include('./templates/step-1.html');
    exit();
}
/*------------------------
环境测试
function _2_TestEnv()
------------------------*/
else if($step==2)
{
	$phpv =PHP_VERSION;
    $sp_os = PHP_OS;
    $sp_gd = gdversion();
    $sp_server = $_SERVER['SERVER_SOFTWARE'];
    $sp_host = (empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
    $sp_name = $_SERVER['SERVER_NAME'];
    $sp_max_execution_time = ini_get('max_execution_time');
    $sp_allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_safe_mode = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');
    $sp_gd = ($sp_gd>0 ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_mysql = (function_exists('mysql_connect') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_PclZip = (PclZip_test() ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_fopen_url = (fopen_url_test() ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
	@unlink('data.zip');
	File::del_dir('./_data');
    if($sp_mysql=='<font color=red>[×]Off</font>')
    $sp_mysql_err = TRUE;
    else
    $sp_mysql_err = FALSE;
	$sp_testdirs = array(
        '/',
		'/Core/*',
        '/Public/*',
        '/Admin/*',
        '/Web/*',
        '/User/*',
        '/Install/*',
    );
    include('./templates/step-2.html');
    exit();
}elseif($step==3){
if(!empty($_SERVER['REQUEST_URI']))
    $scriptName = $_SERVER['REQUEST_URI'];
    else
    $scriptName = $_SERVER['PHP_SELF'];

    $basepath = preg_replace("#\/Install(.*)$#i", '', $scriptName);
	$cfg_cmspath = $basepath;
    if(!empty($_SERVER['HTTP_HOST']))
	{
		$baseurl = 'http://'.$_SERVER['HTTP_HOST'].$basepath;
		$cfg_basehost = 'http://'.$_SERVER['HTTP_HOST'];
	}  
    else
	{
		$baseurl = "http://".$_SERVER['SERVER_NAME'].$basepath;
		$cfg_basehost = 'http://'.$_SERVER['SERVER_NAME'];
	}
include('./templates/step-3.html');
    exit();
}/*------------------------
普通安装
function _4_Setup()
------------------------*/
else if($step==4)
{
    $conn = mysql_connect($dbhost,$dbuser,$dbpwd) or die("<script>alert('数据库服务器或登录密码无效，\\n\\n无法连接数据库，请重新设定！');history.go(-1);</script>");

    mysql_query("CREATE DATABASE IF NOT EXISTS `".$dbname."`;",$conn);
    
    mysql_select_db($dbname) or die("<script>alert('选择数据库失败，可能是你没权限，请预先创建一个数据库！');history.go(-1);</script>");

    //获得数据库版本信息
    $rs = mysql_query("SELECT VERSION();",$conn);
    $row = mysql_fetch_array($rs);
    $mysqlVersions = explode('.',trim($row[0]));
    $mysqlVersion = $mysqlVersions[0].".".$mysqlVersions[1];
    mysql_query("SET NAMES '$dblang',character_set_client=binary,sql_mode='';",$conn);
	
    $fp = fopen(dirname(__FILE__)."/config.ini.php","r");
    $configStr1 = fread($fp,filesize(dirname(__FILE__)."/config.ini.php"));
    fclose($fp);

    //config.ini.php
    $configStr1 = str_replace("~dbhost~",$dbhost,$configStr1);
    $configStr1 = str_replace("~dbname~",$dbname,$configStr1);
    $configStr1 = str_replace("~dbuser~",$dbuser,$configStr1);
    $configStr1 = str_replace("~dbpwd~",$dbpwd,$configStr1);
    $configStr1 = str_replace("~dbprefix~",$dbprefix,$configStr1);
    $configStr1 = str_replace("~dbcookie~",$cookieencode,$configStr1);
	$configStr1 = str_replace("~dblang~",$dblang,$configStr1);
	
    @chmod(WKCONFIG,0777);
    $fp = fopen(WKCONFIG."/config.ini.php","w") or die("<script>alert('写入配置失败，请检查../Public/Config目录是否可写入！');history.go(-1);</script>");
    fwrite($fp,$configStr1);
    fclose($fp);

    if($mysqlVersion >= 4.1)
    {
        $sql4tmp = "MyISAM DEFAULT CHARSET=".$dblang;
    }
  
    //创建数据表
  
    $query = '';
    $fp = fopen(dirname(__FILE__).'/sql-dftables.txt','r');
    while(!feof($fp))
    {
        $line = rtrim(fgets($fp,1024));
        if(preg_match("#;$#", $line))
        {
            $query .= $line."\n";
            $query = str_replace('#@__',$dbprefix,$query);
            if($mysqlVersion < 4.1)
            {
                $rs = mysql_query($query,$conn);
            } else {
                if(preg_match('#CREATE#i', $query))
                {
                    $rs = mysql_query(preg_replace("#MyISAM#i",$sql4tmp,$query),$conn);
                }
                else
                {
                    $rs = mysql_query($query,$conn);
                }
            }
            $query='';
        } else if(!preg_match("#^(\/\/|--)#", $line))
        {
            $query .= $line;
        }
    }
    fclose($fp);
    
    //导入默认数据
    $query = '';
    $fp = fopen(dirname(__FILE__).'/sql-dfdata.txt','r');
    while(!feof($fp))
    {
        $line = rtrim(fgets($fp, 1024));
        if(preg_match("#;$#", $line))
        {
            $query .= $line;
            $query = str_replace('#@__',$dbprefix,$query);
            if($mysqlVersion < 4.1) $rs = mysql_query($query,$conn);
            else $rs = mysql_query(str_replace('#~lang~#',$dblang,$query),$conn);
            $query='';
        } else if(!preg_match("#^(\/\/|--)#", $line))
        {
            $query .= $line;
        }
    }
    fclose($fp);

    //更新配置
    $cquery = "Update `{$dbprefix}config` set value='{$cfg_webname}' where id=4;";
    mysql_query($cquery,$conn);
    $cquery = "Update `{$dbprefix}config` set value='{$cfg_basehost}' where id=1;";
    mysql_query($cquery,$conn);
    $cquery = "Update `{$dbprefix}config` set value='{$cfg_cmspath}' where id=22;";
    mysql_query($cquery,$conn);
	$cquery = "Update `{$dbprefix}config` set value='{$cfg_cmspath}' where id=2;";
    mysql_query($cquery,$conn);
    
    //增加管理员帐号
	$adminquery = "INSERT INTO `{$dbprefix}admin` (`id`, `name`, `password`, `logintime`, `loginip`, `status`) VALUES
(1, '$adminuser', '".xmd5($adminpwd)."', ".time().", '127.0.0.1', '超级');";
$adminquery2 = "INSERT INTO `{$dbprefix}member` (`id`, `username`, `password`, `email`, `status`, `activekey`, `logintime`, `regtime`, `loginip`, `money`, `rankid`, `sex`, `province`, `city`, `qq`, `birthday`, `avtar`) VALUES
(1, '$adminuser', '".xmd5($adminpwd)."', 'i@pengyong.info', 0, 'de90979d72b0d3b2cf4b3ccc0a3a632c', ".time().", ".time().", '127.0.0.1', 0, 23, 1, '山东', '济南', '634150845', '1988-01-23', '');";
    mysql_query($adminquery,$conn);
    mysql_query($adminquery2,$conn);
	//安全密码设置
	$safeadminpwd = empty($safeadminpwd) ? xmd5($adminpwd):xmd5($safeadminpwd);
	File::write_file('../Admin/Common/safeauth.php',"<?php\treturn '{$safeadminpwd}';?>");
	//安装体验数据
    if($installdemo == 1)
    {
		$query = '';
		$fp = fopen(dirname(__FILE__).'/sql-dfdemo.txt','r');
		while(!feof($fp))
		{
			$line = rtrim(fgets($fp, 1024));
			if(preg_match("#;$#", $line))
			{
				$query .= $line;
				$query = str_replace('#@__',$dbprefix,$query);
				if($mysqlVersion < 4.1) $rs = mysql_query($query,$conn);
				else $rs = mysql_query(str_replace('#~lang~#',$dblang,$query),$conn);
				$query='';
			} else if(!preg_match("#^(\/\/|--)#", $line))
			{
				$query .= $line;
			}
		}
		fclose($fp);
	}
	else
	{
	
	}
    //锁定安装程序
        $fp = fopen($insLockfile,'w');
        fwrite($fp,'ok');
        fclose($fp);
	//预先加载应用,并删除缓存
		app_cache($cfg_cmspath);
        include('./templates/step-5.html');
        exit();
}/*------------------------
检测数据库是否有效
function _10_TestDbPwd()
------------------------*/
else if($step==10)
{
    header("Pragma:no-cache\r\n");
    header("Cache-Control:no-cache\r\n");
    header("Expires:0\r\n");
    $conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
    if($conn)
    {
		if(empty($dbname)){
			echo "<font color='green'>信息正确</font>";
		}else{
			$info = mysql_select_db($dbname,$conn)?"<font color='red'>数据库已经存在，系统将覆盖数据库</font>":"<font color='green'>数据库不存在,系统将自动创建</font>";
			echo $info;
		}
    }
    else
    {
        echo "<font color='red'>数据库连接失败！</font>";
    }
    @mysql_close($conn);
    exit();
}
	function xmd5($str,$pattern='wkcms',$num=1)
	{
		$ref = md5(strrev($str.$pattern));
		for($i=1;$i<=$num;$i++)
		{
			$ref = md5(strrev($ref.$pattern));
		}
		return $ref;
	}
?>