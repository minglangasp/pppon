/*******************************************************************************
* KindEditor - WYSIWYG HTML Editor for Internet
* Copyright (C) 2006-2011 kindsoft.net
*
* @author Roddy <luolonghao@gmail.com>
* @site http://www.kindsoft.net/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/

KindEditor.plugin('insertfile', function(K) {
	var self = this, name = 'insertfile',
		extpath = self.basePath + 'mini/',
		allowFileUpload = K.undef(self.allowFileUpload, true),
		allowFileManager = K.undef(self.allowFileManager, false),
		formatUploadUrl = K.undef(self.formatUploadUrl, true),
		keepOriginName = K.undef(self.keepOriginName, false),
		userAllowUpload = K.undef(self.userAllowUpload, false),
		removeTitle = K.undef(self.removeTitle, false),
		uploadJson = K.undef(self.uploadJson, self.basePath + 'php/upload_json.php'),
		extraParams = K.undef(self.extraFileUploadParams, {}),
		filePostName = K.undef(self.filePostName, 'imgFile'),
		file_rename = '',
		user_up = '',
		remove_title = '',
		lang = self.lang(name + '.');
		if(keepOriginName==true) file_rename = '&rename=1';
		if(userAllowUpload==true) user_up = '&userup=1';
		if(removeTitle==true) remove_title = 'style=\'display:none;\'';
	self.plugin.fileDialog = function(options) {
		var fileUrl = K.undef(options.fileUrl, 'http://'),
			fileTitle = K.undef(options.fileTitle, ''),
			clickFn = options.clickFn;
		var html = [
			'<div style="padding:20px;">',
			'<div class="ke-dialog-row">',
			'<label for="keUrl" style="width:60px;">' + lang.url + '</label>',
			'<input type="text" id="keUrl" name="url" class="ke-input-text" style="width:160px;" /> &nbsp;',
			'<input type="button" class="ke-upload-button" value="' + lang.upload + '" /> &nbsp;',
			'<span class="ke-button-common ke-button-outer">',
			'<input type="button" class="ke-button-common ke-button" name="viewServer" value="' + lang.viewServer + '" />',
			'</span>',
			'</div>',
			//title
			'<div class="ke-dialog-row" '+remove_title+'>',
			'<label for="keTitle" style="width:60px;">' + lang.title + '</label>',
			'<input type="text" id="keTitle" class="ke-input-text" name="title" value="" style="width:160px;" /></div>',
			'</div>',
			//form end
			'</form>',
			'</div>'
			].join('');
		var dialog = self.createDialog({
			name : name,
			width : 450,
			title : self.lang(name),
			body : html,
			yesBtn : {
				name : self.lang('yes'),
				click : function(e) {
					var url = K.trim(urlBox.val()),
						title = titleBox.val();
					if (url == 'http://' || K.invalidUrl(url)) {
						alert(self.lang('invalidUrl'));
						urlBox[0].focus();
						return;
					}
					if (K.trim(title) === '') {
						title = url;
					}
					clickFn.call(self, url, title);
				}
			}
		}),
		div = dialog.div;

		var urlBox = K('[name="url"]', div),
			viewServerBtn = K('[name="viewServer"]', div),
			titleBox = K('[name="title"]', div);

		if (allowFileUpload) {
			var uploadbutton = K.uploadbutton({
				button : K('.ke-upload-button', div)[0],
				fieldName : filePostName,
				url : K.addParam(uploadJson, 'dir=file'+file_rename+user_up),
				extraParams : extraParams,
				afterUpload : function(data) {
					dialog.hideLoading();
					if (data.error === 0) {
						var url = data.url;
						if (formatUploadUrl) {
							url = K.formatUrl(url, 'absolute');
						}
						urlBox.val(url);
						if (self.afterUpload) {
							self.afterUpload.call(self, url, data, name);
						}
						alert(self.lang('uploadSuccess'));
					} else {
						alert(data.message);
					}
				},
				afterError : function(html) {
					dialog.hideLoading();
					self.errorDialog(html);
				}
			});
			uploadbutton.fileBox.change(function(e) {
				dialog.showLoading(self.lang('uploadLoading'));
				uploadbutton.submit();
			});
		} else {
			K('.ke-upload-button', div).hide();
		}
		if (allowFileManager) {
			viewServerBtn.click(function(e) {
				self.loadPlugin('filemanager', function() {
					self.plugin.filemanagerDialog({
						viewType : 'LIST',
						dirName : 'file',
						clickFn : function(url, title) {
							if (self.dialogs.length > 1) {
								K('[name="url"]', div).val(url);
								if (self.afterSelectFile) {
									self.afterSelectFile.call(self, url);
								}
								self.hideDialog();
							}
						}
					});
				});
			});
		} else {
			viewServerBtn.hide();
		}
		urlBox.val(fileUrl);
		titleBox.val(fileTitle);
		urlBox[0].focus();
		urlBox[0].select();
	};
	self.clickToolbar(name, function() {
		self.plugin.fileDialog({
			clickFn : function(url, title) {
			  var filename = url.substr(url.lastIndexOf('/')+1,url.length - url.lastIndexOf('/'));
			  var ext = url.substr(url.length - 4,4),
			  ext2 = url.substr(url.length - 3,3),
			  ext3 = url.substr(url.length - 6,6),
			  ext4 = url.substr(url.length - 5,5),
			  extdata = '<br/>附件下载:';
			  
			   if(ext=='.zip' || ext2 =='.7z'  || ext2=='.gz' || ext =='.bz2')
			   {
					extdata = extdata + '<img src="'+extpath+'/zip.gif"/>';
			   }
			   if(ext=='.rar')
			   {
					extdata = extdata + '<img src="'+extpath+'/rar.gif"/>';
			   }
			   if(ext=='.doc' || ext4=='.docx')
			   {
					extdata = extdata + '<img src="'+extpath+'/doc.gif"/>';
			   }
			   if(ext=='.ppt' || ext4=='.pptx')
			   {
					extdata = extdata + '<img src="'+extpath+'/ppt.gif"/>';
			   }
			    if(ext=='.xls' || ext4=='.xlsx')
			   {
					extdata = extdata + '<img src="'+extpath+'/xls.gif"/>';
			   }
			   if(ext=='.txt')
			   {
					extdata = extdata + '<img src="'+extpath+'/txt.gif"/>';
			   }
			   if(ext=='.pdf')
			   {
					extdata = extdata + '<img src="'+extpath+'/pdf.gif"/>';
			   }
				var html = extdata + '<a href="' + url + '" data-ke-src="' + url + '" target="_blank">' + filename + '</a>';
				self.insertHtml(html).hideDialog().focus();
			}
		});
	});
});
