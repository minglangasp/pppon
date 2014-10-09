<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function 基础文档-栏目视图模型

    @Filename ArchiveViewModel.class.php $

    @Author pengyong $

    @Date 2012-12-17 19:50:17 $
*************************************************************/
class ArchiveViewModel extends ViewModel {
   public $viewFields = array(
   'archive'=>array('id','typeid','flag','modelid','senddate',
   'pubdate','click','keywords','description','money','arcrank',
   'writer','title','shorttitle','color','source',
   'litpic','mid','_type'=>'LEFT'), 
   'arctype'=>array('typename','_on'=>'archive.typeid=arctype.id'), 
   );
   }