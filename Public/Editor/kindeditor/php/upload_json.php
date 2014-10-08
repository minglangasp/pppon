<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 编辑器上传json

    @Filename upload_json.php $

    @Author pengyong $

    @Date 2012-12-18 15:45:35 $
*************************************************************/
error_reporting(0);
session_start();
define('THINK_PATH', true);
$config = include('../../../Config/config.ini.php');
require_once 'JSON.php';
$uname = cookie('uname');
$uid = cookie('uid');
$cmsauth = $_SESSION['cmsauth'];
$nowauth = substr(md5(strrev($uname).'waikucms'.$uid),0,10);
//管理员
if(strcmp($cmsauth,$nowauth) <> 0 && !isset($_GET['userup']))
{
	alert('管理员身份信息认证失败！请重新登陆！');
}
//普通会员
if(isset($_GET['userup']) && $_GET['userup']==1)
{
	$wkcode = cookie('wkcode');
	if(strcmp($wkcode,xmd5($uid.$uname,3)) <> 0 )
	{
		alert('会员身份信息认证失败！请重新登陆！');
	}
}
$php_path = dirname(__FILE__) . '/';
$php_url = dirname($_SERVER['PHP_SELF']) . '/';

//文件保存目录路径
$save_path = $php_path . '../../../Uploads/';
//文件保存目录URL
$save_url = $php_url . '../../../Uploads/';
//定义允许上传的文件扩展名
$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
	'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt','txt', 'zip', 'rar', 'gz', 'bz2', 'swf', 'flv', 'mp3'),
);
//最大文件大小
$max_size = 50000000;

$save_path = realpath($save_path) . '/';

//PHP上传失败
if (!empty($_FILES['imgFile']['error'])) {
	switch($_FILES['imgFile']['error']){
		case '1':
			$error = '超过php.ini允许的大小。';
			break;
		case '2':
			$error = '超过表单允许的大小。';
			break;
		case '3':
			$error = '图片只有部分被上传。';
			break;
		case '4':
			$error = '请选择图片。';
			break;
		case '6':
			$error = '找不到临时目录。';
			break;
		case '7':
			$error = '写文件到硬盘出错。';
			break;
		case '8':
			$error = 'File upload stopped by extension。';
			break;
		case '9':
			$error = '身份信息认证失败！请重新登陆！';
			break;
		case '999':
		default:
			$error = '未知错误。';
	}
	alert($error);
}

//有上传文件时
if (empty($_FILES) === false) {
	//原文件名
	$file_name = $_FILES['imgFile']['name'];
	//服务器上临时文件名
	$tmp_name = $_FILES['imgFile']['tmp_name'];
	//文件大小
	$file_size = $_FILES['imgFile']['size'];
	//检查文件名
	if (!$file_name) {
		alert("请选择文件。");
	}
	//检查目录
	if (@is_dir($save_path) === false) {
		alert("上传目录不存在。");
	}
	//检查目录写权限
	if (@is_writable($save_path) === false) {
		alert("上传目录没有写权限。");
	}
	//检查是否已上传
	if (@is_uploaded_file($tmp_name) === false) {
		alert("上传失败。");
	}
	//检查文件大小
	if ($file_size > $max_size) {
		alert("上传文件大小超过限制。");
	}
	//检查目录名
	$dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
	if (empty($ext_arr[$dir_name])) {
		alert("目录名不正确。");
	}
	//获得文件扩展名
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	//检查扩展名
	if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
		alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
	}
	//创建文件夹
	if ($dir_name !== '') {
		$save_path .= $dir_name . "/";
		$save_url .= $dir_name . "/";
		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
	}
	$ymd = date("Ymd");
	$save_path .= $ymd . "/";
	$save_url .= $ymd . "/";
	if (!file_exists($save_path)) {
		mkdir($save_path);
	}
	//新文件名
	//$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
	//py 按 hash命名文件,有效防止文件重复上传
	$new_file_name = hash_file('crc32', $tmp_name). '.' . $file_ext;
	//保留原名
	if($_GET['rename']==1) $new_file_name = $file_name;
	//移动文件
	$file_path = $save_path . $new_file_name;
	if (move_uploaded_file($tmp_name, $file_path) === false) {
		alert("上传文件失败。");
	}
	//图片剪裁
	if($_GET['resize']==1) 
	{
		$img_width = empty($_GET['width']) ? '100' :$_GET['width'];
		$img_height = empty($_GET['height']) ? '100' :$_GET['height'];
		$resizeimage = new resizeimage($file_path, $img_width, $img_height, "1",$file_path);    
	}  
	@chmod($file_path, 0644);
	$file_url = $save_url . $new_file_name;
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 0, 'url' => $file_url));
	exit;
}

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}

