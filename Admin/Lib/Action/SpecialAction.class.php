<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 专题管理

    @Filename SpecialAction.class.php $

    @Author pengyong $

    @Date 2013-01-06 13:31:09 $
*************************************************************/
class SpecialAction extends CommonAction
{	
    public function index()
	{
		import('@.ORG.Page');
		$model = M('special');
		$map['id'] = array('gt',0);
		$count = $model->where($map)->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model->getLastSql();exit;
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>");
		$this->assign('page',$p->show());
		$this->assign("list",$list);
		$this->display();
	}
	
	public function  add()
	{
		$this->display();
	}
	
	public function doadd()
	{
		$data = $this->getdata('title,keywords,description,seotitle,content,tempindex,waptempindex');
		$data['description'] = stripslashes($data['description']);
		$data['content'] = stripslashes($data['content']);
		$data['pubdate'] = time();
		$model  = M('special');
		$model->add($data);
		$this->success('操作成功!',U('Special/index'));
	}
	
	public function edit()
	{
		$model = M('special');
		$list = $model->where($map)->find();
		$this->assign("list",$list);
		$this->display();
	}
	
	public function doedit()
	{
		$data = $this->getdata('id,title,keywords,description,seotitle,content,tempindex,waptempindex');
		$data['description'] = stripslashes($data['description']);
		$data['content'] = stripslashes($data['content']);
		$data['pubdate'] = time();
		$model  = M('special');
		$model->save($data);
		$this->success('操作成功!',U('Special/index'));
	}
	
	public function del()
	{
		$map['id'] = $this->_get('id');
		$model = M('special');
		if(!$model->where($map)->find())$this->error('专题不存在!');
		$model->where($map)->delete();
		$this->success('操作成功',U('Special/index'));
	}
	
	private function getdata($data)
	{
		$map = array();
		$data =  explode(',',$data);
		foreach($data as $v)
		{
			$map[$v] = $_POST[$v];
		}
		return $map;
	}
	public function doimport()
	{
		$filename = $this->_post('filename');
		$checkdir = $this->_post('checkdir');
		if(strtolower(substr($filename,-4))<> '.zip') $this->error('仅支持后缀为zip的压缩包');
		$path = ltrim($filename,__ROOT__.'/');
		$filename = substr(ltrim(strrchr($filename,'/'),'/'),0,-4);
		$tplpath = './Public/Special/'.$filename;
		if(is_dir($tplpath) && $checkdir<>1) $this->error('专题目录已存在!');
		if(!is_file($path)) $this->error('文件包不存在!');
		File::mk_dir('./Public/Special');
		import('ORG.PclZip');
		$zip =  new PclZip($path);
		$zip->extract(PCLZIP_OPT_PATH,$tplpath); 
		//导入数据
		$xmlpath = './Public/Special/'.$filename.'/special.xml';
		if(is_file($xmlpath))
		{
			$xml = simplexml_load_file($xmlpath);
			$data['title'] = (string)$xml->title;
			$data['seotitle'] = (string)$xml->seotitle;
			$data['keywords'] = (string)$xml->keywords;
			$data['description'] = (string)$xml->description;
			$data['content'] = (string)$xml->content;
			$data['tempindex'] = (string)$xml->tempindex;
			$data['waptempindex'] = (string)$xml->waptempindex;
			$data['pubdate'] = time();
			$model = M('special');
			$model->add($data);
		}
		$this->success('操作成功!',U('Special/index'));
	}
	
	//远程安装专题
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
		if(empty($content)){
			$this->assign('waitSecond',20);
			$this->error('远程获取文件失败!,<a href="'.$url.'" target="_blank">本地下载安装</a>');
		} 
		$filename = substr($filepath,0,-4);
		$tplpath = './Public/Special/'.$filename;
		if(is_dir($tplpath)) $this->error('专题目录已存在!');
		File::write_file($filepath,$content);
		import('ORG.PclZip');
		$zip =  new PclZip($filepath);
		$zip->extract(PCLZIP_OPT_PATH,$tplpath); 
		@unlink($filepath);//删除安装文件
		//导入数据
		$xmlpath = './Public/Special/'.$filename.'/special.xml';
		if(is_file($xmlpath))
		{
			$xml = simplexml_load_file($xmlpath);
			$data['title'] = (string)$xml->title;
			$data['seotitle'] = (string)$xml->seotitle;
			$data['keywords'] = (string)$xml->keywords;
			$data['description'] = (string)$xml->description;
			$data['content'] = (string)$xml->content;
			$data['tempindex'] = (string)$xml->tempindex;
			$data['waptempindex'] = (string)$xml->waptempindex;
			$data['pubdate'] = time();
			$model = M('special');
			$model->add($data);
		}
		$this->success('操作成功!',U('Special/index'));
	}
}
?>