<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 系统全局配置

    @Filename ConfigAction.class.php $

    @Author pengyong $

    @Date 2012-11-06 19:47:41 $
*************************************************************/
class ConfigAction extends CommonAction
{	
	public function index()
	{
		//安全验证
		$this->checksafeauth();
		$model = M('config_group');
		$grouplist =  $model->select();
		$groupnum  =  $model->count();
		foreach($grouplist as $k=>$v)
		{
			$configlist[] = $this->getgroup($v['id']);
		}
		$this->assign("grouplist",$grouplist);
		$this->assign("groupnum",$groupnum);
		$this->assign("percent",(100-$groupnum)/$groupnum);
		$this->assign("configlist",$configlist);
		$this->display();
	} 
	
	private function getgroup($gid=1)
	{
		//隐藏一些配置参数
		$hiddenconfig = array(
		'cfg_df_style',
		'cfg_arc_autokeyword',
		'cfg_df_dutyadmin',
		'cfg_arc_dellink',
		'cfg_rm_remote',
		'cfg_arc_autopic',
		'cfg_arcautosp',
		'cfg_arcautosp_size',
		'cfg_mb_max',
		'cfg_mb_sendall',
		'cfg_smtp_port',
		'cfg_delete',
		'cfg_upload_switch',
		'cfg_cookie_encode',
		'cfg_html_editor',
		'cfg_adminemail',
		'cfg_wap_tpl_default',
		);
		$model = M('config');
		$data['groupid'] =$gid;
		$list = $model->where($data)->order('id asc')->select();
		if(!$list) return;
		foreach($list as $k=>$v)
		{
			if(in_array($v['varname'],$hiddenconfig))  unset($v,$list[$k]);
			if($v['type']=='select')
			{
				$arr = explode(",",$v['morevalue']);
				$list[$k]['select'] = '';
				for($i=0;$i<count($arr);$i++)
				{
					if($i==$v['value'])
					{
						$list[$k]['select'] .="<option value='".$i."' selected>".$arr[$i]."</option>"; 
					}
					else
					{
						$list[$k]['select'] .="<option value='".$i."'>".$arr[$i]."</option>"; 
					}
					
				}
			}
			elseif($v['type']=='checkbox')
			{
				$arr = explode(",",$v['morevalue']);
				if(empty($v['value'])) $_tmp1 = 1;
				$varr = explode(",",$v['value']);
				$list[$k]['select'] = '';
				for($i=0;$i<count($arr);$i++)
				{
					if(in_array($i,$varr) && $_tmp1<>1)
					{
						$list[$k]['select'] .= $arr[$i]."<input name='".$v['varname'].$i."' type='checkbox' class='noborder' checked/>";

					}
					else
					{
						$list[$k]['select'] .= $arr[$i]."<input name='".$v['varname'].$i."' type='checkbox' class='noborder'/>";
					}
					
				}
			}
			elseif($v['type']=='radio')
			{
				$arr = explode(",",$v['morevalue']);
				$list[$k]['select'] = '';
				for($i=0;$i<count($arr);$i++)
				{
					if($i==$v['value'])
					{
						$list[$k]['select'].="<input name='".$v['varname']."' type='radio' class='noborder' value='".$i."' checked='checked'/>".$arr[$i];
					}
					else
					{
						$list[$k]['select'].="<input name='".$v['varname']."' type='radio' class='noborder' value='".$i."'>".$arr[$i];
					}
					
				}
			}
			elseif($v['type']=='textarea')
			{
				$list[$k]['value'] = htmlspecialchars($list[$k]['value']);
			}
		}
		return $list;
	}
	public function update()
	{
		$model = M('config');
		$list = $model->select();
		foreach($list as $v)
		{
			$v['value'] = isset($_POST[$v['varname']]) ? $_POST[$v['varname']]:$v['value'];
			if($v['type']=='textarea')
			{
				//防止服务器开启反转义
				$v['value'] = stripslashes($v['value']);
			}
			elseif($v['type']=='checkbox')
			{
				$v['value'] = '';
				$arr = explode(",",$v['morevalue']);
				for($i=0;$i<count($arr);$i++)
				{
					if(isset($_POST[$v['varname'].$i]))
					{
						$v['value'] .= $i.','; 
					}
				}
				$v['value'] = rtrim($v['value'],',');
			}
			$model->save($v);
		}
		$this->success('操作成功!正在转向...',U('Config/index'));
	}
	
	public function add()
	{
		$this->display();
	}
	public function doadd()
	{
		$model = M('config');
		$model->create();
		if($model->add())
		{
			$this->success('操作成功!正在转向...',U('Config/index'));
		}
		else
		{
			$this->error('操作失败!');
		}

	}
}
?>