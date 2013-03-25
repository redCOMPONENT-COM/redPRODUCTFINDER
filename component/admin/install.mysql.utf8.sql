CREATE TABLE IF NOT EXISTS `#__redproductfinder_association_tag` (
  `association_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `quality_score` int(10) unsigned NOT NULL,
  UNIQUE KEY `association_tag` (`association_id`,`tag_id`,`type_id`)
) COMMENT='redPRODUCTFINDER Association Tag Cross Reference';

CREATE TABLE IF NOT EXISTS `#__redproductfinder_associations` (
  `id` int(11) NOT NULL auto_increment,
  `published` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `aliases` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) COMMENT='redPRODUCTFINDER Associatons';

CREATE TABLE IF NOT EXISTS `#__redproductfinder_tag_type` (
  `tag_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `tag_type` (`tag_id`,`type_id`)
) COMMENT='redPRODUCTFINDER Tag Type Cross Reference';

CREATE TABLE IF NOT EXISTS `#__redproductfinder_tags` (
  `id` int(11) NOT NULL auto_increment,
  `published` tinyint(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `tag_name` varchar(255) default NULL,
  `type_id` int(11) NOT NULL,
  `aliases` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) COMMENT='redPRODUCTFINDER Tags';

CREATE TABLE IF NOT EXISTS `#__redproductfinder_types` (
  `id` int(11) NOT NULL auto_increment,
  `published` tinyint(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `type_name` varchar(255) default NULL,
  `type_select` varchar(45) NOT NULL default 'generic',
  `tooltip` varchar(255) default NULL,
  `form_id` int(11) NOT NULL,
  `picker` tinyint(11) NOT NULL,
  `extrafield` int(11) NOT NULL,	
  PRIMARY KEY  (`id`)
) COMMENT='redPRODUCTFINDER Tags';

CREATE TABLE IF NOT EXISTS `#__redproductfinder_forms` (
  `id` int(11) NOT NULL auto_increment,
  `formname` varchar(100) NOT NULL default 'NoName',
  `published` int(1) NOT NULL default '0',
  `checked_out` int(11) default NULL,
  `checked_out_time` datetime default '0000-00-00 00:00:00',
  `showname` int(1) NOT NULL default '0',
  `classname` varchar(45) default NULL,
  `formexpires` tinyint(1) NOT NULL default '1',
  `dependency` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) COMMENT='redPRODUCTFINDER Forms';

CREATE TABLE IF NOT EXISTS `#__redproductfinder_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `published` tinyint(4) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `filter_name` varchar(255) NOT NULL,
  `type_select` varchar(50) NOT NULL,
  `tag_id` text NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='redPRODUCTFINDER Filters';

CREATE TABLE IF NOT EXISTS `#__redproductfinder_dependent_tag` (
  `product_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `dependent_tags` text NOT NULL,
  UNIQUE KEY `product_id` (`product_id`,`tag_id`,`type_id`)
)  COMMENT='redPRODUCTFINDER dependent tag';
