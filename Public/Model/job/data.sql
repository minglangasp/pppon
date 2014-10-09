CREATE TABLE IF NOT EXISTS `#@__addonjob` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `body` mediumtext COMMENT '职位描述',
  `redirecturl` varchar(255) NOT NULL,
  `job_num` char(20) NOT NULL DEFAULT '若干' COMMENT '招聘人数',
  `job_workplace` char(50) NOT NULL COMMENT '工作地点',
  `job_lang` char(50) NOT NULL COMMENT '语言要求',
  `job_degree` char(10) NOT NULL COMMENT '学历',
  `job_job` char(50) NOT NULL COMMENT '职位职能',
  `job_money` char(50) NOT NULL COMMENT '工作薪水',
  `job_year` char(20) NOT NULL COMMENT '工作年限',
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
###
INSERT INTO `#@__arcmodel` (`id`, `nid`, `typename`, `titlename`, `fieldset`, `addtable`, `issystem`, `status`) VALUES
(__LINE__, 'job', '招聘', '招聘标题', '<fieldset>\r\n<field name="redirecturl" tag="input" type="text"  id="redirecturl" size="50" group="basic" alt="跳转地址"/>\r\n<field name="job_num" tag="input" type="text"  id="job_num" size="20" group="basic" alt="招聘人数" value=''1''/>\r\n<field name="job_money" tag="input"  size=''50'' id="job_money" value=''面议''  group="basic" alt="工作薪水"/>\r\n<field name="job_lang" tag="input"  id="job_lang" value=''不限'' group="basic" alt="语言要求" size=''50''/>\r\n<field name="job_degree" tag="radio" type="text"  id="job_degree"  group="basic" alt="学历要求" value=''不限,小学,初中,高中,中专,大专,专科,本科,硕士,博士,博士后''/>\r\n<field name="job_job" tag="input"  size=''50'' id="job_job" value=''干事''  group="basic" alt="职位职能"/>\r\n<field name="job_year" tag="radio" id="job_year" value=''不限,半年,一年,两年,三年,四年,五年'' group="basic" alt="工作年限" />\r\n<field name="body" tag="editor" id="Content" group="basic" style="width:800px;height:300px;" alt="职位详细描述" />\r\n</fieldset>', 'addonjob', 1, 0);