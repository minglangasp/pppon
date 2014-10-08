<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 文档-栏目视图模型

    @Filename ArchiveArctypeViewModel.class.php $

    @Author pengyong $

    @Date 2012-12-17 19:50:17 $
*************************************************************/
class ArchiveArctypeViewModel extends ViewModel {
   public $viewFields = array(
   'archive'=>array('id','typeid','flag','modelid','senddate',
   'pubdate','click','keywords','description','money','arcrank',
   'writer','title','shorttitle','color','source',
   'litpic','mid','_type'=>'LEFT'), 
   'arctype'=>array('id'=>'arctype_id','modelid'=>'arctype_modelid',
   'typename'=>'arctype_typename','linkurl'=>'arctype_linkurl',
   'seotitle'=>'arctype_seotitle','keywords'=>'arctype_keywords',
   'description'=>'arctype_description',
   'content'=>'arctype.content','sortrank'=>'arctype_sortrank',
   'fid'=>'arctype_fid','path'=>'arctype_path',
   'tempindex'=>'arctype_tempindex','templist'=>'arctype_templist',
   'temparticle'=>'arctype_temparticle','waptempindex'=>'arctype_waptempindex',
   'waptemplist'=>'arctype_waptemplist', 'waptemparticle'=>'arctype_waptemparticle',
   '_on'=>'archive.typeid=arctype.id','_type'=>'LEFT'), 
   'member'=>array('id'=>'member_id','username'=>'member_username','_on'=>'archive.mid=member.id'),
   );
   }