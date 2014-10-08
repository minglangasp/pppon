<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 友情链接-分类视图

    @Filename FriendlinkViewModel.class.php $

    @Author pengyong $

    @Date 2013-01-03 22:20:52 $
*************************************************************/
class FriendlinkViewModel extends ViewModel {
   public $viewFields = array(
   'friendlink'=>array('id','status','title','img','pubdate','gid','_type'=>'LEFT'),
   'friendlink_group'=>array('name'=>'groupname','_on'=>'friendlink.gid=friendlink_group.id'),
   );
   }