function cookie($name)
{
	global $config;
	$prefix = $config['COOKIE_PREFIX'];
	return $_COOKIE[$prefix.$name];
}

class resizeimage      
{      
    //图片类型      
    var $type;      
    //实际宽度      
    var $width;      
    //实际高度      
    var $height;      
    //改变后的宽度      
    var $resize_width;      
    //改变后的高度      
    var $resize_height;      
    //是否裁图      
    var $cut;      
    //源图象      
    var $srcimg;      
    //目标图象地址      
    var $dstimg;      
    //临时创建的图象      
    var $im;      
    function resizeimage($img, $wid, $hei,$c,$dstpath)      
    {      
        $this->srcimg = $img;      
        $this->resize_width = $wid;      
        $this->resize_height = $hei;      
        $this->cut = $c;      
        //图片的类型      
     
$this->type = strtolower(substr(strrchr($this->srcimg,"."),1));      
        //初始化图象      
        $this->initi_img();      
        //目标图象地址      
        $this -> dst_img($dstpath);      
        //--      
        $this->width = imagesx($this->im);      
        $this->height = imagesy($this->im);      
        //生成图象      
        $this->newimg();      
        ImageDestroy ($this->im);      
    }      
    function newimg()      
    {      
        //改变后的图象的比例      
        $resize_ratio = ($this->resize_width)/($this->resize_height);      
        //实际图象的比例      
        $ratio = ($this->width)/($this->height);      
        if(($this->cut)=="1")      
        //裁图      
        {      
            if($ratio>=$resize_ratio)      
            //高度优先      
            {      
                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);      
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width,$this->resize_height, (($this->height)*$resize_ratio), $this->height);      
                ImageJpeg ($newimg,$this->dstimg);      
            }      
            if($ratio<$resize_ratio)      
            //宽度优先      
            {      
                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);      
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, $this->resize_height, $this->width, (($this->width)/$resize_ratio));      
                ImageJpeg ($newimg,$this->dstimg);      
            }      
        }      
        else     
        //不裁图      
        {      
            if($ratio>=$resize_ratio)      
            {      
                $newimg = imagecreatetruecolor($this->resize_width,($this->resize_width)/$ratio);      
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, ($this->resize_width)/$ratio, $this->width, $this->height);      
                ImageJpeg ($newimg,$this->dstimg);      
            }      
            if($ratio<$resize_ratio)      
            {      
                $newimg = imagecreatetruecolor(($this->resize_height)*$ratio,$this->resize_height);      
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, ($this->resize_height)*$ratio, $this->resize_height, $this->width, $this->height);      
                ImageJpeg ($newimg,$this->dstimg);      
            }      
        }      
    }      
    //初始化图象      
    function initi_img()      
    {      
        if($this->type=="jpg")      
        {      
            $this->im = imagecreatefromjpeg($this->srcimg);      
        }      
        if($this->type=="gif")      
        {      
            $this->im = imagecreatefromgif($this->srcimg);      
        }      
        if($this->type=="png")      
        {      
            $this->im = imagecreatefrompng($this->srcimg);      
        }      
    }      
    //图象目标地址      
    function dst_img($dstpath)      
    {      
        $full_length  = strlen($this->srcimg);      
        $type_length  = strlen($this->type);      
        $name_length  = $full_length-$type_length;      
     
        $name         = substr($this->srcimg,0,$name_length-1);      
        $this->dstimg = $dstpath;      
     
//echo $this->dstimg;      
    }      
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