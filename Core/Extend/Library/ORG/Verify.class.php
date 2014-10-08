<?php
/******************************************************************
	@filename: Verify.class.php
	
	@function: 验证码处理类

	@author:pengyong.info
	
	@date: 2012-10-17 15:25:35

	@copyright: centphp.com
*******************************************************************/
class Verify
{			

	public function __construct()
	{
		$this->domain ='';		// 域名域
		$this->font_size =	14;
		$this->img_height = 24;
		$this->word_type = 3;	// 1:数字  2:英文   3:单词
		$this->img_width = 68;
		$this->use_boder = true;
		$this->font_path = './Public/Verify/fonts/';				//字体库路径
		$this->font_file = 'ggbi.ttf'; 										//字体路径
		$this->wordlist_file = './Public/Verify/words/words.txt'; //单词库
		$this->filter_type = 5;											  //滤镜模式,等于0 则带边框
		$this->sessSavePath = './Public/Verify/sessions/';//SESSION 保存位置
		if(!class_exists('File')) import("ORG.File");
	}
	
	//数组方式设定参数
	public function config($config='')
	{
		if(!empty($config))
		{
			foreach($config as $k=>$v)
			{
				$this->$k = $v;
			}
		}
		return $this;
	}
	//SESSION 保存路径设置
	public function session($dir='')
	{
		if(!empty($dir)) $this->sessSavePath = $dir; File::mk_dir($this->sessSavePath);
		return $this;
	}
	
	//COOKIE 域
	public function domain($domain='')
	{
		$this->domain = $domain;
		return $this;
	}
	
	//输出
	public function display() 
	{
		// Session保存路径
		if(is_writeable($this->sessSavePath) && is_readable($this->sessSavePath)){ session_save_path($this->sessSavePath); }
		if(!empty($this->domain)) session_set_cookie_params(0,'/',$this->domain);
		if(!$this->show_image())
		{
			// 如果不成功则初始化一个默认验证码
			@session_start();
			$_SESSION['verify'] = strtolower('abcd');
			$im = @imagecreatefromjpeg('./Public/Verify/vdcode.jpg');
			header("Pragma:no-cache\r\n");
			header("Cache-Control:no-cache\r\n");
			header("Expires:0\r\n");
			imagejpeg($im);
			imagedestroy($im);
		}
	}
	//显示图片
	public function show_image()
	{
		@session_start();
		//主要参数
		$font_size   = $this->font_size;
		$img_height  = $this->img_height;
		$img_width   = $this->img_width;
		$font_file   = $this->font_path.$this->font_file;
		$use_boder   = $this->use_boder;
		$filter_type = $this->filter_type;
		
		//创建图片，并设置背景色
		$im = @imagecreate($img_width, $img_height);
		imagecolorallocate($im, 255,255,255);
		
		//文字随机颜色
		$fontColor[]  = imagecolorallocate($im, 0x15, 0x15, 0x15);
		$fontColor[]  = imagecolorallocate($im, 0x95, 0x1e, 0x04);
		$fontColor[]  = imagecolorallocate($im, 0x93, 0x14, 0xa9);
		$fontColor[]  = imagecolorallocate($im, 0x12, 0x81, 0x0a);
		$fontColor[]  = imagecolorallocate($im, 0x06, 0x3a, 0xd5);
		
		//获取随机字符
		$rndstring  = '';
		if ($this->word_type != 3)
		{
			for($i=0; $i<4; $i++)
			{
				if ($this->word_type == 1)
				{
					$c = chr(mt_rand(48, 57));
				} else if($this->word_type == 2)
				{ 
					$c = chr(mt_rand(65, 90));
					if( $c=='I' ) $c = 'P';
					if( $c=='O' ) $c = 'N';
				}
				$rndstring .= $c;
			}
		} else { 
			$fp = @fopen($this->wordlist_file, 'rb');
			if (!$fp) return FALSE;

			$fsize = filesize($this->wordlist_file);
			if ($fsize < 32) return FALSE;

			if ($fsize < 128) 
			{
			  $max = $fsize;
			} else {
			  $max = 128;
			}

			fseek($fp, rand(0, $fsize - $max), SEEK_SET);
			$data = fread($fp, 128);
			fclose($fp);
			$data = preg_replace("/\r?\n/", "\n", $data);

			$start = strpos($data, "\n", rand(0, 100)) + 1; 
			$end   = strpos($data, "\n", $start); 
			$rndstring  = strtolower(substr($data, $start, $end - $start)); 
		}
		
		$_SESSION['verify'] = strtolower($rndstring);

		$rndcodelen = strlen($rndstring);

		//背景横线
		$lineColor1 = imagecolorallocate($im, 0xda, 0xd9, 0xd1);
		for($j=3; $j<=$img_height-3; $j=$j+3)
		{
			imageline($im, 2, $j, $img_width - 2, $j, $lineColor1);
		}
		
		//背景竖线
		$lineColor2 = imagecolorallocate($im, 0xda,0xd9,0xd1);
		for($j=2;$j<100;$j=$j+6)
		{
			imageline($im, $j, 0, $j+8, $img_height, $lineColor2);
		}

		//画边框
		if( $use_boder && $filter_type == 0 )
		{
			$bordercolor = imagecolorallocate($im, 0x9d, 0x9e, 0x96);
			imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $bordercolor);
		}
		
		//输出文字
		$lastc = '';
		for($i=0;$i<$rndcodelen;$i++)
		{
			$bc = mt_rand(0, 1);
			$rndstring[$i] = strtoupper($rndstring[$i]);
			$c_fontColor = $fontColor[mt_rand(0,4)];
			$y_pos = $i==0 ? 4 : $i*($font_size+2);
			$c = mt_rand(0, 15);
			@imagettftext($im, $font_size, $c, $y_pos, 19, $c_fontColor, $font_file, $rndstring[$i]);
			$lastc = $rndstring[$i];
		}
		
		//图象效果
		switch($filter_type)
		{
			case 1:
				imagefilter ( $im, IMG_FILTER_NEGATE);
				break;
			case 2:
				imagefilter ( $im, IMG_FILTER_EMBOSS);
				break;
			case 3:
				imagefilter ( $im, IMG_FILTER_EDGEDETECT);
				break;
			default:
				break;
		}

		header("Pragma:no-cache\r\n");
		header("Cache-Control:no-cache\r\n");
		header("Expires:0\r\n");

		//输出特定类型的图片格式，优先级为 gif -> jpg ->png
		//dump(function_exists("imagejpeg"));
		
		if(function_exists("imagejpeg"))
		{
			header("content-type:image/jpeg\r\n");
			imagejpeg($im);
		}
		else
		{
			header("content-type:image/png\r\n");
			imagepng($im);
		}
		imagedestroy($im);
		exit();
	}
	

}