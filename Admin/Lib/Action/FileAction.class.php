<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 文件上传

    @Filename FileAction.class.php $

    @Author pengyong $

    @Date 2011-11-23 10:11:22 $
*************************************************************/
import('ORG.UploadFile');
import('ORG.File');
class FileAction extends CommonAction
{
	
	
	//上传图片缩略图
	public function thumb()
	{
		$this->display('thumb');
	}
	
	//执行上传缩略图
	public function uploadthumb()
	{
		$this->upmethod('upthumb','t');
	}
	//上传附件
	public function attach()
	{
		$this->display('attach');
	}
	//执行上传附件
	public function uploadattach()
	{
	$this->upmethod('upattach','at');
	}

	//上传临时文件
	public function temp()
	{
		$this->display('temp');
	}
	//执行上传临时文件
	public function uploadtemp()
	{
	$this->upmethod('uptemp','tem');
	}

	
	//缩略图代码处理
	private function t($data)
	{
		$js='';
		if(!empty($data[0]['savename']))
		{
			$js.="<script language=javascript>parent.document.myform.Images.value='".__PUBLIC__."/Uploads/image/".date('Ymd')."/{$data[0]['savename']}';</script>";
			$js.="<script language=javascript>parent.document.getElementById('flag-p').checked=true;</script>";
			$js.="<script language=javascript>parent.editor.appendHtml('<div align=\"center\"><img src=\"".__PUBLIC__."/Uploads/image/".date('Ymd')."/{$data[0]['savename']}\"/></div>');</script>";
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//临时文件 代码处理
	private function tem($data)
	{
		$js='';
		if(!empty($data[0]['savename']))
		{
			$js.="<script language=javascript>parent.document.myform.temp.value='".__PUBLIC__."/Uploads/temp/".date('Ymd')."/{$data[0]['savename']}';</script>";
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//附件代码处理
	private function at($data)
	{
		$js='';
		if(!empty($data[0]['savename']))
		{
			switch($data[0]['extension'])
			{
				case 'zip':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/zip.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'tar.gz':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/zip.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case '7z':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/zip.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'rar':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/rar.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'doc':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/doc.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'docx':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/doc.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'ppt':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/ppt.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'pptx':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/ppt.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'xls':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/xls.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'xlsx':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/xls.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'txt':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/txt.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'pdf':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/pdf.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				case 'swf':
					$js.="<script language=javascript>parent.editor.appendHtml( '<br>附件下载:<img src=\"".__PUBLIC__."/Editor/kindeditor/plugins/insertfile/mini/swf.gif\"/><a href=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\">".$data[0]['savename']."</a></br>');</script>";
					break;
				default:
					$js.="<script language=javascript>parent.editor.appendHtml( '<img src=\"".__PUBLIC__."/Uploads/attach/".$data[0]['savename']."\" />');</script>";
				//default 默认为gif,png,jpg等图片
			}
			$this->assign('js',$js);
			return true;
		}
		else
		{
			return false;
		}
	}


	//*********************以下是执行上传的方法**************************




	private function upthumb()
	{
		$upload=new UploadFile();
		$upload->maxSize='102400';  //上传文件大小的限制 1M
		$upload->savePath='./Public/Uploads/image/'.date('Ymd').'/';   //上传的路径   
		File::mk_dir("./Public/Uploads/image");
		$upload->uploadReplace=true;     
		$upload->allowExts=array('jpg','jpeg','png','gif');     //准许上传的文件后缀
		$upload->allowTypes=array('image/jpeg','image/pjpeg','image/png','image/gif','image/x-png');//准许上传的文件类型
		$upload->imageClassPath = 'ORG.Image';
		$upload->thumb = true;   //是否开启图片文件缩略,true表示开启
		$upload->thumbMaxWidth='500';  //以字串格式来传，如果你希望有多个，那就在此处，用,分格，写上多个最大宽
		$upload->thumbMaxHeight='400';	
		$upload->thumbPrefix='thumb_';//缩略图文件前缀
		$upload->thumbPath='./Public/Uploads/image/'.date('Ymd').'/'; 
		$upload->thumbRemoveOrigin=1;
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			//再次改名,防止文件重复上传
			$oldfile = $upload->thumbPath.$upload->thumbPrefix.$info[0]['savename'];
			$hashfile = hash_file('crc32',$oldfile).'.'.$info[0]['extension'];//HASH规则
			$newfile = $upload->thumbPath.$hashfile;
			if(!rename($oldfile,$newfile)) @unlink($oldfile);
			$info[0]['savename'] = $hashfile;
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}
	
	//上传附件方法
	private function upattach()
	{
		$upload=new UploadFile();
		$upload->maxSize='1024000';  
		$upload->savePath='./Public/Uploads/file/'.date('Ymd').'/'; 
		File::mk_dir("./Public/Uploads/file");		
		$upload->saveRule= uniqid;   
		$upload->uploadReplace = true; 
		$upload->allowExts = array('zip','rar','txt','ppt','pptx','xls','xlsx','doc','docx','swf','jpg','png','gif','tar.gz','.7z');     //准许上传的文件后缀
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}
	
	//上传附件方法
	private function uptemp()
	{
		$upload=new UploadFile();
		$upload->maxSize='1024000';  
		$upload->savePath='./Public/Uploads/temp/'.date('Ymd').'/'; 
		File::mk_dir("./Public/Uploads/temp");		
		$upload->saveRule= uniqid;   
		$upload->uploadReplace = true; 
		$upload->allowExts = array('zip','xml','rar','txt','ppt','pptx','xls','xlsx','doc','docx','swf','jpg','png','gif','tar.gz','.7z');     //准许上传的文件后缀
		if($upload->upload())
		{
			$info=$upload->getUploadFileInfo();
			return $info;
		}
		else
		{
			$this->error($upload->getErrorMsg());
		}
	}
	
	

	//上传方法,提取公共代码
	private function upmethod($upload,$method)
	{
		if(empty($_FILES))
		{
			$this->error('必须选择上传文件');
			
		}
		$a=$this->$upload();
		if(isset($a))
		{
			if($this->$method($a))
			{
				$this->success('上传成功');
			}
			else
			{
				$this->error('插入文本框失败');
			}
		}
		else
		{
			$this->error('上传文件有异常请与系统管理员联系');
		}
		
	}
	
	function  error($msg)
	{
		$this->assign("message",$msg);
		$this->assign('waitSecond',"3");
		$this->assign('jumpUrl',"javascript:history.back(-1);");
		$this->display('null');
	}
	function success($msg)
	{
		$this->assign("message",$msg);
		$this->assign('waitSecond',"1");
		$this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
		$this->display('null');
	}
}
?>