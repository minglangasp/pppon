<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 wap首页

    @Filename WapAction.class.php $

    @Author pengyong $

    @Date 2013-01-06 15:12:24 $
*************************************************************/
class WapAction extends CommonAction 
{
    public function index()
	{
		//仅做映射
		header('location:'.__ROOT__.'?mode=wap');
	}
}