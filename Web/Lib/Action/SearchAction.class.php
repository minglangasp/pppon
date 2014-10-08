<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 搜索处理

    @Filename SearchAction.class.php $

    @Author pengyong $

    @Date 2013-02-06 15:43:59 $
*************************************************************/
class SearchAction extends CommonAction 
{
    public function index()
	{
		global $_mode,$cfg_df_style,$cfg_wap_tpl_default;
		$GLOBALS['_fields']['position'] = $this->position();
		$filename = './Public/Tpl/'.$cfg_df_style.'/search.htm';
		//wap支持
		if($_mode =='wap') $filename = './Public/Wap/'.$cfg_wap_tpl_default.'/search.htm';
		if(!file_exists($filename)) $this->error('主题文件:'.$filename.' 不存在!');
		$GLOBALS['_fields']['keyword'] = $_REQUEST['keyword'];
		//统计TAG关键词搜索次数
		if(isset($_GET['tag']) && $_GET['tag']==1)
		{
			$model = M('tags');
			$map['keyword'] = $_REQUEST['keyword'];
			$model->where($map)->setInc('searchnum',1); 
		}
		//搜索页面不缓存编译文件
		C('TMPL_CACHE_ON',false);
		$this->display($filename);
	}
}