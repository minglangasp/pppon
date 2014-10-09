<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 会员表视图模型

    @Filename MemberViewModel.class.php $

    @Author pengyong $

    @Date 2012-12-22 17:48:22 $
*************************************************************/
class MemberViewModel extends ViewModel {
   public $viewFields = array(
   'member'=>array('id','username','status','money','sex','province','city','birthday','qq','email','regtime','loginip','rankid','_type'=>'LEFT'),
   'member_rank'=>array('rankname','rankmoney','rankimg','groupid','_on'=>'member.rankid = member_rank.id'), 
   );
   }