/*******************************************************************************
* KindEditor - WYSIWYG HTML Editor for Internet
* Copyright (C) 2006-2011 kindsoft.net
*
* @author Roddy <luolonghao@gmail.com>
* @site http://www.kindsoft.net/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/

KindEditor.plugin('filemanager', function(K) {
	var self = this, name = 'filemanager',
		fileManagerJson = K.undef(self.fileManagerJson, self.basePath + 'php/file_manager_json.php'),
		ajaxphpurl = K.undef(self.fileManagerJson_delete, self.basePath + 'php/ajax_file_delete.php'),
		renameajaxphpurl = K.undef(self.fileManagerJson_rename, self.basePath + 'php/ajax_file_rename.php'),
		imgPath = self.pluginsPath + name + '/images/',
		lang = self.lang(name + '.');
	function makeFileTitle(filename, filesize, datetime) {
		return filename + ' (' + Math.ceil(filesize / 1024) + 'KB, ' + datetime + ')';
	}
	function bindTitle(el, data) {
		if (data.is_dir) {
			el.attr('title', data.filename);
		} else {
			el.attr('title', makeFileTitle(data.filename, data.filesize, data.datetime));
		}
	}
	self.plugin.filemanagerDialog = function(options) {
		var width = K.undef(options.width, 650),
			height = K.undef(options.height, 510),
			dirName = K.undef(options.dirName, ''),
			viewType = K.undef(options.viewType, 'VIEW').toUpperCase(), // "LIST" or "VIEW"
			clickFn = options.clickFn;
		var html = [
			'<div style="padding:10px 20px;">',
			// header start
			'<div class="ke-plugin-filemanager-header">',
			// left start
			'<div class="ke-left">',
			'<img class="ke-inline-block" name="moveupImg" src="' + imgPath + 'go-up.gif" width="16" height="16" border="0" alt="" /> ',
			'<a class="ke-inline-block" name="moveupLink" href="javascript:;">' + lang.moveup + '</a>',
			'</div>',
			// right start
			'<div class="ke-right">',
			lang.viewType + ' <select class="ke-inline-block" name="viewType">',
			'<option value="VIEW">' + lang.viewImage + '</option>',
			'<option value="LIST">' + lang.listImage + '</option>',
			'</select> ',
			lang.orderType + ' <select class="ke-inline-block" name="orderType">',
			'<option value="NAME">' + lang.fileName + '</option>',
			'<option value="SIZE">' + lang.fileSize + '</option>',
			'<option value="TYPE">' + lang.fileType + '</option>',
			'</select>',
			'</div>',
			'<div class="ke-clearfix"></div>',
			'</div>',
			// body start
			'<div class="ke-plugin-filemanager-body"></div>',
			'</div>'
		].join('');
		var dialog = self.createDialog({
			name : name,
			width : width,
			height : height,
			title : self.lang(name),
			body : html
		}),
		div = dialog.div,
		bodyDiv = K('.ke-plugin-filemanager-body', div),
		moveupImg = K('[name="moveupImg"]', div),
		moveupLink = K('[name="moveupLink"]', div),
		viewServerBtn = K('[name="viewServer"]', div),
		viewTypeBox = K('[name="viewType"]', div),
		orderTypeBox = K('[name="orderType"]', div);
		function reloadPage(path, order, func) {
			var param = 'path=' + path + '&order=' + order + '&dir=' + dirName;
			dialog.showLoading(self.lang('ajaxLoading'));
			K.ajax(K.addParam(fileManagerJson, param + '&' + new Date().getTime()), function(data) {
				dialog.hideLoading();
				func(data);
			});
		}
		var elList = [];
		function bindEvent(el, result, data, createFunc) {
			var fileUrl = K.formatUrl(result.current_url + data.filename, 'absolute'),
				dirPath = encodeURIComponent(result.current_dir_path + data.filename + '/');
			if (data.is_dir) {
				el.click(function(e) {
					reloadPage(dirPath, orderTypeBox.val(), createFunc);
				});
			} else if (data.is_photo) {
				el.click(function(e) {
					clickFn.call(this, fileUrl, data.filename);
				});	
			}else{
				el.click(function(e) {
					clickFn.call(this, fileUrl, data.filename);
				});
			}
			elList.push(el);
		}
		function createCommon(result, createFunc) {
			// remove events
			K.each(elList, function() {
				this.unbind();
			});
			moveupLink.unbind();
			viewTypeBox.unbind();
			orderTypeBox.unbind();
			// add events
			if (result.current_dir_path) {
				moveupLink.click(function(e) {
					reloadPage(result.moveup_dir_path, orderTypeBox.val(), createFunc);
				});
			}

			function changeFunc() {
				if (viewTypeBox.val() == 'VIEW') {
					reloadPage(result.current_dir_path, orderTypeBox.val(), createView);
				} else {
					reloadPage(result.current_dir_path, orderTypeBox.val(), createList);
				}
			}
			viewTypeBox.change(changeFunc);
			orderTypeBox.change(changeFunc);
			bodyDiv.html('');
		}
		function createList(result) {
			createCommon(result, createList);
			var table = document.createElement('table');
			table.className = 'ke-table';
			table.cellPadding = 0;
			table.cellSpacing = 0;
			table.border = 0;
			bodyDiv.append(table);
			var fileList = result.file_list;
			for (var i = 0, len = fileList.length; i < len; i++) {
				var data = fileList[i], row = K(table.insertRow(i));
				row.mouseover(function(e) {
					K(this).addClass('ke-on');
				})
				.mouseout(function(e) {
					K(this).removeClass('ke-on');
				});
				var iconUrl = imgPath + (data.is_dir ? 'folder-16.gif' : 'file-16.gif'),
					img = K('<img src="' + iconUrl + '" width="16" height="16" alt="' + data.filename + '" align="absmiddle" />'),
					cell0 = K(row[0].insertCell(0)).addClass('ke-cell ke-name').append(img).append('<span filename=\''+data.filename+'\' style=\'margin-left:4px;\'>'+data.filename+'</span>');
				if (!data.is_dir || data.has_file) {
					row.css('cursor', 'pointer');
					cell0.attr('title', data.filename);
					bindEvent(cell0, result, data, createList);
				} else {
					cell0.attr('title', lang.emptyFolder);
				}
				K(row[0].insertCell(1)).addClass('ke-cell ke-size').html(data.is_dir ? '-' : Math.ceil(data.filesize / 1024) + 'KB');
				K(row[0].insertCell(2)).addClass('ke-cell ke-datetime').html(data.datetime);
				row.attr('filename', data.filename);
				var fileUrl = result.current_url + data.filename;
				var truefileUrl = fileUrl.replace('Editor/kindeditor/php/../../../','');
				K(row[0].insertCell(3)).addClass('ke-cell ke-action').css('cursor', 'pointer').html('<a filename="rm_'+data.filename+'" href="javascript:;" onclick="ajaxrename'+"('"+ truefileUrl +"','"+ data.filename+"','"+renameajaxphpurl+ "')"+'">' + lang.renameActionName + '</a>&nbsp;&nbsp;<a href="javascript:;" filename="del_'+data.filename+'" onclick="ajaxdel'+"('"+ truefileUrl +"','"+ data.filename+"','"+ ajaxphpurl+ "')"+'">' + lang.delActionName + '</a>');
			}
		}
		function createView(result) {
			createCommon(result, createView);
			var fileList = result.file_list;
			for (var i = 0, len = fileList.length; i < len; i++) {
				var data = fileList[i],
					div = K('<div class="ke-inline-block ke-item"></div>');
				bodyDiv.append(div);
				var photoDiv = K('<div class="ke-inline-block ke-photo"></div>')
					.mouseover(function(e) {
						K(this).addClass('ke-on');
					})
					.mouseout(function(e) {
						K(this).removeClass('ke-on');
					});
				div.append(photoDiv);
				var fileUrl = result.current_url + data.filename,
					iconUrl = data.is_dir ? imgPath + 'folder-64.gif' : (data.is_photo ? fileUrl : imgPath + 'file-64.gif');
				var img = K('<img src="' + iconUrl + '" width="80" height="80" alt="' + data.filename + '" />');
				if (!data.is_dir || data.has_file) {
					photoDiv.css('cursor', 'pointer');
					bindTitle(photoDiv, data);
					bindEvent(photoDiv, result, data, createView);
				} else {
					photoDiv.attr('title', lang.emptyFolder);
				}
				photoDiv.append(img);
				div.append('<div class="ke-name" title="' + data.filename + '"><span filename=\''+data.filename+'\'>' + data.filename + '</span></div>');
				var truefileUrl = fileUrl.replace('Editor/kindeditor/php/../../../','');
				div.append('<div class="ke-action" title="' + data.filename + '" style="cursor: pointer; "><a filename="rm_'+data.filename+'" href="javascript:;" onclick="ajaxrename'+"('"+ truefileUrl +"','"+ data.filename+"','"+ renameajaxphpurl+ "')"+'">' + lang.renameActionName + '</a>&nbsp;&nbsp;<a filename="del_'+data.filename+'"  href="javascript:;" onclick="ajaxdel'+"('"+ truefileUrl +"','"+ data.filename+"','"+ ajaxphpurl+ "')"+'">' + lang.delActionName + '</a></div>');
				div.attr('filename',data.filename);
			}
		}
		viewTypeBox.val(viewType);
		reloadPage('', orderTypeBox.val(), viewType == 'VIEW' ? createView : createList);
		return dialog;
	}

});

function ajaxdel(path,filename,url)
{
	$.ajax({
			type: "POST",
			url: url,
			data: "path="+path,
			success: function(msg){
				if(msg==1)
				{
					$("div[filename='"+filename+"']").hide();
					$("tr[filename='"+filename+"']").hide();
				}
				else
				{
					$.dialog.alert(msg);
				}
			}
		});
}

function ajaxrename(path,filename,url)
{
	if(filename.indexOf('.') >0)
	{ 
		var filetype='文件';
		var ext = filename.substring(filename.lastIndexOf('.'),filename.length);
		var value = filename.substring(0,filename.length - ext.length);
	}
	else
	{
		var ext ='';
		var filetype='文件夹';
		var value =filename;
	}
	var content = '重命名'+filetype+':'+filename;
	$.dialog({
	id: 'Prompt',
    icon: 'question',
	lock: true,
    background: '#ccc', 
    opacity: 0.87,	
	title:'重命名操作',
	zIndex:'999999',
	content: [
            '<div style="margin-bottom:5px;font-size:12px">',
                content,
            '</div>',
			'<div style="margin-bottom:5px;font-size:12px;display:none;color:red;" id=\'renamecheck\'>',
                '请检查书写,不支持中文.?/等字符,不能为空',
            '</div>',
            '<div>',
                '<input value="',
                    value,
                '" style="width:18em;padding:6px 4px" />&nbsp;'+ext,
            '</div>'
            ].join(''),
    init: function () {
        input = this.DOM.content.find('input')[0];
        input.select();
        input.focus();
    },
    ok: function (here) {
		if(input.value.indexOf('.')>0 || input.value.indexOf('?')>0 ||  input.value.indexOf('/')>0 || input.value=='')
		{
			this.shake && this.shake();
			$('#renamecheck').show();
			return false;
		}
		   var renamepath = input.value+ext;
			$.ajax({
			type: "POST",
			url: url,
			data: "path="+path+'&rename='+renamepath,
			success: function(msg){
					if(msg=='1')
					{
						$.dialog.tips('操作成功,请手动切换浏览方式,刷新列表!');
						$("tr[filename='"+filename+"']").attr('filename',renamepath);
						$("div[title='"+filename+"']").attr('title',renamepath);
						$("span[filename='"+filename+"']").html(renamepath).attr('filename',renamepath);
						$("img[alt='"+filename+"']").attr('alt',renamepath);
						$("td[title='"+filename+"']").attr('title',renamepath);
						var onclick1 = $("a[filename='rm_"+filename+"']").attr('onclick');
						var reg = new RegExp(filename,"ig");  
						$("a[filename='rm_"+filename+"']").attr('onclick',onclick1.replace(reg,renamepath)).attr('filename',renamepath);
						var onclick2 = $("a[filename='del_"+filename+"']").attr('onclick');
						$("a[filename='del_"+filename+"']").attr('onclick',onclick2.replace(reg,renamepath)).attr('filename',renamepath);
					}
					else
					{
						$.dialog.alert(msg);
					}
				}
			});
    },
    cancel: true
	});
}
