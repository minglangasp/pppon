<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2013 waikucms.com
    
	@function 核心框架 标签解析

    @Filename TaglibWk.class.php $

    @Author pengyong $

    @Date 2012-12-21 12:10:11 $
*************************************************************/
defined('THINK_PATH') or exit();
class TagLibWk extends TagLib {
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		'arclist'	 =>	 array('attr'=>'diymodel,vo,fromvo,flag,noflag,field,typeid,modelid,row,titlelen,subday,orderby,limit,key,mod,aid,keyword,arcrank,noarcrank,getall,pagesize,paging,noglobal,likearticle','level'=>3),
		'global'	 =>  array('attr'=>'name,type,function','close'=>0),
		'tplvar'	 =>  array('attr'=>'name,function','close'=>0),
		'field'	 	 =>  array('attr'=>'name,function','close'=>0),
		'arctype'	 =>	 array('attr'=>'diymodel,vo,fromvo,typeid,row,limit,field,type,modeltype,modelid,key,mod','level'=>3),
		'page'       =>  array('attr'=>'linkpage,option,getvar','close'=>0),
		'myad'       =>  array('attr'=>'diymodel,id,gid,orderby,limit,row,key,mod','level'=>3),
		'include'    =>  array('attr'=>'filename','close'=>0),
		'plugin'    =>  array('attr'=>'name,method,parameter,function','close'=>0),
		'memberlist'    =>  array('attr'=>'diymodel,field,id,money,row,limit,rankid,orderby,status,key,mod','level'=>3),
		'flink'    =>  array('attr'=>'diymodel,id,gid,img,row,limit,orderby,key,mod','level'=>3),
		'vote'    => array('attr'=>'id','close'=>0),
		'votelist' => array('attr'=>'field,titlelen,id,orderby,row,limit,fromvo,vo,key,mod,type,option','level'=>3),
		'taglist' =>array('attr'=>'diymodel,field,titlelen,orderby,row,limit,fromvo,vo,key,mod,subday','level'=>3),
		'themelist'=>array('attr'=>'row,dirname','level'=>2),
		'recordlist'=>array('attr'=>'diymodel,row,limit,vo,fromvo,title,mod,key,orderby','level'=>2),
		);
		
	 /**
     * arclist标签解析  循环输出自由文章列表
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 
	 public function _arclist($attr,$content)
	 {
		static $_iterateParseCache = array();
		//全局变量
		global $_fields;
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_arclist'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
		$tag   =    $this->parseXmlAttr($attr,'arclist');
		//获取标签属性
		$flag         =    isset($tag['flag']) ? $tag['flag'] : '';
		$noflag       =    isset($tag['noflag']) ? $tag['noflag'] : '';
		$aid          =    isset($tag['aid']) ? $tag['aid'] : '';
		$typeid       =    isset($tag['typeid']) ? $tag['typeid'] : '';
		$modelid      =    isset($tag['modelid']) ? $tag['modelid'] : '';
		$field	  	  =    isset($tag['field'])  ?  $tag['field'] : '';
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$titlelen     =    isset($tag['titlelen']) ? (int)$tag['titlelen'] : '';
		$orderby	  =    isset($tag['orderby'])  ? $tag['orderby'] : 'pubdate desc';
		$limit	  	  =    isset($tag['limit'])  ?  $tag['limit'] : '';
		$paging	 	  =    isset($tag['paging'])  ? $tag['paging'] : '';//分页
		$pagesize	  =    isset($tag['pagesize'])  ? $tag['pagesize'] : 10;//分页数
		$keyword	  =    isset($tag['keyword'])  ?  $tag['keyword'] : '';
		$arcrank	  =    isset($tag['arcrank'])  ?  $tag['arcrank'] : '';
		$noarcrank	  =    isset($tag['noarcrank'])  ?  $tag['noarcrank'] : '';
		$likearticle  =    isset($tag['likearticle'])  ?  $tag['likearticle'] : 0;//likearticle==1 则获取相关文章
		$getall	  	  =    isset($tag['getall'])  ? $tag['getall'] : '';
		$key  		  =    !empty($tag['key']) ? $tag['key']:'i';
		$mod  		  =    isset($tag['mod']) ? $tag['mod']:'2';
		$vo			  =	   isset($tag['vo']) ? $tag['vo']:'';
		$fromvo		  =	   isset($tag['fromvo']) ? $tag['fromvo']:'';
		$subday		  =	   isset($tag['subday']) ? $tag['subday']:'';//定义多少天之内发布的文档
		$noglobal 	  =    $likearticle==1 ? 1:0;
		$noglobal	  =	   isset($tag['noglobal']) ? $tag['noglobal']:$noglobal;
		$_getglobal = 0;
		$debug		  =	   isset($tag['debug']) ? $tag['debug']:'';//调试模式
		$record		  =		isset($tag['record']) ? $tag['record'] :'';//全站日期归档
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		if($debug=='getTrace')
		{
			import('ORG.Debug');
			$debugnum='arclist_'.uniqid();
			Debug::mark($debugnum);
		}
		//查询数据库
		$model = empty($diymodel) ?  D('ArchiveView'): D($diymodel);
		$map['_string'] = '';
		$map['arcrank'] =  empty($arcrank) ? array('in','1,2,3'):array('in',$arcrank);
		if(!empty($noarcrank)) $map['arcrank'] = array('notin',$noarcrank);
		if(!empty($subday)) $map['pubdate'] = array('egt','__SUBDAY__');
		if(!empty($flag))
		{
			$flags = explode(',',$flag);
			for($i=0; isset($flags[$i]); $i++) $map['_string'] .= "AND FIND_IN_SET('{$flags[$i]}', flag)>0 ";
		}
		if(!empty($noflag))
        {
            if(strrpos($noflag,',')===false)
            {
                $map['_string'] .= "AND FIND_IN_SET('$noflag', flag)<1 ";
            }
            else
            {
                $noflags = explode(',', $noflag);
                foreach($noflags as $noflag) {
                    if(trim($noflag)=='') continue;
                    $map['_string'] .= "AND FIND_IN_SET('$noflag', flag)<1 ";
                }
            }
        }
		//尝试获取全局变量栏目id
		if(empty($modelid) && empty($typeid) && !empty($_fields['typeid']) && $noglobal<>1) {$typeid = $_fields['typeid']; $_getglobal = 1;}
		if(!empty($typeid)) $map['typeid'] =  $typeid=='~typeid~' ? array('gt',0):array('in',$typeid);
		//获取全局变量则注销 $map['typeid']
		if($_getglobal == 1){unset($map['typeid']);}
		//查询栏目下包含子栏目的文档
		if($getall==1 && !empty($typeid) && $typeid<>'~typeid~' && $_getglobal<>1)
		{
			$arctype = M('arctype');
			$bpath = $arctype->field("concat(path,'-',id) as bpath")->where(array('id'=>array('in',$typeid)))->select();
			foreach($bpath as $v)
			{
				$data['path'][] = array('like','%'.$v['bpath']);
			}
			$data['path'][] = 'or';
			$typeids = $arctype->field('id')->where($data)->select();
			if(!empty($typeids))
			{
				foreach($typeids as $v)
				{
					$tidarray[] = $v['id'];
				}
				$tidarray = array_merge(explode(',',$typeid),$tidarray);
				$map['typeid'] = array('in',$tidarray);
			}
		}
		if(!empty($modelid)) $map['modelid'] = array('in',$modelid);
		if(!empty($aid)) $map['id'] = $aid;
		if(empty($limit)) $limit = '0,'.$row;
		if(!empty($keyword) or $likearticle==1)
		{
			//尝试获取 $_GET['keyword'] 和 $_POST['keyword'];post优先
			if(substr($keyword,0,1) == '~' && substr($keyword,-1,1) == '~')
			{
				$keyword = trim($keyword,'~');
				$keyword = RemoveXSS($_REQUEST[$keyword]);
			}
			//从全局变量获取
			if($likearticle==1) 
			{
				if(empty($keyword)) $keyword = strtr($_fields['keywords'],',',' ');
				if(empty($keyword)) return;
				//剔除本身,即当前文章将不会显示在文章列表
				if(!empty($_fields['id'])) $map['id'] = array('neq',$_fields['id']);
			}
			$keywords = explode(' ',$keyword);
			//tag关键词获取判断,则 匹配 keywords字段
			if($_REQUEST['tag']==1 or $likearticle==1)
			{
				foreach($keywords as $v)
				{
					$map['keywords'][] = array('like',"%".$v."%");
				}
				$map['keywords'][] = 'or';
			}
			else
			{
				foreach($keywords as $v)
				{
					$map['title'][] = array('like',"%".$v."%");
				}
				$map['title'][] = 'or';
			}
		}
		if(!empty($field)) $field = rtrim($field,',').',';
		$root  = $GLOBALS['cfg_rewrite']==0 ? __ROOT__ :__ROOT__.'/index.php';
		$suffix = '.'.C('URL_HTML_SUFFIX');
		$field .= "concat('{$root}/view-', archive.id, '{$suffix}') as arcurl,";
		$field .= "concat('<a href=\\\"{$root}/view-', archive.id, '{$suffix}\\\">',title,'</a>') as arclink,";
		$field .= "concat('{$root}/list-', archive.typeid, '{$suffix}') as typeurl,";
		$field .= "concat(archive.typeid) as typesid,";
		$field .= "concat('<a href=\\\"{$root}/list-', archive.typeid, '{$suffix}\\\">',typename,'</a>') as typelink,";
		$field .= 'id,pubdate,litpic,typename,title as fulltitle,';
		empty($titlelen) ?  $field.='title' : $field.= "left(archive.title,{$titlelen}) as title";
		if(empty($map['_string'])){ unset($map['_string']);}else{ $map['_string'] = trim($map['_string'],'AND');}
		//record判断
		if($record =='~pubdate~' && !empty($_fields['pubdate']) && empty($subday)) $map['pubdate'] = array('egt',$_fields['pubdate']);
		$sql = $model->field($field)->where($map)->order($orderby)->limit($limit)->select(false);
		//循环标识判断
		$fromvotag = empty($fromvo) ? '$_field' : '$vo_'.$fromvo;
		if($typeid=='~typeid~' && $getall <> 1) $sql = str_replace(') ORDER BY','AND archive.typeid=\'".'.$fromvotag.'[\'typeid\']."\') ORDER BY',$sql);
		if($typeid=='~typeid~' && $getall == 1) $sql = str_replace(') ORDER BY','AND archive.typeid IN ("._arclist_getall_sonid('.$fromvotag.'[\'typeid\']).") ) ORDER BY',$sql);
		$parseStr   =  '<?php ';
	    //分页支持
		if($paging==1)
		{	
			//获取全局变量时动态统计sql获取记录总数
			$sql2 = $model->field("count(*) as wk_num")->where($map)->order('pubdate desc')->select(false);
			if($_getglobal==1 && $getall <> 1) 
			{
				$sql2 = str_replace(') ORDER BY','AND archive.typeid=\'".$GLOBALS[\'_fields\'][\'typeid\']."\') ORDER BY',$sql2);
				$parseStr  .='$totalnum = query("'.$sql2.'");';
				$parseStr  .='$totalnum = $totalnum[0][\'wk_num\'];';
			}
			elseif($_getglobal==1 && $getall == 1)
			{
				$sql2 = str_replace(') ORDER BY','AND archive.typeid IN ("._arclist_getall_sonid($GLOBALS[\'_fields\'][\'typeid\']).") ) ORDER BY',$sql2);
				$parseStr  .='$totalnum = query("'.$sql2.'");';
				$parseStr  .='$totalnum = $totalnum[0][\'wk_num\'];';
			}
			else
			{
				$totalnum = $model->where($map)->count();
				$parseStr  .='$totalnum = "'.$totalnum.'";';
			}
			$sql = $model->field($field)->where($map)->order($orderby)->select(false);
			$GLOBALS['_fields']['page']['pagesize'] = $pagesize;
			$parseStr  .= '$_limita = _page_limit_a($totalnum,'.$pagesize.');';
			$parseStr  .= '$_limitb = \''.$pagesize.'\';';
			$sql.= ' Limit ".$_limita.",".$_limitb."';
		}
		//获取全局变量时动态生成sql语句
		if($_getglobal==1 && $getall <> 1) {$sql = str_replace(') ORDER BY','AND archive.typeid=\'".$GLOBALS[\'_fields\'][\'typeid\']."\') ORDER BY',$sql);}
		if($_getglobal==1 && $getall == 1) {$sql = str_replace(') ORDER BY','AND archive.typeid IN ("._arclist_getall_sonid($GLOBALS[\'_fields\'][\'typeid\']).") ) ORDER BY',$sql);}
		//自定义天数则 动态设置sql
		if(!empty($subday)) 
		{
			$parseStr.= '$__SUBDAY__ = time() - '.$subday.'*24*3600;';
			$sql = str_replace('__SUBDAY__','".$__SUBDAY__."',$sql);
		}
		//动态设置 orderby
		if($orderby =='~orderby~')
		{
			$parseStr.= '$__ORDERBY__ = empty($_REQUEST[\'orderby\']) ? \'pubdate desc\':$_REQUEST[\'orderby\'];';
			$sql = str_replace('~orderby~ ASC','".$__ORDERBY__."',$sql);
		}
		if($debug=='getSql') return htmlspecialchars($sql);
		$parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
		//循环标识
		if(!empty($vo))  $parseStr .= '<?php $vo_'.$vo.' = $_field;?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		//性能调试
		if($debug=='getTrace') 
		{
			Debug::mark($debugnum.'_end');
			return 'Traceid:'.$debugnum.';useTime:'.Debug::useTime($debugnum,$debugnum.'_end').'s;useMemory:'.Debug::useMemory($debugnum,$debugnum.'_end').'kb';
		}
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	 }
	 
	 /**
     * 分页标签解析  输出分页链接
     * @access public
     * @param string $attr 标签属性
     * @return string|void
     */
	 
	 public function _page($attr)
	 {
		global $_fields;
		if(!isset($_fields['page'])) return ;
        $tag     		=    $this->parseXmlAttr($attr,'page');
		$linknum	    =    empty($tag['linkpage'])? 5 : (int)$tag['linkpage'];
		$option	    	=    !empty($tag['option']) ? $tag['option'] : 'indexpage,prepage,linkpage,nextpage,endpage,select,pageinfo';
		$function 		= 	 empty($tag['function'])? '@me' : rtrim($tag['function'],';');
		$getvar 		= 	 !empty($tag['getvar'])? $tag['getvar'] : 'p';
		$parseStr 		= ''; 
		$parseStr  	   .= '<?php $__PAGE__ = _page_output($totalnum,\''.$_fields['page']['pagesize'].'\',\''.$linknum.'\',\''.$option.'\',\''.$getvar.'\');';
		$function 	    = strtr($function,array('@me'=>'$__PAGE__'));
		$parseStr 	   .= 'echo '.$function.';?>';
        return $parseStr;
	}
	 
	 
	 
	 /**
     * global标签解析  输出全局变量
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 
	 public function _global($attr)
	 {
        $tag   =    $this->parseXmlAttr($attr,'global');
		$name	  = isset($tag['name']) ? $tag['name']:'';
		$type	  = isset($tag['type']) ? $tag['type']:'';
		//变量前缀
		$var_prefix = empty($type) ?  '$GLOBALS':'$_'.strtoupper($type);
		if($type=='field') $var_prefix = '$GLOBALS'."['_fields']";
		if($type=='tplvar') $var_prefix = '$GLOBALS'."['_tplvars']";
		$function = empty($tag['function'])? '@me' : rtrim($tag['function'],';');
		$function = strtr($function,array('@me'=>$var_prefix.'[\''.$name.'\']'));
		$parseStr   = '<?php echo '.$function.';?>';
        return $parseStr;
	 }
	 
	 /**
     * field标签解析  输出当前模板变量
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 
	 public function _field($attr)
	 {
        $tag   	  =    $this->parseXmlAttr($attr,'field');
		$name	  = isset($tag['name']) ? $tag['name'] :'';
		if(empty($name)) return;
		//变量前缀
		$var_prefix = '$GLOBALS'."['_fields']";
		$function = empty($tag['function'])? '@me' : rtrim($tag['function'],';');
		$function = strtr($function,array('@me'=>$var_prefix.'[\''.$name.'\']'));
		$parseStr   = '<?php echo '.$function.';?>';
        return $parseStr;
	 }
	 
	  /**
     * include标签解析  载入模板
     * @access public
     * @param string $attr 标签属性
     * @return string|void
     */
	 
	 public function _include($attr)
	 {
		global $cfg_df_style,$cfg_wap_tpl_default;
        $tag   =    $this->parseXmlAttr($attr,'include');
		$mode  =  isset($_GET['mode']) ? $_GET['mode']:'';
		$dir = $mode=='wap' ? 'Wap' : 'Tpl';
		$style = $mode=='wap' ? $cfg_wap_tpl_default : $cfg_df_style;
		//跨目录支持
		if(substr($tag['filename'],0,3)=='../')
		{
			$filename = str_replace('../','./Public/'.$dir.'/',$tag['filename']);
		}
		elseif(substr($tag['filename'],0,3)<>'../' &&  substr($tag['filename'],0,2)=='./')
		{
			$filename = $tag['filename'];
		}
		else
		{
			$filename	  = './Public/'.$dir.'/'.$style.'/'.$tag['filename'];
		}
		if(!file_exists($filename)) return '模板文件:'.$filename.'不存在!';
		$parseStr  = $this->tpl->parse(File::read_file($filename));
        return $parseStr;
	 }
	 
	 /**
     * 主题扩展标签解析  输出主题扩展变量
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	
	 public function _tplvar($attr)
	 {
        $tag   =    $this->parseXmlAttr($attr,'tplvar');
		$name	  = $tag['name'];
		//变量前缀
		$var_prefix = '$GLOBALS'."['_tplvars']";
		$function = empty($tag['function'])? '@me' : rtrim($tag['function'],';');
		$function = strtr($function,array('@me'=>$var_prefix.'[\''.$name.'\']'));
		$parseStr   = '<?php echo '.$function.';?>';
        return $parseStr;
	 }
	 
	 /**
     * 广告标签解析  输出广告内容
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	
	 public function _myad($attr,$content)
	 {
		static $_iterateParseCache = array();
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_myad'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag  = $this->parseXmlAttr($attr,'myad');
		$id	  		  = !empty($tag['id']) ? $tag['id'] : '';
		$gid		  = !empty($tag['gid']) ? $tag['gid'] : '';
		$orderby	  =  isset($tag['orderby'])  ? $tag['orderby'] : 'pubdate desc';
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$limit	  	  =    isset($tag['limit'])  ?  $tag['limit'] : '';
		$key  		  =    !empty($tag['key'])?$tag['key']:'i';
		$mod  		  =    isset($tag['mod'])?$tag['mod']:'2';
		$debug		  =	   isset($tag['debug']) ? $tag['debug']:'';//调试模式
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		if($debug=='getTrace')
		{
			import('ORG.Debug');
			$debugnum='myad_'.uniqid();
			Debug::mark($debugnum);
		}
		if(!empty($gid)) $map['gid'] = array('in',$gid); 
		if(!empty($id)) $map['id'] = array('in',$id);
		if(empty($limit)) $limit = '0,'.$row;	
		$map['status'] = 1;
		$model = empty($diymodel) ? M('ad'):D($diymodel);
		$sql = $model->field('content')->where($map)->order($orderby)->limit($limit)->select(false);
		if($debug=='getSql') return htmlspecialchars($sql);;
		$parseStr   =  '<?php ';
		$parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		//性能调试
		if($debug=='getTrace') 
		{
			Debug::mark($debugnum.'_end');
			return 'Traceid:'.$debugnum.';useTime:'.Debug::useTime($debugnum,$debugnum.'_end').'s;useMemory:'.Debug::useMemory($debugnum,$debugnum.'_end').'kb';
		}
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	 }
	 /**
     * 栏目列表解析  输出栏目列表
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	
	 public function _arctype($attr,$content)
	 {
        static $_iterateParseCache = array();
		//全局变量
		global $_fields;
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_arctype'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
		$tag   =    $this->parseXmlAttr($attr,'arctype');
		//获取标签属性
		$typeid        	 =    isset($tag['typeid']) ? $tag['typeid'] : '';
		$modelid         =    isset($tag['modelid']) ? $tag['modelid'] : '';
		$field        	 =    isset($tag['field']) ? $tag['field'] : '';
		$type        	 =    isset($tag['type']) ? $tag['type'] : '';// top 顶级栏目, self 同级栏目, son 子级栏目,parent 父级栏目
		$row        	 =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$limit        	 =    isset($tag['limit']) ? $tag['limit'] : '';
		$modeltype    	 =    isset($tag['modeltype']) ? $tag['modeltype'] : '';
		$display    	 =    isset($tag['display']) ? $tag['display'] : '';
		$key  		 	 =    !empty($tag['key'])?$tag['key']:'i';
		$mod  		 	 =    isset($tag['mod'])?$tag['mod']:'2';
		$vo			  	 =	  isset($tag['vo']) ? $tag['vo']:'';
		$fromvo		     =	  isset($tag['fromvo']) ? $tag['fromvo']:'';
		$noglobal	  	 =	  isset($tag['noglobal']) ? $tag['noglobal']:0;
		$getall	  	  =    isset($tag['getall'])  ? $tag['getall'] : 0;
		$debug		  =	   isset($tag['debug']) ? $tag['debug']:'';//调试模式
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		if($debug=='getTrace')
		{
			import('ORG.Debug');
			$debugnum='arctype_'.uniqid();
			Debug::mark($debugnum);
		}
		$_getglobal = 0;
		//查询数据库
		$model = empty($diymodel) ? M('arctype'):D($diymodel);
		if(!empty($modelid)) $map['modelid'] = array('in',$modelid);
		//获取全局变量typeid
		if(empty($typeid) && !empty($_fields['typeid']) && $noglobal<>1) {$typeid = $_fields['typeid']; $_getglobal = 1;}
		if(!empty($type) && $typeid<>'~typeid~')
		{
			if($type=='top')
			{
				$map['fid'] = 0;
				if($getall==1 or $_getglobal == 1) $typeid='';
			}
			elseif($type=='self'  && !empty($typeid))
			{
				if($_getglobal <> 1){ $path = $model->field('fid')->where(array('id'=>$typeid))->find(); $map['fid'] = $path['fid'];}
				$typeid='';
				$map['id'] =array('gt',0);
			}
			elseif($type=='son'  && !empty($typeid))
			{
				if($_getglobal <> 1) $map['fid'] = $typeid;
				$typeid='';
				$map['id'] =array('gt',0);
			}
			elseif($type=='parent'  && !empty($typeid))
			{
				if($_getglobal <> 1)
				{
					$path = $model->field('fid')->where(array('id'=>$typeid))->find();
					if($path['fid']<>0)
					{
						$path = $model->field('fid')->where(array('id'=>$path['fid']))->find();
						$map['fid'] = $path['fid'];
					}
					else
					{
						$map['fid'] = 0;
					}
				}
				$typeid='';
				$map['id'] =array('gt',0);
			}
		}
		if(!empty($typeid)){$map['id'] =  $typeid=='~typeid~' ? array('gt',0):array('in',$typeid);}
		//栏目属性modeltype 
		if(!empty($modeltype)) $map['modeltype'] = array('in',$modeltype);
        if(!empty($display)) $map['display'] =  array('in',$display);
		$root  = $GLOBALS['cfg_rewrite']==0 ? __ROOT__ :__ROOT__.'/index.php';
		$suffix = '.'.C('URL_HTML_SUFFIX');
		if(!empty($field)) $field = rtrim($field,',').',';
		$field .= "concat('{$root}/list-', id, '{$suffix}') as typeurl,";
		$field .= "concat(id) as typesid,";
		$field .= "concat('<a href=\\\"{$root}/list-', id, '{$suffix}\\\">',typename,'</a>') as typelink,";
		$field .= 'id as typeid,typename';
		if(empty($limit)) $limit = '0,'.$row;
		$sql = $model->field($field)->where($map)->order('sortrank asc')->limit($limit)->select(false);
		//循环标识判断
		$fromvotag = empty($fromvo) ? '$_field' : '$vo_'.$fromvo;
		if($typeid=='~typeid~') $sql = str_replace(') ORDER BY','AND `fid`=\'".'.$fromvotag.'[\'typeid\']."\') ORDER BY',$sql);
		//全局变量获取则替换全局变量sql
		if($_getglobal==1 && $type=='son') $sql = str_replace(') ORDER BY','AND `fid`=\'".$GLOBALS[\'_fields\'][\'typeid\']."\') ORDER BY',$sql);
		if($_getglobal==1 && $type=='self') $sql = str_replace(') ORDER BY','AND `fid`=\'"._arctype_self_getfid($GLOBALS[\'_fields\'][\'typeid\'])."\') ORDER BY',$sql);
		if($_getglobal==1 && $type=='parent') $sql = str_replace(') ORDER BY','AND `fid`=\'"._arctype_parent_getfid($GLOBALS[\'_fields\'][\'typeid\'])."\') ORDER BY',$sql);
        if($debug=='getSql') return htmlspecialchars($sql);
		$parseStr   =  '<?php ';
        $parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
		//循环标识
		if(!empty($vo))  $parseStr .= '<?php $vo_'.$vo.' = $_field;?>';
		$parseStr .=  $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		//性能调试
		if($debug=='getTrace') 
		{
			Debug::mark($debugnum.'_end');
			return 'Traceid:'.$debugnum.';useTime:'.Debug::useTime($debugnum,$debugnum.'_end').'s;useMemory:'.Debug::useMemory($debugnum,$debugnum.'_end').'kb';
		}
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	 }
	 
	 /**
     * 插件挂载  输出插件相应方法执行结果
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	
	 public function _plugin($attr)
	 {
        $tag   =    $this->parseXmlAttr($attr,'plugin');
		$name	  		= isset($tag['name']) ? $tag['name'] :'';
		$method	  		= isset($tag['method']) ? $tag['method'] :'index';
		$parameter	  	= isset($tag['parameter']) ? $tag['parameter'] :'';
		if(empty($name)||empty($method)) return;
		$parseStrson = 'plugin(\''.$name.'\',\''.$method.'\',\''.$parameter.'\')';
		$function = empty($tag['function'])? '@me' : rtrim($tag['function'],';');
		$function = strtr($function,array('@me'=>$parseStrson));
		$parseStr   = '<?php echo '.$function.';?>';
        return $parseStr;
	 }
	 
	  /**
     * 会员列表 输出会员列表
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	
	 public function _memberlist($attr,$content)
	 {
		static $_iterateParseCache = array();
		//全局变量
		global $_fields;
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_memberlist'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
		$tag   =    $this->parseXmlAttr($attr,'memberlist');
		$field	  	  =    isset($tag['field'])  ?  $tag['field'] : '';
		$id	  	      =    isset($tag['id'])  ?  $tag['id'] : '';
		$status	  	  =    isset($tag['status'])  ?  $tag['status'] : '';
		$money	  	  =    isset($tag['money'])  ?  $tag['money'] : '';
		$rankid	  	  =    isset($tag['rankid']) ?  $tag['rankid'] : '';
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$orderby	  =    isset($tag['orderby'])  ? $tag['orderby'] : 'regtime desc';
		$limit	  	  =    isset($tag['limit'])  ?  $tag['limit'] : '';
		$key  		  =    !empty($tag['key']) ? $tag['key']:'i';
		$mod  		  =    isset($tag['mod']) ? $tag['mod']:'2';
		$debug		  =	   isset($tag['debug']) ? $tag['debug']:'';//调试模式
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		if($debug=='getTrace')
		{
			import('ORG.Debug');
			$debugnum='memberlist_'.uniqid();
			Debug::mark($debugnum);
		}
		if(!empty($field)) $field = rtrim($field,',').',';
		if(!empty($status)) $map['status'] = $status;
		if(!empty($money)) $map['money'] = array('lgt',$money);
		if(!empty($rankid)) $map['rankid'] = $rankid;
		if(empty($limit)) $limit = '0,'.$row;
		if(!empty($id)) $map['id'] = array('in',$id);
		$field.='money,username,id,';
		$model = empty($diymodel) ? D('MemberView'):D($diymodel);
		$sql = $model->field($field)->where($map)->order($orderby)->limit($limit)->select(false);
		if($debug=='getSql') return htmlspecialchars($sql);
		$parseStr   =  '<?php ';
        $parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
		$parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		//性能调试
		if($debug=='getTrace') 
		{
			Debug::mark($debugnum.'_end');
			return 'Traceid:'.$debugnum.';useTime:'.Debug::useTime($debugnum,$debugnum.'_end').'s;useMemory:'.Debug::useMemory($debugnum,$debugnum.'_end').'kb';
		}
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	 }
	 
	 /**
     * 友情链接 输出友情链接
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 
	 public function _flink($attr,$content)
	 {
		static $_iterateParseCache = array();
		//全局变量
		global $_fields;
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_flink'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
		$tag   =    $this->parseXmlAttr($attr,'flink');
		$field		  =    isset($tag['field']) ? $tag['field'] :'';
		$id	  	      =    isset($tag['id'])  ?  $tag['id'] : '';
		$gid	  	  =    isset($tag['gid'])  ?  $tag['gid'] : '';
		$img          =    isset($tag['img'])  ?  $tag['img'] : '';
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$orderby	  =    isset($tag['orderby'])  ? $tag['orderby'] : 'pubdate desc';
		$limit	  	  =    isset($tag['limit'])  ?  $tag['limit'] : '';
		$key  		  =    !empty($tag['key']) ? $tag['key']:'i';
		$mod  		  =    isset($tag['mod']) ? $tag['mod']:'2';
		$debug		  =	   isset($tag['debug']) ? $tag['debug']:'';//调试模式
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		if($debug=='getTrace')
		{
			import('ORG.Debug');
			$debugnum='flink_'.uniqid();
			Debug::mark($debugnum);
		}
		if(!empty($id)) $map['id'] = array('in',$id);
		if(!empty($gid)) $map['gid'] = array('in',$gid);
		if(empty($limit)) $limit = '0,'.$row;
		if($img=='1') $map['img'] = array('neq','');
		$map['status'] = 1;
		$model = empty($diymodel) ? M('friendlink') :D($diymodel);
		$field .= 'title,content as friendurl,img as friendimg,';
		$field .="concat('<a href=\\\"', content, '\\\">',title,'</a>') as friendlink";
		$sql = $model->field($field)->where($map)->order($orderby)->limit($limit)->select(false);
		if($debug=='getSql') return htmlspecialchars($sql);
		$parseStr   =  '<?php ';
        $parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
		$parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		//性能调试
		if($debug=='getTrace') 
		{
			Debug::mark($debugnum.'_end');
			return 'Traceid:'.$debugnum.';useTime:'.Debug::useTime($debugnum,$debugnum.'_end').'s;useMemory:'.Debug::useMemory($debugnum,$debugnum.'_end').'kb';
		}
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	 }
	 
	 /**
     * 投票标签 输出投票内容
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 public function _vote($attr)
	{
		$tag   =    $this->parseXmlAttr($attr,'vote');
		$id    =	isset($tag['id']) ? (int)$tag['id']:'';
		$show  =	isset($tag['show']) ? (int)$tag['show']:'';
		$barid  =	isset($tag['barid']) ? $tag['barid']:'show';
		$notitle  =	isset($tag['notitle']) ? $tag['notitle']:'';
		$showtotal  =	isset($tag['showtotal']) ? $tag['showtotal']:'';
		return '<?php echo _vote_show(\''.$id.'\',\''.$show.'\',\''.$barid.'\',\''.$notitle.'\',\''.$showtotal.'\');?>';
	}
	
	 /**
     * 投票列表 输出投票列表
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 public function _votelist($attr,$content)
	{
		static $_iterateParseCache = array();
		//全局变量
		global $_fields;
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_votelist'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
		$tag   =    $this->parseXmlAttr($attr,'votelist');
		$field	  	  =    isset($tag['field'])  ?  $tag['field'] : '';
		$id	  	  	  =    isset($tag['id'])  ?  $tag['id'] : '';
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$titlelen     =    isset($tag['titlelen']) ? (int)$tag['titlelen'] : '';
		$orderby	  =    isset($tag['orderby'])  ? $tag['orderby'] : 'rank asc';
		$limit	  	  =    isset($tag['limit'])  ?  $tag['limit'] : '';
		$type	  	  =    isset($tag['type'])  ?  $tag['type'] : '';
		$option	  	  =    isset($tag['option'])  ?  $tag['option'] : '';
		$key  		  =    !empty($tag['key']) ? $tag['key']:'i';
		$mod  		  =    isset($tag['mod']) ? $tag['mod']:'2';
		$vo			  =	   isset($tag['vo']) ? $tag['vo']:'';
		$fromvo		  =	   isset($tag['fromvo']) ? $tag['fromvo']:'';
		$debug		  =	   isset($tag['debug']) ? $tag['debug']:'';//调试模式
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		if($debug=='getTrace')
		{
			import('ORG.Debug');
			$debugnum='votelist_'.uniqid();
			Debug::mark($debugnum);
		}
		//查询数据库
		$model = empty($diymodel) ? M('vote') :D($diymodel);
		$map['status'] = 1;
		//id直接查询支持
		if(!empty($id)) $map['id'] = array('in',$id);
		if($option=='radio') $map['type'] = 0;//单选
		if($option=='checkbox') $map['type'] = 1;//多选
		if($type=='starting') $map['overtime'] = array('gt',time());//正在进行的投票
		if($type=='end') $map['overtime'] = array('elt',time());//已结束的投票
		if(empty($limit)) $limit = '0,'.$row;
		if(!empty($field)) $field = rtrim($field,',').',';
		$root  = $GLOBALS['cfg_rewrite']==0 ? __ROOT__ :__ROOT__.'/index.php';
		$suffix = '.'.C('URL_HTML_SUFFIX');
		$field .= "concat('{$root}/vote-', id, '{$suffix}') as voteurl,";
		$field .= "concat('<a href=\\\"{$root}/vote-', id, '{$suffix}\\\">',title,'</a>') as votelink,";
		$field .= 'id,starttime,overtime,title as fulltitle,';
		empty($titlelen) ?  $field.='title' : $field.= "left(title,{$titlelen}) as title";
		$sql = $model->field($field)->where($map)->order($orderby)->limit($limit)->select(false);
		if($debug=='getSql') return htmlspecialchars($sql);
		$parseStr	='<?php ';
		$parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
		//循环标识
		if(!empty($vo))  $parseStr .= '<?php $vo_'.$vo.' = $_field;?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		//性能调试
		if($debug=='getTrace') 
		{
			Debug::mark($debugnum.'_end');
			return 'Traceid:'.$debugnum.';useTime:'.Debug::useTime($debugnum,$debugnum.'_end').'s;useMemory:'.Debug::useMemory($debugnum,$debugnum.'_end').'kb';
		}
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	}
	
	
	 /**
     * 主题列表 
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 public function _themelist($attr,$content)
	 {
		$tag   =    $this->parseXmlAttr($attr,'themelist');
		$name	  	  =    isset($tag['dirname'])  ?  $tag['dirname'] : '';
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$key  		  =    !empty($tag['key']) ? $tag['key']:'i';
		$mod  		  =    isset($tag['mod']) ? $tag['mod']:'2';
		$parseStr	= '<?php ';
		$parseStr  .=  '$__LIST__ = _theme_list(\''.$name.'\','.$row.');';
		$parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
		return $parseStr;
	 }
	 
	 
	/**
     * TAG关键字列表 
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	 public function _taglist($attr,$content)
	{
		static $_iterateParseCache = array();
		//全局变量
		global $_fields;
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_taglist'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
		$tag   =    $this->parseXmlAttr($attr,'taglist');
		$field	  	  =    isset($tag['field'])  ?  $tag['field'] : '';
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$titlelen     =    isset($tag['titlelen']) ? (int)$tag['titlelen'] : '';
		$orderby	  =    isset($tag['orderby'])  ? $tag['orderby'] : 'searchnum desc';
		$limit	  	  =    isset($tag['limit'])  ?  $tag['limit'] : '';
		$subday	  	  =    isset($tag['subday'])  ?  $tag['subday'] : '';//多少天以内
		$key  		  =    !empty($tag['key']) ? $tag['key']:'i';
		$mod  		  =    isset($tag['mod']) ? $tag['mod']:'2';
		$vo			  =	   isset($tag['vo']) ? $tag['vo']:'';
		$fromvo		  =	   isset($tag['fromvo']) ? $tag['fromvo']:'';
		$debug		  =	   isset($tag['debug']) ? $tag['debug']:'';//调试模式
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		if($debug=='getTrace')
		{
			import('ORG.Debug');
			$debugnum='taglist_'.uniqid();
			Debug::mark($debugnum);
		}
		//查询数据库
		$model = M('tags');
		$model = empty($diymodel) ? M('tags'):D($diymodel);
		if(!empty($subday)) $map['pubdate'] = array('egt','__SUBDAY__');
		if(empty($limit)) $limit = '0,'.$row;
		if(!empty($field)) $field = rtrim($field,',').',';
		$root  = $GLOBALS['cfg_rewrite']==0 ? __ROOT__ :__ROOT__.'/index.php';
		$suffix = '.'.C('URL_HTML_SUFFIX');
		$field .= "concat('{$root}/search', '{$suffix}?tag=1&keyword=',keyword) as tagurl,";
		$field .= "concat('<a href=\\\"{$root}/search', '{$suffix}?tag=1&keyword=',keyword,'\\\">',keyword,'</a>') as taglink,";
		$field .= 'id,searchnum,keyword as fulltitle,';
		empty($titlelen) ?  $field.='keyword as title' : $field.= "left(keyword,{$titlelen}) as title";
		$sql = $model->field($field)->where($map)->order($orderby)->limit($limit)->select(false);
		$parseStr	='<?php ';
		//自定义天数则 动态设置sql
		if(!empty($subday)) 
		{
			$parseStr.= '$__SUBDAY__ = time() - '.$subday.'*24*3600;';
			$sql = str_replace('__SUBDAY__','".$__SUBDAY__."',$sql);
		}
		if($debug=='getSql') return htmlspecialchars($sql);
		$parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
		//循环标识
		if(!empty($vo))  $parseStr .= '<?php $vo_'.$vo.' = $_field;?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		//性能调试
		if($debug=='getTrace') 
		{
			Debug::mark($debugnum.'_end');
			return 'Traceid:'.$debugnum.';useTime:'.Debug::useTime($debugnum,$debugnum.'_end').'s;useMemory:'.Debug::useMemory($debugnum,$debugnum.'_end').'kb';
		}
        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	}
	
	/**
     * 整站文章归档列表 
     * @access public
     * @param string $attr 标签属性
     * @param string $content  标签内容
     * @return string|void
     */
	
	public function _recordlist($attr,$content)
	{
		static $_iterateParseCache = array();
		//全局变量
		global $_fields;
        //如果已经解析过，则直接返回变量值
        $cacheIterateId = md5('_recordlist'.$attr.$content);
        if(isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
		$tag   =    $this->parseXmlAttr($attr,'recordlist');
		$type	=	 isset($tag['title']) ?  $tag['title']: '%Y-%m';// record title样式 %Y年, %m月
		$row          =    isset($tag['row']) ? (int)$tag['row'] : 10;
		$limit	  	  =    isset($tag['limit'])  ?  $tag['limit'] : '';
		$key  		  =    !empty($tag['key']) ? $tag['key']:'i';
		$mod  		  =    isset($tag['mod']) ? $tag['mod']:'2';
		$vo			  =	   isset($tag['vo']) ? $tag['vo']:'';
		$fromvo		  =	   isset($tag['fromvo']) ? $tag['fromvo']:'';
		$orderby	  =  isset($tag['orderby'])  ? $tag['orderby'] : 'pubdate desc';
		$diymodel	  =    isset($tag['diymodel'])  ?  $tag['diymodel'] : '';// 自定义模型
		$root  = $GLOBALS['cfg_rewrite']==0 ? __ROOT__ :__ROOT__.'/index.php';
		$suffix = '.'.C('URL_HTML_SUFFIX');
		if(empty($limit)) $limit = '0,'.$row;
		$field = "FROM_UNIXTIME(pubdate,'{$type}') as title,concat('{$root}/record-',FROM_UNIXTIME(pubdate, '%Y%m'),'{$suffix}') as recordurl,
		concat('<a href=\\\"{$root}/record-', FROM_UNIXTIME(pubdate, '%Y%m'),'{$suffix}\\\">',FROM_UNIXTIME(pubdate, '{$type}'),'</a>') as recordlink,count(*) as recordnum";
		$model = empty($diymodel) ? M('archive'):D($diymodel);
		$map['arcrank'] = array('elt',3);
		$sql = $model->field($field)->group("FROM_UNIXTIME(pubdate, '{$type}')")->where($map)->limit($limit)->order($orderby)->select(false);
		$parseStr	='<?php ';
		$parseStr  .=  '$__LIST__ = query("'.$sql.'");';
        $parseStr  .=  'if(is_array($__LIST__)): $'.$key.' = 0;';
        $parseStr .= 'foreach($__LIST__ as $key=>$_field): ';
        $parseStr .= '$mod = ($'.$key.' % '.$mod.' );';
        $parseStr .= '++$'.$key.';?>';
		//循环标识
		if(!empty($vo))  $parseStr .= '<?php $vo_'.$vo.' = $_field;?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif;?>';
        $_iterateParseCache[$cacheIterateId] = $parseStr;
		 if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
	}
	}
	
	