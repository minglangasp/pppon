<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function User组 文档-用户视图

    @Filename ArchiveViewModel.class.php $

    @Author pengyong $

    @Date 2013-01-11 17:30:51 $
*************************************************************/
class ArchiveViewModel extends ViewModel {
   public $viewFields = array(
   'archive'=>array('id','typeid','flag','modelid','senddate',
   'pubdate','click','keywords','description','money','arcrank',
   'writer','title','shorttitle','color','source',
   'litpic','mid','_type'=>'LEFT'), 
   'member'=>array('username','_on'=>'archive.mid=member.id'),
   );
   }