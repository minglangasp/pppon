<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function User组 文档管理

    @Filename ArchiveAction.class.php $

    @Author pengyong $

    @Date 2013-01-14 14:33:08 $
*************************************************************/
class ArchiveAction extends CommonAction
{
	public function index()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		global $member;
		import('@.ORG.Page');
		$status = $this->_get('status',false);
		$status = empty($status) ? 0 : $status;
		if($status==0)
		{
			$map['arcrank'] = array('in','1,2,3');
		}
		elseif($status==1)
		{
			$map['arcrank'] = array('in','4');
		}
		$map['mid'] = $member['id'];
		$model = D('ArchiveView');
		$count = $model->where($map)->count();
		$fenye = 20;
		$p = new Page($count,$fenye); 
		$list = $model->field('litpic,id,typeid,modelid,arcrank,title,flag,color,click,pubdate,mid,username,description')->where($map)->limit($p->firstRow.','.$p->listRows)->order('pubdate desc')->select();
		$p->setConfig('prev','上一页');
		$p->setConfig('header','条记录');
		$p->setConfig('first','首 页');
		$p->setConfig('last','末 页');
		$p->setConfig('next','下一页');
		$p->setConfig('theme',"%first%%upPage%%linkPage%%downPage%%end%<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 ".$fenye."条/每页</span></li>\n");
		$this->assign('page',$p->show());
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		$modelid = (int) $_GET['modelid'];
		//模型下文章发布
		$map['id'] = $modelid;
		$model = M('arcmodel');
		$modellist = $model->where($map)->find();
		if(!$modellist) $this->error('栏目模型不存在!');
		//解析模型自定义字段
		$taglist = $this->parsefieldset($modellist['fieldset']);
		//标记字段
		$this->assign('taglist',$taglist);
		//模型字段
		$this->assign('modellist',$modellist);
		//自定义属性
		$this->assign('flagtreelist',$this->flagtree('add'));
		//栏目树
		$typelist['modelid'] = $modelid;
		$this->assign('typelist',$typelist);
		$this->assign('selecttreelist',$this->selecttree($typelist));
		$this->display('add');
	}
	
	public function doadd()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		//基本字段
		$postvar = 'title,mid,color,typeid,litpic,shorttitle,keywords,writer,source,description,modelid,';
		//扩展字段
		$extrapost = $this->parseextendfield();
		$extrapostvar = $extrapost['extravar'];
		$data = $this->getdata($postvar.$extrapostvar);
		if(!empty($extrapost['data'])) $data = array_merge($data,$extrapost['data']);
		//默认字段处理
		$data['description'] = stripslashes(strip_tags($data['description']));
		$data['pubdate'] = time();
		$data['senddate'] = time();
		$data['click'] = 0;
		$data['arcrank'] = 4;
		$data['money'] = 0;
		global $cfg_auot_description,$cfg_arc_dellink;
		//自动摘要处理
		if($cfg_auot_description>0 && $cfg_auot_description<=250 && empty($data['description']))
		{
			$data['description']  = cn_substr(strip_tags($data['body']),0,$cfg_auot_description);
		}
		//0.基本验证
		if(empty($data['title'])) $this->error('标题不能为空!');
		if($data['typeid']=='0') $this->error('请选择文章所属栏目!');
		//关键词入库
		$this->inserttag(trim($data['keywords']));
		//1.微型表 更新
		$arctiny = M('arctiny');
		$arctinyvar = 'typeid,modelid,senddate,mid,';
		$arctinydata = $this->parsearray($data,$arctinyvar,'get');
		$data['id'] = $arctiny->add($arctinydata);
		//2.文档主表 更新
		$archive = M('archive');
		$archivedata = $this->parsearray($data,$extrapostvar,'del');
		$archive->add($archivedata);
		
		//3.获取附加表名
		$arcmodel = M('arcmodel');
		$arcmodellist = $arcmodel->field('addtable')->where("id='".$arctinydata['modelid']."'")->find();
		//4. 附加表 更新
		$addon= M($arcmodellist['addtable']);
		$addonvar = $extrapostvar.'id,typeid,';
		$addondata = $this->parsearray($data,$addonvar,'get');
		$addon->add($addondata);
		//4.1 积分变动
		global  $cfg_money_articleadd,$member;
		$membermodel = M('member');
		$membermodel->where(array('id'=>$member['id']))->setInc('money',$cfg_money_articleadd);
		//5.成功跳转
		$this->success('操作成功!',U('Archive/index?status=0'));
	}
	
	public function type()
	{
		$model = M('arcmodel');
		$list = $model->field('id,typename')->select();
		$this->assign('arcmodellist',$list);
		$this->display();
	}
	
	public function edit()
	{	
		if(!USER_LOGINED) jump(U('Public/login'));
		global $member;
		$map['id'] = $this->_get('id',false);
		$map['arcrank'] =array('neq',8);
		$model = M('archive');
		$list = $model->where($map)->find();
		if(!$list) $this->error('文档不存在!');
		if($list['mid'] <> $member['id']) $this->error('无权操作当前文档!');
		unset($model,$list,$map);
		//原始代码
		$map['id'] = $this->_get('id',false);
		if($map['id']==0) $this->error('参数不正确!');
		$arctinymodel = M('arctiny');
		$tinylist = $arctinymodel->where($map)->find();
		if(!$tinylist) $this->error('数据不存在!');
		$arcmodel = M('arcmodel');
		$map['id'] = $tinylist['modelid'];
		$modellist = $arcmodel->where($map)->find();
		$archivemodel = M('archive');
		$map['id'] = $tinylist['id'];
		$archivelist =  $archivemodel->where($map)->find();
		if(!$archivelist) $this->error('archive主表数据不存在!');
		$this->assign('archivelist',$archivelist);
		$arctypemodel = M('arctype');
		$map['id'] = $tinylist['typeid'];
		$typelist = $arctypemodel->where($map)->find();
		if(!$typelist) $this->error('arctype表数据不存在!');
		$addonmodel = M($modellist['addtable']);
		$map['id'] = $tinylist['id'];
		$addonlist = $addonmodel->where($map)->find();
		if(!$addonlist) $this->error('附加表数据不存在!');
		//投稿人显示
		$member = M('Member');
		$memberlist = $member->field('username,id')->where(array('id'=>$archivelist['mid']))->find();
		$this->assign('memberlist',$memberlist);
		//附加表字段
		$this->assign('addonlist',$addonlist);
		//解析模型自定义字段
		$taglist = $this->parsefieldset($modellist['fieldset'],3);
		//加空格防止fetch解析默认模板
		$taglist['basic'] = $this->fetch('',$taglist['basic'].' ');
		$taglist['advance'] = $this->fetch('',$taglist['advance'].' ');
		$taglist['extend'] = $this->fetch('',$taglist['extend'].' ');
		//标记字段
		$this->assign('taglist',$taglist);
		//模型字段
		$this->assign('modellist',$modellist);
		//自定义属性
		$this->assign('flagtreelist',$this->flagtree('edit',$archivelist['flag']));
		//栏目树
		$this->assign('typelist',$typelist);
		$this->assign('selecttreelist',$this->selecttree($typelist));
		$this->display();
	}
	
	public function doedit()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		//基本字段
		$postvar = 'id,title,mid,color,typeid,litpic,shorttitle,keywords,writer,source,description,modelid,';
		//扩展字段
		$extrapost = $this->parseextendfield();
		$extrapostvar = $extrapost['extravar'];
		$data = $this->getdata($postvar.$extrapostvar);
		if(!empty($extrapost['data'])) $data = array_merge($data,$extrapost['data']);
		$data['description'] = stripslashes(strip_tags($data['description']));
		//重新打回未审核状态
		$data['arcrank'] = 4;
		//0.基本验证
		if(empty($data['title'])) $this->error('标题不能为空!');
		if($data['typeid']=='0') $this->error('请选择文章所属栏目!');
		//关键词入库
		$this->inserttag(trim($data['keywords']));
		//1.微型表 更新
		$arctiny = M('arctiny');
		$arctinyvar = 'typeid,modelid,mid,id';
		$arctinydata = $this->parsearray($data,$arctinyvar,'get');
		$arctiny->save($arctinydata);
		//2.文档主表 更新
		$archive = M('archive');
		$archivedata = $this->parsearray($data,$extrapostvar,'del');
		//dump($archivedata);exit;
		$archive->save($archivedata);
		//3.获取附加表名
		$arcmodel = M('arcmodel');
		$arcmodellist = $arcmodel->field('addtable')->where("id='".$arctinydata['modelid']."'")->find();
		//4. 附加表 更新
		$addon = M($arcmodellist['addtable']);
		$addonvar = $extrapostvar.'id,typeid,';
		$addondata = $this->parsearray($data,$addonvar,'get');
		//dump($addondata);exit;
		$addon->save($addondata);
		//5.成功跳转
		$this->success('操作成功!',U('Archive/index?status=0'));
	}
	
	public function del()
	{
		if(!USER_LOGINED) jump(U('Public/login'));
		global  $cfg_money_articledel,$member;
		$map['id'] = $this->_get('id',false);
		$model = M('archive');
		$list = $model->where($map)->find();
		if(!$list) $this->error('文档不存在!');
		if($list['arcrank']<>4) $this->error('文档已经被审核通过了!');
		if($list['mid'] <> $member['id']) $this->error('无权操作!');
		$map['arcrank'] = 8;
		$model->save($map);
		//积分变动
		$membermodel = M('member');
		$membermodel->where(array('id'=>$member['id']))->setDec('money',$cfg_money_articledel);
		$from = empty($_SERVER['HTTP_REFERER']) ? U('Archive/index?status=0'):$_SERVER['HTTP_REFERER'];
		$this->success('操作成功!',$from);
	}
	//原始类:
	
	 //处理解析扩展字段
   private function parseextendfield()
   {
		//扩展字段
		$extrapostvar ='';
		$map['id'] = $this->_post('modelid');
		$arcmodel = M('arcmodel');
		$modellist = $arcmodel->field('fieldset')->where($map)->find();
		$taglist =$this->parsefieldset($modellist['fieldset'],2);
		foreach($taglist as $k=>$tag)
		{	
			$extrapostvar .=$tag['name'].',';
			if($tag['tag']=='checkbox')
			{
				$_temp_vars='';
				$values = explode(",",$tag['value']);
				foreach($values as $k=>$v)
				{
					if(isset($_POST[$tag['name'].'-'.$k]))  $_temp_vars.= $_POST[$tag['name'].'-'.$k].',';
				}
					$extra_data[$tag['name']] = rtrim($_temp_vars,',');
			}
			//防止服务器开启反转义
			elseif($tag['tag']=='editor' || $tag['tag']=='textarea')
			{
				$extra_data[$tag['name']] = stripslashes($_POST[$tag['name']]);
			}
		}
		$returndata['extravar'] = $extrapostvar;
		$returndata['data'] = $extra_data;
		return $returndata;
   }
   
   //获取 提交数据
   protected function getdata($post)
   {
		$data = $this->parsepost($post);
		$data['flag'] = $this->parseflag();
		return  $data;
   }
   //解析post数据
   private function parsepost($postvar)
   {
		$data = array();
		$post = explode(",",trim($postvar,','));
		foreach($post as $k=>$v)
		{
			$v = trim($v);
			$data[$v] = trim($this->_post($v));
		}
		return $data;
   }
   //解析flag标记
   private function parseflag($flagvar="c,h,p,f,s,j,a,b")
   {
		$flagdata = '';
		$flag = explode(",",trim($flagvar,','));
		foreach($flag as $v)
		{
			$v = trim($v);
			if(isset($_POST['flag-'.$v]))  $flagdata .=','.$v;
		}
		return trim($flagdata,',');
   }
   //自定义解析数组
   private function parsearray($array,$str,$method='get')
   {
		$returnarray = array();
		$strarray =  explode(',',trim($str,','));
		//从数组里拿取
		if($method=='get')
		{
			foreach($strarray as $v)
			{
				$returnarray[$v] = $array[$v];
			}
			return $returnarray;
		}
		//从数组里剔除
		elseif($method=='del')
		{
			foreach($strarray as $v)
			{
				unset($array[$v]);
			}
			return $array;
		}
	
   }
   //时间解析处理,返回时间戳
   private function parsetime($str)
   {
		$str =  explode(" ",$str);
		$ymd = explode("-",$str[0]);
		$his = explode(":",$str[1]);
		return mktime($his[0], $his[1], $his[2], $ymd[1], $ymd[2], $ymd[0]);
   }
   
   protected function parsefieldset($fieldset,$mode='1')
   {
		//缓存机制
		$fieldsetcacheid = 'fieldset_'.md5($fieldset);
		$fieldsettagcacheid = 'fieldsettag_'.md5($fieldset);
		$fieldsetdisplaycacheid = 'fieldsetdisplay_'.md5($fieldset);
		$fieldsetcachedata  = F($fieldsetcacheid);
		$fieldsettagcachedata  = F($fieldsettagcacheid);
		$fieldsetdisplaycachedata  = F($fieldsetdisplaycacheid);
		if(!empty($fieldsetcachedata) && $mode==1) return  $fieldsetcachedata;
		if(!empty($fieldsettagcachedata) && $mode==2) return  $fieldsettagcachedata;
		if(!empty($fieldsetdisplaycachedata) && $mode==3) return  $fieldsetdisplaycachedata;
		$taglist = array();
		$fieldset = simplexml_load_string($fieldset);
		foreach($fieldset->field as $field)
		{
			$tag = array();
			//标签 name值
			$tag['name'] = (string)$field->attributes()->name;
			//标签类型 string,textarea,editor,checkbox,radio,select
			$tag['tag'] = (string)$field->attributes()->tag;
			//标签id
			$tag['id'] = (string)$field->attributes()->id;
			//提示说明内容
			$tag['alt'] = (string)$field->attributes()->alt;
			//默认值
			$tag['value'] = (string)$field->attributes()->value;
			//字段长度
			$tag['size'] = (string)$field->attributes()->size;
			//所在组: basic advance extend
			$tag['group'] = (string)$field->attributes()->group;
			//字符类型, text/hidden/password
			$tag['type'] = (string)$field->attributes()->type;
			//其他扩展属性
			$tag['extend'] = (string)$field->attributes()->extend;
			// 编辑器 主题
			$tag['theme'] = (string)$field->attributes()->theme;
			//编辑器 文件管理
			$tag['fm'] = 'false';
			//应用样式
			$tag['style'] = (string)$field->attributes()->style;
			//addsrc/editsrc 添加状态时内容路径/编辑状态时内容路径 
			$tag['addsrc'] = (string)$field->attributes()->addsrc;
			$tag['editsrc'] = (string)$field->attributes()->editsrc;
			$tag['src'] = (string)$field->attributes()->src;
			if(!empty($tag['src']))
			{
				$tag['addsrc'] = $tag['src'];
				$tag['editsrc'] = $tag['src'];
			} 
			//是否解析附加代码片段 0=不解析  1=解析
			$tag['srcdisplayed'] = (string)$field->attributes()->srcdisplayed;
			$tag['srcdisplayed'] = empty($tag['srcdisplayed']) ? 0 : 1;
			if(empty($tag['type'])) $tag['type'] = 'text';
			if(empty($tag['size'])) $tag['size'] = '50';
			if(empty($tag['theme'])) $tag['theme'] = 'default';
			if(empty($tag['id'])) $tag['id'] = $tag['name'];
			if(empty($tag['fm'])) $tag['fm'] = 'true';
			if(empty($tag['group'])) $tag['group'] = 'extend';
			if(empty($tag['tag'])) $tag['tag'] = 'input';
			//添加方法 返回html结构
			if($mode==1)
			{
				if($tag['group']=='basic') $taglist['basic'] .= "<div class='control-group'><label class='control-label'>{$tag['alt']}</label><div class='controls'>".$this->parsefieldsetmode($tag,'basic')."</div></div>";
				if($tag['group']=='advance') $taglist['advance'] .= "<div class='control-group'><label class='control-label'>{$tag['alt']}</label><div class='controls'>".$this->parsefieldsetmode($tag,'advance')."</div></div>";
				if($tag['group']=='extend') $taglist['extend'] .= "<div class='control-group'><label class='control-label'>{$tag['alt']}</label><div class='controls'>".$this->parsefieldsetmode($tag,'extend')."</div></div>";
				F($fieldsetcacheid,$taglist);
			}
			//执行添加 返回tag数组
			elseif($mode==2)
			{
				$taglist[] = $tag;
				F($fieldsettagcacheid,$taglist);
			}
			//修改方法下 返回html结构
			elseif($mode==3)
			{
				if($tag['group']=='basic') $taglist['basic'] .= "<div class='control-group'><label class='control-label'>{$tag['alt']}</label><div class='controls'>".$this->parsefieldsetmode($tag,'basic',true)."</div></div>";
				if($tag['group']=='advance') $taglist['advance'] .= "<div class='control-group'><label class='control-label'>{$tag['alt']}</label><div class='controls'>".$this->parsefieldsetmode($tag,'advance',true)."</div></div>";
				if($tag['group']=='extend') $taglist['extend'] .= "<div class='control-group'><label class='control-label'>{$tag['alt']}</label><div class='controls'>".$this->parsefieldsetmode($tag,'extend',true)."</div></div>";
				F($fieldsetdisplaycacheid,$taglist);
			}
		}
		return $taglist;
   }
	private function parsefieldsetmode($tag,$mode,$display=false)
	{
		$tagvar = '';
		if($tag['group'] == $mode)
		{
			if($tag['tag']=='input')
			{
				$tag['value'] = $display==true ? "<field name='{$tag['name']}' prefix='addonlist'/>" :$tag['value'];
				$tagvar = "<input type='{$tag['type']}' group='{$mode}' name='{$tag['name']}' id='{$tag['id']}' size='{$tag['size']}'  style='{$tag['style']}' value='{$tag['value']}' {$tag['extend']}/>";
			}
			elseif($tag['tag']=='inserturl')
			{
				$tag['value'] = $display==true ? "<field name='{$tag['name']}' prefix='addonlist'/>" :$tag['value'];
				$tagvar = "<input type='{$tag['type']}' group='{$mode}' name='{$tag['name']}' id='{$tag['id']}' size='{$tag['size']}'  style='{$tag['style']}' value='{$tag['value']}' {$tag['extend']}/>";
				$tagvar.=<<<DATA
				<script>
				KindEditor.ready(function(K){
				K('#{$tag['id']}').click(function() {
				if(K('#{$tag['id']}').val() ==''){url ='http://';}else{url=K('#{$tag['id']}').val();} 
					$.dialog.prompt('请输入网址', function (val) {
			if(val =='' || val=='http://'){K('#{$tag['id']}').val('http://');}else{K('#{$tag['id']}').val(val);}
}, url);});
				});
				</script>
DATA;
			}
			elseif($tag['tag']=='insertfile')
			{
				$tag['value'] = $display==true ? "<field name='{$tag['name']}' prefix='addonlist'/>" :$tag['value'];
				$tagvar = "<input type='{$tag['type']}' group='{$mode}' name='{$tag['name']}' id='{$tag['id']}' size='{$tag['size']}'  style='{$tag['style']}' value='{$tag['value']}' {$tag['extend']}/>";
				$tagvar.=<<<DATA
				<script>
				KindEditor.ready(function(K) {
				K('#{$tag['id']}').click(function() {
					var editor = K.editor({
					allowFileManager : false,
					keepOriginName : false,
					userAllowUpload:true,
					removeTitle:true
				});
					editor.loadPlugin('insertfile', function() {
						editor.plugin.fileDialog({
							fileUrl : K('#{$tag['id']}').val(),
							clickFn : function(url, title) {
								K('#{$tag['id']}').val(url);
								editor.hideDialog();
							}
						});
					});
				});
			});
				</script>
DATA;
			}
			elseif($tag['tag']=='insertpic')
			{
				$tag['value'] = $display==true ? "<field name='{$tag['name']}' prefix='addonlist'/>" :'';
				$tagvar = "<input type='{$tag['type']}' group='{$mode}' name='{$tag['name']}' id='{$tag['id']}' size='{$tag['size']}'  style='{$tag['style']}' value='{$tag['value']}' {$tag['extend']}/>";
				$tagvar.=<<<DATA
				<script>
				KindEditor.ready(function(K) {
				K('#{$tag['id']}').click(function() {
					var editor = K.editor({
					allowFileManager : false,
					allowImageUpload : true,
					userAllowUpload : true,
					removeSize:true,
					removeAlign:true,
					resizeWidth:true,
					resizeHeight:true,
					removeTitle:true
				});
					editor.loadPlugin('image', function() {
						editor.plugin.imageDialog({
							imageUrl : K('#{$tag['id']}').val(),
							clickFn : function(url, title, width, height, border, align) {
									K('#{$tag['id']}').val(url);
									editor.hideDialog();
							}
							});
						
					});
				});
			});
				</script>
DATA;
			}
			elseif($tag['tag']=='checkbox')
			{
				$values = explode(",",$tag['value']);
				foreach($values as $k=>$v)
				{
					$value_set = $display==true ? "<field name='{$tag['name']}' prefix='addonlist' function=\"in_array('{$k}',explode(',',@me))?'checked':''\"/>":'';
					$tagvar.=$v."<input name='{$tag['name']}-{$k}' group='{$mode}' type='checkbox' class='noborder' id='{$tag['name']}-{$k}' value='{$k}' style='{$tag['style']}' {$tag['extend']} {$value_set}/>";
				}
			}
			elseif($tag['tag']=='textarea')
			{
				$tag['value'] = $display==true ? "<field name='{$tag['name']}' prefix='addonlist'/>" :$tag['value'];
				$tagvar = "<textarea name='{$tag['name']}' group='{$mode}'  id='{$tag['id']}' cols='60' rows='60' style='{$tag['style']}' {$tag['extend']} >{$tag['value']}</textarea>";
			}
			elseif($tag['tag']=='editor')
			{
				$tag['value'] = $display==true ? "<field name='{$tag['name']}' prefix='addonlist'/>" :$tag['value'];
				$tagvar .= "<script charset='utf-8' src='".__ROOT__."/Public/Editor/kindeditor/editor.php?theme={$tag['theme']}&fm={$tag['fm']}&id={$tag['id']}'></script>";
				$tagvar .="<textarea name='{$tag['name']}' id='{$tag['id']}' group='{$mode}' cols='60' rows='60' style='{$tag['style']}' {$tag['extend']}>{$tag['value']}</textarea>";
			}
			elseif($tag['tag']=='radio')
			{
				$values = explode(",",$tag['value']);
				foreach($values as $k=>$v)
				{
					$value_set = $display==true ? "<field name='{$tag['name']}' prefix='addonlist' function=\"@me=='{$k}'?'checked':''\"/>":'';
					if(!$display && $k==0) $value_set ='checked';
					$tagvar.=$v."<input name='{$tag['name']}' group='{$mode}' type='radio' class='noborder' id='{$tag['name']}-{$k}' value='{$k}' style='{$tag['style']}' {$tag['extend']} {$value_set}/>";
				}
			}
			elseif($tag['tag']=='select')
			{
				$tagvar .= "<select name='{$tag['name']}' group='{$mode}' style='{$tag['style']}' {$tag['extend']}>";
				$values = explode(",",$tag['value']);
				foreach($values as $k=>$v)
				{
					$value_set = $display==true ? "<field name='{$tag['name']}' prefix='addonlist' function=\"@me=='{$k}'?'selected=selected':''\"/>":'';
					$tagvar.="<option value='{$k}'{$value_set}>{$v}</option>";
				}
				$tagvar .= "</select>";
			}
			if($display && !empty($tag['editsrc']))
			{
					$editcontent = substr($tag['editsrc'],0,1)=='.' ? File::read_file($tag['editsrc']):fopen_url($tag['editsrc']);
					if($tag['srcdisplayed']==1) $editcontent = $this->fetch('',$editcontent.' ');
					$tagvar .=$editcontent;
			}
			if(!$display && !empty($tag['addsrc']))
			{
				$content = substr($tag['addsrc'],0,1)=='.' ? File::read_file($tag['addsrc']):fopen_url($tag['addsrc']);
				if($tag['srcdisplayed']==1) $content = $this->fetch('',$content.' ');
				$tagvar .=$content;
			}
			return $tagvar;
		}

	
	}
   
   /*
      //检测栏目是否为封面
   */
   
   protected function typecheck($typeid)
   {
	  $data['id'] = $typeid;
	  $model = M('arctype');
	  $list =  $model->where($data)->find();
	  if(!$list) return false;
	  if($list['modeltype']<>0) return false;
	  return $list;
   }
   
   /*
		//栏目二叉树结构
  */
	protected function selecttree($_arr=array())
	{
		$model = M('arctype');
		$list = $model->field("*,concat(path,'-',id) as bpath")->order('bpath')->select();
		foreach($list as $key=>$value)
		{
			$list[$key]['count'] = count(explode('-',$value['bpath']));
			if($_arr['id']==$list[$key]['id']) 
			{
				$list[$key]['selected'] = " selected='selected'";
			}
			//不显示非当前模型的栏目
			if($_arr['modelid'] <> $list[$key]['modelid'])
			{
				unset($list[$key]);
			} 
			//屏蔽所有封面类型栏目,跳转类型栏目,并标注封面
			if($list[$key]['modeltype']==1) 
			{
				$list[$key]['typename'] .='[封面]'; 
				$list[$key]['selected'] = " disabled='disabled'";
			} 
			if($list[$key]['modeltype']==2) 
			{
				$list[$key]['typename'] .='[跳转]';  
				$list[$key]['selected'] = " disabled='disabled'";
			}
		}
		return $list;
	}
	/*
		//自定义属性
	*/
	protected function flagtree($method='add',$_arr)
	{
		$flag = array();
		$keys = array('h','c','f','a','s','b','p','j');
		$values  = array('头条','推荐','幻灯','特荐','滚动','加粗','图片','跳转'); 
		if($method=='add')
		{
			foreach($keys as $k=>$v)
			{
				$flag[$k]['key'] = $keys[$k];
				$flag[$k]['value']  = $values[$k];
			}
		}
		elseif($method=='edit')
		{
			$flags = explode(',',$_arr);
			foreach($keys as $k=>$v)
			{
				$flag[$k]['key'] = $keys[$k];
				$flag[$k]['value']  = $values[$k];
				if(in_array($keys[$k],$flags))  $flag[$k]['checked'] = 'checked';
			}
		}
		return $flag;
	}
	
	/*
		//栏目位置导航
   */
   private function parselocation($typeid=0,$path='')
   {
		$data['id']    = $typeid;
		$arctypemodel  = M('arctype');
		if(empty($path))
		{
			$arcmodel      = M('arcmodel');
			$arctypelist   = $arctypemodel->field("modelid,concat(path,'-',id) as bpath")->where($data)->find();
			$path = $arctypelist['bpath'];
		}
		$map['id']     = array('in',strtr($path,array('0-'=>'','-'=>',')));
		return $arctypemodel->where($map)->field("typename,id,concat(path,'-',id) as bpath")->order('bpath')->select();
   }
   
   /*
		关键字tag入库
   */
   private function inserttag($keyword)
   {
		if(empty($keyword)) return;
		$keyword = explode(',',$keyword);
		$model = M('tags');
		foreach($keyword as $v)
		{
			$data['keyword'] = $v;
			$data['pubdate'] = time();
			$data['searchnum'] = 0;
			$model->add($data);
		}
   }
   
}