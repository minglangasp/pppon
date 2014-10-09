		<?php
			class addondiyinfoViewModel extends ViewModel 
			{
				public $viewFields = array
				(
					'archive'=>array('id','typeid','flag','modelid','senddate',
					'pubdate','click','keywords','description','money','arcrank',
					'writer','title','shorttitle','color','source',
					'litpic','voteid','mid','_type'=>'LEFT'), 
					'arctype'=>array('typename','temparticle','waptemparticle','_on'=>'archive.typeid=arctype.id','_type'=>'LEFT'),
					'member'=>array('username','_on'=>'archive.mid=member.id'),
					'addondiyinfo'=>array('body','img','open','redirecturl','simg','topic','url','yheight','ywidth','_on'=>'archive.id=addondiyinfo.id'), 
				);
			}