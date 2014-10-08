<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function WEB组 Flash首页

    @Filename FlashAction.class.php $

    @Author Cream $

    @Date 2014-01-20 14:50:24 $
*************************************************************/
class FlashAction extends CommonAction 
{
    public function index()
	{
		//仅做映射
		header('location:'.__ROOT__.'?mode=flash');
	}
}