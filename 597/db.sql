/*
SQLyog 企业版 - MySQL GUI v7.14 
MySQL - 5.5.24 : Database - 597
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

USE `597`;

/*Table structure for table `site` */

DROP TABLE IF EXISTS `site`;

CREATE TABLE `site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '站点名称',
  `surl` varchar(255) NOT NULL COMMENT '源站URL',
  `susr` varchar(20) NOT NULL COMMENT '源站用户名',
  `spwd` varchar(20) NOT NULL COMMENT '源站密码',
  `turl` varchar(255) NOT NULL COMMENT '目标站URL',
  `tusr` varchar(20) NOT NULL COMMENT '目标站用户名',
  `tpwd` varchar(20) NOT NULL COMMENT '目标站密码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=gbk;

/*Data for the table `site` */

insert  into `site`(`id`,`name`,`surl`,`susr`,`spwd`,`turl`,`tusr`,`tpwd`) values (1,'福州','http://www.fz597.com/','','','http://www.0591job.com.cn/','',''),(2,'厦门','http://xm.597.com/','','','http://www.0592job.com.cn/','','');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
