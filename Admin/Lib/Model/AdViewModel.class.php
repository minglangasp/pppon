<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 广告-分类视图

    @Filename AdViewModel.class.php $

    @Author pengyong $

    @Date 2013-01-03 22:20:52 $
*************************************************************/
class AdViewModel extends ViewModel {
   public $viewFields = array(
   'ad'=>array('id','status','title','pubdate','gid','_type'=>'LEFT'),
   'ad_group'=>array('name'=>'groupname','_on'=>'ad.gid=ad_group.id'),
   );
   }