<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Admin组 微型表视图模型

    @Filename ArchivetinyViewModel.class.php $

    @Author pengyong $

    @Date 2012-12-22 17:48:22 $
*************************************************************/
class ArctinyViewModel extends ViewModel {
   public $viewFields = array(
   'arctiny'=>array('id','typeid','modelid','mid','senddate'),
   'archive'=>array('flag','arcrank','pubdate','click','color','title','_on'=>'archive.id=arctiny.id','_type'=>'LEFT'), 
	//  'arcmodel'=>array('typename'=>'arcmodel_typename','_on'=>'arcmodel.id=arctiny.modelid','_type'=>'LEFT'),
   'arctype'=>array('typename','_on'=>'arctiny.typeid=arctype.id','_type'=>'LEFT'), 
   'member'=>array('username','_on'=>'arctiny.mid=member.id'),
   );
   }