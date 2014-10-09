CREATE TABLE IF NOT EXISTS `#@__addondownload` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `body` mediumtext,
  `soft_author` char(50) NOT NULL,
  `soft_nature` char(50) NOT NULL,
  `soft_version` char(50) NOT NULL,
  `soft_lang` char(50) NOT NULL,
  `soft_platform` char(50) NOT NULL,
  `soft_size` char(50) NOT NULL,
  `soft_contact` char(50) NOT NULL,
  `soft_downloadnum` char(50) NOT NULL,
  `redirecturl` varchar(255) NOT NULL,
  `downloadurl` mediumtext,
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
###
INSERT INTO `#@__arcmodel` (`id`, `nid`, `typename`, `titlename`, `fieldset`, `addtable`, `issystem`, `status`) VALUES
(__LINE__, 'download', '下载', '下载标题', '<fieldset>\r\n<field name="redirecturl" tag="input" type="text"  id="redirecturl" size="50" group="basic" alt="跳转地址"/>\r\n<field name="soft_author" tag="input" type="text"  id="soft_author" size="20" group="basic" alt="软件作者" value=''匿名''/>\r\n<field name="soft_nature" tag="radio"  id="soft_nature" value=''授权免费,开源软件,共享软件,商业软件'' group="basic" alt="软件性质"/>\r\n<field name="soft_version" tag="input" type="text"  id="soft_version" size="20" group="basic" alt="软件版本" value=''1.0''/>\r\n<field name="soft_lang" tag="select"  id="soft_nature" value=''简体中文,繁体中文,英文,多国语''  group="basic" alt="软件语言"/>\r\n<field name="soft_platform" tag="checkbox" id="soft_platform" value=''xp,win2003,vista,win7,win8,linux,unix'' group="basic" alt="软件平台" src="./Public/Model/download/selectall.html"/>\r\n<field name="soft_size" tag="input" type="text"  id="soft_size" size="20" group="basic" alt="软件大小（KB）" value=''0''/>\r\n<field name="soft_contact" tag="input" type="text"  id="soft_contact" size="20" group="basic" alt="联系人" value=''暂无''/>\r\n<field name="soft_downloadnum" tag="input" type="text"  id="soft_downloadnum" size="20" group="basic" alt="下载次数" value=''1'' src="./Public/Model/download/upload.html"/>\r\n<field name="downloadurl" tag="textarea"  id="downloadurl" group="basic" style="width:800px;height:100px;" alt="下载地址（多个地址请换行）"/>\r\n<field name="body" tag="editor" id="Content" group="basic" style="width:800px;height:300px;" alt="下载介绍" src="./Public/Model/download/docheck.html"/>\r\n</fieldset>', 'addondownload', 1, 0);