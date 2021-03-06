-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 03 月 14 日 14:55
-- 服务器版本: 5.6.10
-- PHP 版本: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `aibang`
--

-- --------------------------------------------------------

--
-- 表的结构 `area`
--

CREATE TABLE IF NOT EXISTS `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `ok` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=483 ;

--
-- 转存表中的数据 `area`
--

INSERT INTO `area` (`id`, `province`, `city`, `country`, `ok`) VALUES
(1, '北京', '北京', '朝阳区', 0),
(2, '北京', '北京', '东城区', 0),
(3, '北京', '北京', '西城区', 0),
(4, '北京', '北京', '海淀区', 0),
(5, '北京', '北京', '丰台区', 0),
(6, '北京', '北京', '石景山区', 0),
(7, '北京', '北京', '大兴区', 0),
(8, '北京', '北京', '通州区', 0),
(9, '北京', '北京', '昌平区', 0),
(10, '北京', '北京', '房山区', 0),
(11, '北京', '北京', '怀柔区', 0),
(12, '北京', '北京', '门头沟区', 0),
(13, '北京', '北京', '密云区', 0),
(14, '北京', '北京', '平谷区', 0),
(15, '北京', '北京', '顺义区', 0),
(16, '北京', '北京', '延庆区', 0),
(17, '北京', '北京', '郊区', 0),
(18, '重庆', '重庆', '江北区', 0),
(19, '重庆', '重庆', '渝中区', 0),
(20, '重庆', '重庆', '九龙坡区', 0),
(21, '重庆', '重庆', '沙坪坝区', 0),
(22, '重庆', '重庆', '南岸区', 0),
(23, '重庆', '重庆', '渝北区', 0),
(24, '重庆', '重庆', '巴南区', 0),
(25, '重庆', '重庆', '北碚区', 0),
(26, '重庆', '重庆', '大渡口区', 0),
(27, '上海', '上海', '卢湾区', 0),
(28, '上海', '上海', '徐汇区', 0),
(29, '上海', '上海', '静安区', 0),
(30, '上海', '上海', '长宁区', 0),
(31, '上海', '上海', '闵行区', 0),
(32, '上海', '上海', '浦东新区', 0),
(33, '上海', '上海', '黄浦区', 0),
(34, '上海', '上海', '普陀区', 0),
(35, '上海', '上海', '闸北区', 0),
(36, '上海', '上海', '虹口区', 0),
(37, '上海', '上海', '杨浦区', 0),
(38, '上海', '上海', '大华地区', 0),
(39, '上海', '上海', '松江区', 0),
(40, '上海', '上海', '嘉定区', 0),
(41, '上海', '上海', '青浦区', 0),
(42, '上海', '上海', '崇明县', 0),
(43, '上海', '上海', '奉贤区', 0),
(44, '上海', '上海', '金山区', 0),
(45, '天津', '天津', '南开区', 0),
(46, '天津', '天津', '河西区', 0),
(47, '天津', '天津', '河东区', 0),
(48, '天津', '天津', '和平区', 0),
(49, '天津', '天津', '河北区', 0),
(50, '天津', '天津', '红桥区', 0),
(51, '天津', '天津', '塘沽区', 0),
(52, '天津', '天津', '东丽区', 0),
(53, '天津', '天津', '西青区', 0),
(54, '天津', '天津', '北辰区', 0),
(55, '天津', '天津', '津南区', 0),
(56, '天津', '天津', '大港区', 0),
(57, '天津', '天津', '开发区', 0),
(58, '安徽', '合肥', '蜀山区', 0),
(59, '安徽', '合肥', '庐阳区', 0),
(60, '安徽', '合肥', '包河区', 0),
(61, '安徽', '合肥', '瑶海区', 0),
(62, '安徽', '安庆', '', 0),
(63, '安徽', '蚌埠', '', 0),
(64, '安徽', '亳州', '', 0),
(65, '安徽', '巢湖', '', 0),
(66, '安徽', '池州', '', 0),
(67, '安徽', '滁州', '', 0),
(68, '安徽', '阜阳', '', 0),
(69, '安徽', '淮北', '', 0),
(70, '安徽', '淮南', '', 0),
(71, '安徽', '黄山', '', 0),
(72, '安徽', '六安', '', 0),
(73, '安徽', '马鞍山', '', 0),
(74, '安徽', '宿州', '', 0),
(75, '安徽', '铜陵', '', 0),
(76, '安徽', '芜湖', '', 0),
(77, '安徽', '宣城', '', 0),
(78, '福建', '福州', '鼓楼区', 0),
(79, '福建', '福州', '台江区', 0),
(80, '福建', '福州', '晋安区', 0),
(81, '福建', '福州', '仓山区', 0),
(82, '福建', '福州', '马尾区', 0),
(83, '福建', '龙岩', '', 0),
(84, '福建', '南平', '', 0),
(85, '福建', '宁德', '', 0),
(86, '福建', '莆田', '', 0),
(87, '福建', '泉州', '', 0),
(88, '福建', '三明', '', 0),
(89, '福建', '厦门', '思明区', 0),
(90, '福建', '厦门', '湖里区', 0),
(91, '福建', '厦门', '集美区', 0),
(92, '福建', '厦门', '海沧区', 0),
(93, '福建', '厦门', '同安区', 0),
(94, '福建', '厦门', '翔安区', 0),
(95, '福建', '漳州', '', 0),
(96, '甘肃', '白银', '', 0),
(97, '甘肃', '兰州', '城关区', 0),
(98, '甘肃', '兰州', '七里河区', 0),
(99, '甘肃', '兰州', '安宁区', 0),
(100, '甘肃', '兰州', '西固区', 0),
(101, '甘肃', '酒泉', '', 0),
(102, '甘肃', '金昌', '', 0),
(103, '甘肃', '武威', '', 0),
(104, '广东', '广州', '天河区', 0),
(105, '广东', '广州', '越秀区', 0),
(106, '广东', '广州', '海珠区', 0),
(107, '广东', '广州', '荔湾区', 0),
(108, '广东', '广州', '白云区', 0),
(109, '广东', '广州', '番禺区', 0),
(110, '广东', '广州', '近郊', 0),
(111, '广东', '潮州', '', 0),
(112, '广东', '东莞', '', 0),
(113, '广东', '佛山', '', 0),
(114, '广东', '河源', '', 0),
(115, '广东', '惠州', '', 0),
(116, '广东', '江门', '', 0),
(117, '广东', '揭阳', '', 0),
(118, '广东', '茂名', '', 0),
(119, '广东', '梅州', '', 0),
(120, '广东', '清远', '', 0),
(121, '广东', '汕头', '', 0),
(122, '广东', '汕尾', '', 0),
(123, '广东', '韶关', '', 0),
(124, '广东', '深圳', '福田区', 0),
(125, '广东', '深圳', '罗湖区', 0),
(126, '广东', '深圳', '南山区', 0),
(127, '广东', '深圳', '盐田区', 0),
(128, '广东', '深圳', '宝安区', 0),
(129, '广东', '深圳', '龙岗区', 0),
(130, '广东', '阳江', '', 0),
(131, '广东', '云浮', '', 0),
(132, '广东', '湛江', '', 0),
(133, '广东', '肇庆', '', 0),
(134, '广东', '中山', '', 0),
(135, '广东', '珠海', '', 0),
(136, '广西', '南宁', '青秀区', 0),
(137, '广西', '南宁', '兴宁区', 0),
(138, '广西', '南宁', '江南区', 0),
(139, '广西', '南宁', '西乡塘区', 0),
(140, '广西', '南宁', '邕宁区', 0),
(141, '广西', '南宁', '良庆区', 0),
(142, '广西', '北海', '', 0),
(143, '广西', '防城港', '', 0),
(144, '广西', '桂林', '', 0),
(145, '广西', '河池', '', 0),
(146, '广西', '柳州', '', 0),
(147, '贵州', '贵阳', '云岩区', 0),
(148, '贵州', '贵阳', '南明区', 0),
(149, '贵州', '贵阳', '白云区', 0),
(150, '贵州', '贵阳', '乌当区', 0),
(151, '海南', '海口', '龙华区', 0),
(152, '海南', '海口', '秀英区', 0),
(153, '海南', '海口', '琼山区', 0),
(154, '海南', '海口', '美兰区', 0),
(155, '海南', '三亚', '', 0),
(156, '河北', '石家庄', '桥西区', 0),
(157, '河北', '石家庄', '桥东区', 0),
(158, '河北', '石家庄', '裕华区', 0),
(159, '河北', '石家庄', '长安区', 0),
(160, '河北', '石家庄', '新华区', 0),
(161, '河北', '石家庄', '井陉矿区', 0),
(162, '河北', '石家庄', '开发区', 0),
(163, '河北', '保定', '', 0),
(164, '河北', '沧州', '', 0),
(165, '河北', '承德', '', 0),
(166, '河北', '邯郸', '', 0),
(167, '河北', '衡水', '', 0),
(168, '河北', '廊坊', '', 0),
(169, '河北', '秦皇岛', '', 0),
(170, '河北', '唐山', '', 0),
(171, '河北', '邢台', '', 0),
(172, '河北', '张家口', '', 0),
(173, '河南', '郑州', '中原', 0),
(174, '河南', '郑州', '二七', 0),
(175, '河南', '郑州', '管城回族区', 0),
(176, '河南', '郑州', '金水', 0),
(177, '河南', '郑州', '上街', 0),
(178, '河南', '郑州', '惠济', 0),
(179, '河南', '郑州', '郑东新区', 0),
(180, '河南', '郑州', '经开区', 0),
(181, '河南', '安阳', '', 0),
(182, '河南', '鹤壁', '', 0),
(183, '河南', '济源', '', 0),
(184, '河南', '焦作', '', 0),
(185, '河南', '开封', '', 0),
(186, '河南', '洛阳', '', 0),
(187, '河南', '漯河', '', 0),
(188, '河南', '南阳', '', 0),
(189, '河南', '平顶山', '', 0),
(190, '河南', '濮阳', '', 0),
(191, '河南', '三门峡', '', 0),
(192, '河南', '商丘', '', 0),
(193, '河南', '新乡', '', 0),
(194, '河南', '信阳', '', 0),
(195, '河南', '许昌', '', 0),
(196, '河南', '周口', '', 0),
(197, '河南', '驻马店', '', 0),
(198, '黑龙江', '哈尔滨', '南岗区', 0),
(199, '黑龙江', '哈尔滨', '道里区', 0),
(200, '黑龙江', '哈尔滨', '道外区', 0),
(201, '黑龙江', '哈尔滨', '香坊区', 0),
(202, '黑龙江', '哈尔滨', '江北区', 0),
(203, '黑龙江', '哈尔滨', '开发区', 0),
(204, '黑龙江', '大庆', '', 0),
(205, '黑龙江', '鹤岗', '', 0),
(206, '黑龙江', '黑河', '', 0),
(207, '黑龙江', '鸡西', '', 0),
(208, '黑龙江', '佳木斯', '', 0),
(209, '黑龙江', '牡丹江', '', 0),
(210, '黑龙江', '七台河', '', 0),
(211, '黑龙江', '齐齐哈尔', '', 0),
(212, '黑龙江', '双鸭山', '', 0),
(213, '黑龙江', '绥化', '', 0),
(214, '黑龙江', '伊春', '', 0),
(215, '湖北', '武汉', '武昌区', 0),
(216, '湖北', '武汉', '洪山区', 0),
(217, '湖北', '武汉', '江岸区', 0),
(218, '湖北', '武汉', '东西湖区', 0),
(219, '湖北', '武汉', '江汉区', 0),
(220, '湖北', '武汉', '汉阳区', 0),
(221, '湖北', '武汉', '硚口区', 0),
(222, '湖北', '武汉', '青山区', 0),
(223, '湖北', '鄂州', '', 0),
(224, '湖北', '恩施', '', 0),
(225, '湖北', '黄冈', '', 0),
(226, '湖北', '黄石', '', 0),
(227, '湖北', '荆门', '', 0),
(228, '湖北', '荆州', '', 0),
(229, '湖北', '潜江', '', 0),
(230, '湖北', '十堰', '', 0),
(231, '湖北', '随州', '', 0),
(232, '湖北', '神农架', '', 0),
(233, '湖北', '天门', '', 0),
(234, '湖北', '仙桃', '', 0),
(235, '湖北', '咸宁', '', 0),
(236, '湖北', '襄樊', '', 0),
(237, '湖北', '孝感', '', 0),
(238, '湖北', '宜昌', '', 0),
(239, '湖南', '长沙', '芙蓉区', 0),
(240, '湖南', '长沙', '岳麓区', 0),
(241, '湖南', '长沙', '雨花区', 0),
(242, '湖南', '长沙', '天心区', 0),
(243, '湖南', '长沙', '开福区', 0),
(244, '湖南', '常德', '', 0),
(245, '湖南', '郴州', '', 0),
(246, '湖南', '衡阳', '', 0),
(247, '湖南', '怀化', '', 0),
(248, '湖南', '娄底', '', 0),
(249, '湖南', '邵阳', '', 0),
(250, '湖南', '湘潭', '', 0),
(251, '湖南', '益阳', '', 0),
(252, '湖南', '永州', '', 0),
(253, '湖南', '岳阳', '', 0),
(254, '湖南', '张家界', '', 0),
(255, '湖南', '株洲', '', 0),
(256, '吉林', '长春', '朝阳区', 0),
(257, '吉林', '长春', '南关区', 0),
(258, '吉林', '长春', '绿园区', 0),
(259, '吉林', '长春', '宽城区', 0),
(260, '吉林', '长春', '二道区', 0),
(261, '吉林', '长春', '双阳区', 0),
(262, '吉林', '白城', '', 0),
(263, '吉林', '白山', '', 0),
(264, '吉林', '吉林', '', 0),
(265, '吉林', '辽源', '', 0),
(266, '吉林', '四平', '', 0),
(267, '吉林', '松原', '', 0),
(268, '吉林', '通化', '', 0),
(269, '吉林', '延吉', '', 0),
(270, '江苏', '南京', '玄武区', 0),
(271, '江苏', '南京', '鼓楼区', 0),
(272, '江苏', '南京', '建邺区', 0),
(273, '江苏', '南京', '白下区', 0),
(274, '江苏', '南京', '秦淮区', 0),
(275, '江苏', '南京', '下关区', 0),
(276, '江苏', '南京', '雨花台区', 0),
(277, '江苏', '南京', '浦口区', 0),
(278, '江苏', '南京', '栖霞区', 0),
(279, '江苏', '南京', '江宁区', 0),
(280, '江苏', '南京', '六合区', 0),
(281, '江苏', '南京', '溧水', 0),
(282, '江苏', '南京', '高淳', 0),
(283, '江苏', '常州', '', 0),
(284, '江苏', '淮安', '', 0),
(285, '江苏', '连云港', '', 0),
(286, '江苏', '南通', '', 0),
(287, '江苏', '苏州', '沧浪区', 0),
(288, '江苏', '苏州', '相城区', 0),
(289, '江苏', '苏州', '平江', 0),
(290, '江苏', '苏州', '金阊', 0),
(291, '江苏', '苏州', '工业园', 0),
(292, '江苏', '苏州', '吴中', 0),
(293, '江苏', '苏州', '昆山', 0),
(294, '江苏', '苏州', '张家港', 0),
(295, '江苏', '苏州', '吴江', 0),
(296, '江苏', '苏州', '高新区', 0),
(297, '江苏', '宿迁', '', 0),
(298, '江苏', '泰州', '', 0),
(299, '江苏', '无锡', '', 0),
(300, '江苏', '徐州', '', 0),
(301, '江苏', '盐城', '', 0),
(302, '江苏', '扬州', '', 0),
(303, '江苏', '镇江', '', 0),
(304, '江西', '南昌', '东湖区', 0),
(305, '江西', '南昌', '西湖区', 0),
(306, '江西', '南昌', '青云谱区', 0),
(307, '江西', '南昌', '湾里区', 0),
(308, '江西', '南昌', '青山湖区', 0),
(309, '江西', '南昌', '红谷滩新区', 0),
(310, '江西', '南昌', '高新开发区', 0),
(311, '江西', '抚州', '', 0),
(312, '江西', '赣州', '', 0),
(313, '江西', '吉安', '', 0),
(314, '江西', '景德镇', '', 0),
(315, '江西', '九江', '', 0),
(316, '江西', '萍乡', '', 0),
(317, '江西', '上饶', '', 0),
(318, '江西', '新余', '', 0),
(319, '江西', '宜春', '', 0),
(320, '江西', '鹰潭', '', 0),
(321, '辽宁', '沈阳', '和平区', 0),
(322, '辽宁', '沈阳', '沈河区', 0),
(323, '辽宁', '沈阳', '皇姑区', 0),
(324, '辽宁', '沈阳', '大东区', 0),
(325, '辽宁', '沈阳', '铁西区', 0),
(326, '辽宁', '沈阳', '东陵区', 0),
(327, '辽宁', '沈阳', '于洪区', 0),
(328, '辽宁', '沈阳', '沈北新区', 0),
(329, '辽宁', '沈阳', '苏家屯区', 0),
(330, '辽宁', '沈阳', '浑南新区', 0),
(331, '辽宁', '鞍山', '', 0),
(332, '辽宁', '本溪', '', 0),
(333, '辽宁', '朝阳', '', 0),
(334, '辽宁', '大连', '西岗', 0),
(335, '辽宁', '大连', '中山', 0),
(336, '辽宁', '大连', '高新园', 0),
(337, '辽宁', '大连', '旅顺', 0),
(338, '辽宁', '大连', '金州', 0),
(339, '辽宁', '大连', '开发区', 0),
(340, '辽宁', '丹东', '', 0),
(341, '辽宁', '抚顺', '', 0),
(342, '辽宁', '阜新', '', 0),
(343, '辽宁', '葫芦岛', '', 0),
(344, '辽宁', '锦州', '', 0),
(345, '辽宁', '辽阳', '', 0),
(346, '辽宁', '盘锦', '', 0),
(347, '辽宁', '铁岭', '', 0),
(348, '辽宁', '营口', '', 0),
(349, '内蒙古', '呼和浩特', '新城区', 0),
(350, '内蒙古', '呼和浩特', '赛罕区', 0),
(351, '内蒙古', '呼和浩特', '回民区', 0),
(352, '内蒙古', '呼和浩特', '玉泉区', 0),
(353, '内蒙古', '包头', '', 0),
(354, '内蒙古', '赤峰', '', 0),
(355, '宁夏', '银川', '', 0),
(356, '青海', '西宁', '', 0),
(357, '山东', '济南', '历下区', 0),
(358, '山东', '济南', '市中区', 0),
(359, '山东', '济南', '天桥区', 0),
(360, '山东', '济南', '历城区', 0),
(361, '山东', '济南', '槐荫区', 0),
(362, '山东', '济南', '高新区', 0),
(363, '山东', '滨州', '', 0),
(364, '山东', '德州', '', 0),
(365, '山东', '东营', '', 0),
(366, '山东', '菏泽', '', 0),
(367, '山东', '济宁', '', 0),
(368, '山东', '莱芜', '', 0),
(369, '山东', '聊城', '', 0),
(370, '山东', '临沂', '', 0),
(371, '山东', '青岛', '市南区', 0),
(372, '山东', '青岛', '市北', 0),
(373, '山东', '青岛', '四方', 0),
(374, '山东', '青岛', '黄岛', 0),
(375, '山东', '青岛', '崂山', 0),
(376, '山东', '青岛', '李沧', 0),
(377, '山东', '青岛', '城阳', 0),
(378, '山东', '日照', '', 0),
(379, '山东', '泰安', '', 0),
(380, '山东', '威海', '', 0),
(381, '山东', '潍坊', '', 0),
(382, '山东', '烟台', '', 0),
(383, '山东', '枣庄', '', 0),
(384, '山东', '淄博', '', 0),
(385, '山西', '太原', '小店区', 0),
(386, '山西', '太原', '迎泽区', 0),
(387, '山西', '太原', '杏花岭区', 0),
(388, '山西', '太原', '万拍林区', 0),
(389, '山西', '太原', '尖草坪区', 0),
(390, '山西', '太原', '晋源区', 0),
(391, '山西', '长治', '', 0),
(392, '山西', '大同', '', 0),
(393, '山西', '晋城', '', 0),
(394, '山西', '晋中', '', 0),
(395, '山西', '临汾', '', 0),
(396, '山西', '吕梁', '', 0),
(397, '山西', '朔州', '', 0),
(398, '山西', '忻州', '', 0),
(399, '山西', '阳泉', '', 0),
(400, '山西', '运城', '', 0),
(401, '陕西', '西安', '雁塔区', 0),
(402, '陕西', '西安', '碑林区', 0),
(403, '陕西', '西安', '莲湖区', 0),
(404, '陕西', '西安', '新城区', 0),
(405, '陕西', '西安', '未央区', 0),
(406, '陕西', '西安', '长安区', 0),
(407, '陕西', '西安', '灞桥区', 0),
(408, '陕西', '西安', '临潼区', 0),
(409, '陕西', '西安', '阎良区', 0),
(410, '陕西', '西安', '高新区', 0),
(411, '陕西', '安康', '', 0),
(412, '陕西', '宝鸡', '', 0),
(413, '陕西', '汉中', '', 0),
(414, '陕西', '商洛', '', 0),
(415, '陕西', '铜川', '', 0),
(416, '陕西', '渭南', '', 0),
(417, '陕西', '咸阳', '', 0),
(418, '陕西', '延安', '', 0),
(419, '陕西', '榆林', '', 0),
(420, '四川', '成都', '武侯区', 0),
(421, '四川', '成都', '金牛区', 0),
(422, '四川', '成都', '青羊区', 0),
(423, '四川', '成都', '锦江区', 0),
(424, '四川', '成都', '成华区', 0),
(425, '四川', '成都', '新都区', 0),
(426, '四川', '成都', '高新区', 0),
(427, '四川', '成都', '高新西区', 0),
(428, '四川', '成都', '温江区', 0),
(429, '四川', '成都', '郫县', 0),
(430, '四川', '巴中', '', 0),
(431, '四川', '达州', '', 0),
(432, '四川', '德阳', '', 0),
(433, '四川', '广安', '', 0),
(434, '四川', '广元', '', 0),
(435, '四川', '乐山', '', 0),
(436, '四川', '泸州', '', 0),
(437, '四川', '眉山', '', 0),
(438, '四川', '绵阳', '', 0),
(439, '四川', '内江', '', 0),
(440, '四川', '南充', '', 0),
(441, '四川', '攀枝花', '', 0),
(442, '四川', '遂宁', '', 0),
(443, '四川', '雅安', '', 0),
(444, '四川', '宜宾', '', 0),
(445, '四川', '资阳', '', 0),
(446, '四川', '自贡', '', 0),
(447, '西藏', '拉萨', '', 0),
(448, '西藏', '日喀则', '', 0),
(449, '新疆', '乌鲁木齐', '', 0),
(450, '新疆', '吐鲁番', '', 0),
(451, '云南', '昆明', '盘龙区', 0),
(452, '云南', '昆明', '官渡区', 0),
(453, '云南', '昆明', '五华区', 0),
(454, '云南', '昆明', '西山区', 0),
(455, '云南', '大理', '', 0),
(456, '云南', '曲靖', '', 0),
(457, '云南', '西双版纳', '', 0),
(458, '云南', '玉溪', '', 0),
(459, '云南', '昭通', '', 0),
(460, '浙江', '杭州', '西湖区', 0),
(461, '浙江', '杭州', '拱墅区', 0),
(462, '浙江', '杭州', '江干区', 0),
(463, '浙江', '杭州', '下城区', 0),
(464, '浙江', '杭州', '上城区', 0),
(465, '浙江', '杭州', '滨江区', 0),
(466, '浙江', '杭州', '余杭区', 0),
(467, '浙江', '杭州', '萧山区', 0),
(468, '浙江', '湖州', '', 0),
(469, '浙江', '嘉兴', '', 0),
(470, '浙江', '金华', '', 0),
(471, '浙江', '丽水', '', 0),
(472, '浙江', '宁波', '海曙', 0),
(473, '浙江', '宁波', '江东', 0),
(474, '浙江', '宁波', '江北', 0),
(475, '浙江', '宁波', '鄞州', 0),
(476, '浙江', '宁波', '北仑', 0),
(477, '浙江', '宁波', '镇海', 0),
(478, '浙江', '衢州', '', 0),
(479, '浙江', '绍兴', '', 0),
(480, '浙江', '台州', '', 0),
(481, '浙江', '温州', '', 0),
(482, '浙江', '舟山', '', 0);
