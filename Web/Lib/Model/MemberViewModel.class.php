<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function Web组 会员表视图模型

    @Filename MemberViewModel.class.php $

    @Author pengyong $

    @Date 2013-01-09 14:35:13 $
*************************************************************/
class MemberViewModel extends ViewModel {
   public $viewFields = array(
   'member'=>array('id','username','email','status','logintime','regtime','loginip','money','rankid','sex','province','city','qq','birthday','_type'=>'LEFT'), 
   'member_rank'=>array('rankname','rankmoney','rankimg','groupid','_on'=>'member.rankid=member_rank.id'), 
   );
   }