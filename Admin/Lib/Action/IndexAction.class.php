<?php
class IndexAction extends CommonAction 
{
  //首页显示框架主体index
    public function index()
    {
       $this->display();
    }
	
//首页显示框架内left页面
	public function left()
    {
		//内容管理自动获取
		$arcmodel = M('arcmodel');
		$map['status'] = 0;
		$list = $arcmodel->where($map)->select();
		$this->assign('list',$list);
        $this->display();
    }
	
//首页显示框架内head头部页面
	public function head()
    {
        $this->display();
    }
//首页显示框架内bottom底部页面
	public function bottom()
    {
        $this->display();
    }
//首页显示框架内center页面包含了left和main
	public function center()
    {
        $this->display();
    }
	//首页显示框架内右侧主页面
	public function main()
    {
		//安全密码检测
		$saveauth = F('safeauth','',COMMON_PATH);
		if(!empty($saveauth)) $this->assign('safeauth',1);
		//模型
		$arcmodel = M('arcmodel');
		$arcmodellist = $arcmodel->field('typename,id')->where('status=0')->select();
		$this->assign("arcmodellist",$arcmodellist);
		//最新投递
		$arctiny = M('arctiny');
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$week = time() - 7*24*3600;
		$month = time() - 30*24*3600;
		$map['senddate'] = array('egt',$today);
		$todaynum = $arctiny->where($map)->count();
		$map['senddate'] = array('egt',$week);
		$weeknum = $arctiny->where($map)->count();
		$map['senddate'] = array('egt',$month);
		$monthnum = $arctiny->where($map)->count();
		unset($map['senddate']);
		$map['arcrank'] = 8;
		$archive =  M('archive');
		$recyclenum = $archive->where($map)->count();
		$map['arcrank'] = array('in','1,2,3');
		$postednum = $archive->where($map)->count();
		$map['arcrank'] = 4;
		$unpostednum = $archive->where($map)->count();
		//栏目总数
		$arctype = M('arctype');
		$typenum =  $arctype->count();
		$totalnum = $arctiny->count();
		
		$num = array(
		'todaynum'=>$todaynum,
		'weeknum'=>$weeknum,
		'monthnum'=>$monthnum,
		'totalnum'=>$totalnum,
		'recyclenum'=>$recyclenum,
		'postednum'=>$postednum,
		'unpostednum'=>$unpostednum,
		'typenum'=>$typenum,
		);
		$this->assign('num',$num);
		$info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time').'秒',
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s",time() + 8 * 3600),
            '服务器域名' => $_SERVER['SERVER_NAME'],
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)),2).'M',
            'register_globals' => get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
		$this->display();
    }
}