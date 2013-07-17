

DROP TABLE IF EXISTS `list`;
CREATE TABLE IF NOT EXISTS `list` (
  `fid` int(11) NOT NULL COMMENT '帖子ID',
  `tid` int(11) NOT NULL COMMENT '版块ID',
  `currpage` int(11) NOT NULL DEFAULT '0' COMMENT '页面',
  `lastpage` int(11) NOT NULL DEFAULT '0',
  `ok` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fid`),
  KEY `tid` (`tid`),
  KEY `ok` (`ok`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;







DROP TABLE IF EXISTS `reply`;
CREATE TABLE IF NOT EXISTS `reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `content` text NOT NULL,
  `ord` int(11) NOT NULL,
  `ok` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`,`ord`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk;







DROP TABLE IF EXISTS `subject`;
CREATE TABLE IF NOT EXISTS `subject` (
  `fid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `postid` int(11) NOT NULL DEFAULT '0' COMMENT '发布在0555hs.com论坛上的ID',
  `ok` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fid`),
  KEY `ok` (`ok`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;
