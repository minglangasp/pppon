<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 扩展市场

    @Filename MarketAction.class.php $

    @Author pengyong $

    @Date 2013-4-12 22:16:08 $
*************************************************************/
class MarketAction extends CommonAction
{	
	public function plugin()
	{
		$host = $this->gethostinfo();
		header('Location:http://cloud.waikucms.net/market.php?type=plugin&host='.$host);
	}
	
	public function theme()
	{
		$host = $this->gethostinfo();
		header('Location:http://cloud.waikucms.net/market.php?type=theme&host='.$host);
	}
	
	public function special()
	{
		$host = $this->gethostinfo();
		header('Location:http://cloud.waikucms.net/market.php?type=special&host='.$host);
	}
	
	public function model()
	{
		$host = $this->gethostinfo();
		header('Location:http://cloud.waikucms.net/market.php?type=model&host='.$host);
	}
	
	public function index()
	{
		$host = $this->gethostinfo();
		header('location:http://cloud.waikucms.net/market.php?type=cloud&host='.$host);
	}
	
	public function app()
	{
		$host = $this->gethostinfo();
		header('location:http://cloud.waikucms.net/market.php?type=app&host='.$host);
	}
	
	private function gethostinfo()
	{
		$host['domain'] = $_SERVER['SERVER_NAME'];
		$host['ip'] = $_SERVER['SERVER_ADDR'];
		$host['version'] = C('SOFT_VERSION');
		$host['baseurl'] = 'http://'.$host['domain'].__APP__;
		return base64_encode(serialize($host));
	}
}
?>