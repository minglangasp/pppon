<?php
/***********************************************************
    [WaiKuCms] (C)2011 - 2012 waikucms.com
    
	@function 编辑器js调用脚本

    @Filename editor.php $

    @Author pengyong $

    @Date 2012-12-17 15:17:32 $
*************************************************************/

//$theme = isset($_GET['theme']) ? $_GET['theme'] : 'simple';//风格
$theme = 'simple';
$id = isset($_GET['id']) ? $_GET['id'] : 'Content';//编辑器id
$fm = isset($_GET['fm']) ? $_GET['fm'] : 'true';//文件管理
$mode  = isset($_GET['mode']) ? $_GET['mode']: '';//插件模式
$type  = isset($_GET['type']) ? $_GET['type']: 'image';//默认为加载图片插件
$buttonid  = isset($_GET['buttonid']) ? $_GET['buttonid']: '';
//插件模式
if($mode=='plugin' && $type=='file')
{
	$themejs = <<<DATA
	KindEditor.ready(function(K) {
				var editor = K.editor({
					allowFileManager : {$fm}
				});
				K('#{$buttonid}').click(function() {
					editor.loadPlugin('insertfile', function() {
						editor.plugin.fileDialog({
							fileUrl : K("{$_GET['tag']}[name='{$_GET['name']}']").val(),
							clickFn : function(url, title) {
								K("{$_GET['tag']}[name='{$_GET['name']}']").val(url);
								editor.hideDialog();
							}
						});
					});
				});
			});
DATA;
die($themejs);
}
if($mode=='plugin' && $type=='image')
{
	$themejs = <<<DATA
	KindEditor.ready(function(K) {
				var editor = K.editor({
					allowFileManager : {$fm}
				});
				K('#{$buttonid}').click(function() {
					editor.loadPlugin('image', function() {
						editor.plugin.imageDialog({
							imageUrl : K("{$_GET['tag']}[name='{$_GET['name']}']").val(),
							clickFn : function(url, title) {
								K("{$_GET['tag']}[name='{$_GET['name']}']").val(url);
								editor.hideDialog();
							}
						});
					});
				});
			});
DATA;
die($themejs);
}
if($theme=='default')
{
	$themejs = <<<DATA
	items : [
		'source', '|', 'undo', 'redo', '|', 'print', 'template', 'code', 'cut', 'copy', 'paste',
		'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		'superscript', 'clearhtml', 'quickformat', 'selectall', 'preview','fullscreen','/',
		'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage',
		'flash', 'media', 'insertfile','multifile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
		'anchor', 'link', 'unlink','about'
	],
DATA;
}
elseif($theme=='simple')
{
	$themejs= <<<DATA
					resizeType : 1,
					allowPreviewEmoticons : false,
					allowImageUpload : true,
                    fillDescAfterUploadImage : true,
                    newlineTag : "br",
					userAllowUpload:true,
					items : [
						'source', '|','fontname', 'fontsize', '|', 'forecolor', 'bold', 'italic', 'underline',
						'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'hilitecolor', 'emoticons', 'image','insertfile','link','hr','baidumap','preview','fullscreen'],

DATA;
	$fm = "false";
}
elseif($theme=='qq')
{
	$js = <<<DATA
	KindEditor.ready(function(K) {
				K.each({ 
					'plug-align' : {
						name : '对齐方式',
						method : {
							'justifyleft' : '左对齐',
							'justifycenter' : '居中对齐',
							'justifyright' : '右对齐'
						}
					},
					'plug-order' : {
						name : '编号',
						method : {
							'insertorderedlist' : '数字编号',
							'insertunorderedlist' : '项目编号'
						}
					},
					'plug-indent' : {
						name : '缩进',
						method : {
							'indent' : '向右缩进',
							'outdent' : '向左缩进'
						}
					}
				},function( pluginName, pluginData ){
					var lang = {};
					lang[pluginName] = pluginData.name;
					KindEditor.lang( lang );
					KindEditor.plugin( pluginName, function(K) {
						var self = this;
						self.clickToolbar( pluginName, function() {
							var menu = self.createMenu({
									name : pluginName,
									width : pluginData.width || 100
								});
							K.each( pluginData.method, function( i, v ){
								menu.addItem({
									title : v,
									checked : false,
									iconClass : pluginName+'-'+i,
									click : function() {
										self.exec(i).hideMenu();
									}
								});
							})
						});
					});
				});
				K.create('#{$id}', {
					themeType : 'qq',
					items : [
						'bold','italic','underline','fontname','fontsize','forecolor','hilitecolor','plug-align','plug-order','plug-indent','link'
					]
				});
			});
DATA;
die($js);
}
$js =<<<DATA
var editor_{$id};
KindEditor.ready(function(K) {
                editor_{$id} = K.create('#{$id}',{
				{$themejs}
				allowFileManager : {$fm}
				});
        });
DATA;
die($js);
