drop TABLE IF EXISTS `{TABLE_PREFIX}members` ;

CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}members` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(15) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `created` char(10) NOT NULL,
  `modified` char(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;


INSERT INTO `{TABLE_PREFIX}members` (`uid`, `username`, `password`, `email`, `created`, `modified`) VALUES
(1, 'admin', '158359df7bf29bf903df94b517573c4a', 'ksgujie@gmail.com', '', '1266595390'),
(2, '顾杰杰', '1266595390', '', '', '1266593808'),
(3, '冰心凉凉''''', 'e55a03d3797278e016fff64117059394', '', '', ''),
(6, '顾杰', '123456', '', '', ''),
(7, '雨雨2', '16164ef4bbc6a81', 'ksgujie@21cn.com', '', ''),
(8, 'bluebirld', '21232f297a57a5a743894a0e4a801fc3', '', '', '1266595151'),
(9, '和韫在一起', 'eea86bf36edb3e3d21693ff11ae9c899', '', '', '');
