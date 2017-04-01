-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 �?04 �?01 �?17:31
-- 服务器版本: 5.5.40
-- PHP 版本: 5.5.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `ci_ims`
--

-- --------------------------------------------------------

--
-- 表的结构 `ci_admin`
--

CREATE TABLE IF NOT EXISTS `ci_admin` (
  `uid` smallint(6) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '用户名称',
  `userpwd` varchar(32) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '密码',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否锁定',
  `name` varchar(25) COLLATE utf8_unicode_ci DEFAULT '',
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT '',
  `lever` text COLLATE utf8_unicode_ci COMMENT '权限',
  `roleid` tinyint(1) DEFAULT '1' COMMENT '角色ID',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='后台用户' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `ci_admin`
--

INSERT INTO `ci_admin` (`uid`, `username`, `userpwd`, `status`, `name`, `mobile`, `lever`, `roleid`) VALUES
(1, 'admin', '538b65e68ce92c40af55cfeca5fc4068', 1, '小阳', '', NULL, 0);

-- --------------------------------------------------------

--
-- 表的结构 `ci_category`
--

CREATE TABLE IF NOT EXISTS `ci_category` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '导航栏目',
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '栏目名称',
  `pid` smallint(6) DEFAULT '0' COMMENT '上级栏目ID',
  `path` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '栏目路径',
  `depth` tinyint(2) DEFAULT '1' COMMENT '层次',
  `ordnum` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `type` varchar(25) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '区别',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='客户、商品、供应商类别' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_category_type`
--

CREATE TABLE IF NOT EXISTS `ci_category_type` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '导航栏目',
  `name` varchar(20) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '栏目名称',
  `number` varchar(15) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT '上级栏目ID',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `pid` (`number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='客户、商品、供应商类别' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `ci_category_type`
--

INSERT INTO `ci_category_type` (`id`, `name`, `number`, `status`) VALUES
(1, '商品类别', 'trade', 1),
(2, '户类别', 'customertype', 1),
(3, '供应商类别', 'supplytype', 1);

-- --------------------------------------------------------

--
-- 表的结构 `ci_contact`
--

CREATE TABLE IF NOT EXISTS `ci_contact` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0' COMMENT '客户名称',
  `number` varchar(50) DEFAULT '0' COMMENT '客户编号',
  `categoryid` smallint(6) DEFAULT '0' COMMENT '客户类别',
  `categoryname` varchar(50) DEFAULT '' COMMENT '分类名称',
  `taxrate` double DEFAULT '0' COMMENT '税率',
  `amount` double DEFAULT '0' COMMENT '期初应付款',
  `periodmoney` double DEFAULT '0' COMMENT '期初预付款',
  `begindate` datetime DEFAULT NULL COMMENT '余额日期',
  `remark` varchar(100) DEFAULT '' COMMENT '备注',
  `linkmans` text COMMENT '客户联系方式',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `type` tinyint(1) DEFAULT '1' COMMENT '1客户  2供应商',
  `contact` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户、供应商管理' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_goods`
--

CREATE TABLE IF NOT EXISTS `ci_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT '',
  `number` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '商品编号',
  `quantity` double DEFAULT '0' COMMENT '起初数量',
  `spec` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '规格',
  `unitid` smallint(6) DEFAULT '0' COMMENT '单位ID',
  `unitname` varchar(10) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '单位名称',
  `categoryid` smallint(6) DEFAULT '0' COMMENT '商品分类ID',
  `categoryname` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '分类名称',
  `purprice` double DEFAULT '0' COMMENT '预计采购价',
  `saleprice` double DEFAULT '0' COMMENT '预计销售价',
  `unitcost` double DEFAULT '0' COMMENT '单位成本',
  `amount` double DEFAULT '0' COMMENT '期初总价',
  `remark` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `goods` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `number` (`number`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='商品管理' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_invoi`
--

CREATE TABLE IF NOT EXISTS `ci_invoi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contactid` smallint(6) DEFAULT '0' COMMENT '供应商ID',
  `contactname` varchar(50) DEFAULT '',
  `billno` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '单据编号',
  `uid` smallint(6) DEFAULT '0',
  `username` varchar(50) DEFAULT '' COMMENT '制单人',
  `type` tinyint(1) DEFAULT '1' COMMENT '1其他入库  2盘盈  3其他出库  4盘亏',
  `typename` varchar(20) DEFAULT '',
  `totalamount` double DEFAULT '0' COMMENT '金额',
  `totalqty` double DEFAULT '0',
  `billdate` date DEFAULT NULL COMMENT '单据日期',
  `description` varchar(50) DEFAULT '' COMMENT '备注',
  `billtype` tinyint(1) DEFAULT '1' COMMENT '1入库  2出库',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='其他入库、出库记录' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_invoi_info`
--

CREATE TABLE IF NOT EXISTS `ci_invoi_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoiid` int(11) DEFAULT '0',
  `contactno` varchar(50) DEFAULT '' COMMENT '供应商编号',
  `contactid` smallint(6) DEFAULT '0' COMMENT '供应商ID',
  `contactname` varchar(50) DEFAULT '' COMMENT '供应商名称',
  `billno` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '单据编号',
  `type` tinyint(1) DEFAULT '1' COMMENT '1其他入库 2盘盈 3其他出库 4盘亏',
  `typename` varchar(20) DEFAULT '',
  `amount` double DEFAULT '0' COMMENT '购货金额',
  `billdate` date DEFAULT NULL COMMENT '单据日期',
  `description` varchar(50) DEFAULT '' COMMENT '备注',
  `goodsno` varchar(50) DEFAULT '' COMMENT '商品编号',
  `goodsid` int(11) DEFAULT '0' COMMENT '商品ID',
  `price` double DEFAULT '0' COMMENT '单价',
  `qty` double DEFAULT '0' COMMENT '数量',
  `billtype` tinyint(1) DEFAULT '1' COMMENT '1入库 2出库',
  PRIMARY KEY (`id`),
  KEY `goodsid` (`goodsid`),
  KEY `type` (`type`),
  KEY `billdate` (`billdate`),
  KEY `type,billdate` (`type`,`billdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='其他入库、出库明细' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_invoi_type`
--

CREATE TABLE IF NOT EXISTS `ci_invoi_type` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '名称',
  `inout` tinyint(1) DEFAULT '1' COMMENT '1 入库  -1出库',
  `status` tinyint(1) DEFAULT '1',
  `type` varchar(10) DEFAULT '',
  `default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='其他入库类型' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `ci_invoi_type`
--

INSERT INTO `ci_invoi_type` (`id`, `name`, `inout`, `status`, `type`, `default`) VALUES
(1, '其他入库', 1, 1, 'in', 1),
(2, '盘盈', 1, 1, 'in', 0),
(3, '其他出库', -1, 1, 'out', 1),
(4, '盘亏', -1, 1, 'out', 0);

-- --------------------------------------------------------

--
-- 表的结构 `ci_invpu`
--

CREATE TABLE IF NOT EXISTS `ci_invpu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contactid` smallint(6) DEFAULT '0' COMMENT '供应商ID',
  `contactname` varchar(50) DEFAULT '' COMMENT '供应商名称',
  `billno` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '单据编号',
  `uid` smallint(6) DEFAULT '0',
  `username` varchar(50) DEFAULT '' COMMENT '制单人',
  `type` tinyint(1) DEFAULT '1' COMMENT '1购货 2退货',
  `totalamount` double DEFAULT '0' COMMENT '购货总金额',
  `amount` double DEFAULT '0' COMMENT '折扣后金额',
  `rpamount` double DEFAULT '0' COMMENT '已付款金额',
  `billdate` date DEFAULT NULL COMMENT '单据日期',
  `description` varchar(100) DEFAULT '' COMMENT '备注',
  `arrears` double DEFAULT '0' COMMENT '本次欠款',
  `disrate` double DEFAULT '0' COMMENT '折扣率',
  `disamount` double DEFAULT '0' COMMENT '折扣金额',
  `totalqty` double DEFAULT '0' COMMENT '总数量',
  `totalarrears` double DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购记录' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_invpu_info`
--

CREATE TABLE IF NOT EXISTS `ci_invpu_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invpuid` int(11) DEFAULT '0' COMMENT '关联ID',
  `contactno` varchar(50) DEFAULT '' COMMENT '供应商编号',
  `contactid` smallint(6) DEFAULT '0' COMMENT '供应商ID',
  `contactname` varchar(50) DEFAULT '' COMMENT '供应商名称',
  `billno` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '单据编号',
  `type` tinyint(1) DEFAULT '1' COMMENT '1采购 2退货',
  `amount` double DEFAULT '0' COMMENT '购货金额',
  `billdate` date DEFAULT NULL COMMENT '单据日期',
  `description` varchar(50) DEFAULT '' COMMENT '备注',
  `goodsno` varchar(50) DEFAULT '' COMMENT '商品编号',
  `goodsid` int(11) DEFAULT '0' COMMENT '商品ID',
  `price` double DEFAULT '0' COMMENT '单价',
  `deduction` double DEFAULT '0' COMMENT '折扣额',
  `discountrate` double DEFAULT '0' COMMENT '折扣率',
  `qty` double DEFAULT '0' COMMENT '数量',
  PRIMARY KEY (`id`),
  KEY `goodsid` (`goodsid`),
  KEY `type` (`type`),
  KEY `billdate` (`billdate`),
  KEY `type,billdate` (`type`,`billdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购明细' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_invsa`
--

CREATE TABLE IF NOT EXISTS `ci_invsa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contactid` smallint(6) unsigned zerofill DEFAULT NULL COMMENT '供应商ID',
  `contactname` varchar(50) DEFAULT '' COMMENT '供应商名称',
  `billno` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '单据编号',
  `uid` smallint(6) DEFAULT '0',
  `username` varchar(50) DEFAULT '' COMMENT '制单人',
  `type` tinyint(1) DEFAULT '1' COMMENT '1销货 2退货',
  `totalamount` double DEFAULT '0' COMMENT '购货总金额',
  `amount` double DEFAULT '0' COMMENT '折扣后金额',
  `rpamount` double DEFAULT '0' COMMENT '已付款金额',
  `billdate` date DEFAULT NULL COMMENT '单据日期',
  `description` varchar(100) DEFAULT '' COMMENT '备注',
  `arrears` double DEFAULT '0' COMMENT '本次欠款',
  `disrate` double DEFAULT '0' COMMENT '折扣率',
  `disamount` double DEFAULT '0' COMMENT '折扣金额',
  `totalqty` double DEFAULT '0' COMMENT '总数量',
  `totalarrears` double DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售记录' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_invsa_info`
--

CREATE TABLE IF NOT EXISTS `ci_invsa_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invsaid` int(11) DEFAULT '0' COMMENT '关联ID',
  `contactid` smallint(6) DEFAULT '0' COMMENT '客户ID',
  `contactno` varchar(50) DEFAULT '' COMMENT '客户编号',
  `contactname` varchar(50) DEFAULT '' COMMENT '客户名称',
  `billno` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '单据编号',
  `type` tinyint(1) DEFAULT '1' COMMENT '1销售 2退货',
  `amount` double DEFAULT '0' COMMENT '销货金额',
  `billdate` date DEFAULT NULL COMMENT '单据日期',
  `description` varchar(50) DEFAULT '' COMMENT '备注',
  `goodsid` int(11) DEFAULT '0' COMMENT '商品ID',
  `goodsno` varchar(50) DEFAULT '' COMMENT '商品编号',
  `price` double DEFAULT '0' COMMENT '单价',
  `deduction` double DEFAULT '0' COMMENT '折扣额',
  `discountrate` double DEFAULT '0' COMMENT '折扣率',
  `qty` double DEFAULT '0' COMMENT '数量',
  PRIMARY KEY (`id`),
  KEY `goodsid` (`goodsid`),
  KEY `type` (`type`),
  KEY `type,billdate` (`type`,`billdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购明细' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ci_log`
--

CREATE TABLE IF NOT EXISTS `ci_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` smallint(6) DEFAULT '0' COMMENT '用户ID',
  `name` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '姓名',
  `log` varchar(50) DEFAULT '' COMMENT '日志内容',
  `type` tinyint(1) DEFAULT '1' COMMENT ' ',
  `username` varchar(50) DEFAULT '' COMMENT '用户名',
  `modifytime` datetime DEFAULT NULL COMMENT '写入日期',
  `adddate` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='操作日志' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `ci_log`
--

INSERT INTO `ci_log` (`id`, `userid`, `name`, `log`, `type`, `username`, `modifytime`, `adddate`) VALUES
(1, 1, '小阳', '登陆成功 用户名：admin', 1, 'admin', '2015-09-07 09:19:17', '2015-09-07'),
(2, 1, '小阳', '登陆成功 用户名：admin', 1, 'admin', '2017-04-01 14:49:45', '2017-04-01'),
(3, 1, '小阳', '登陆成功 用户名：admin', 1, 'admin', '2017-04-01 14:50:34', '2017-04-01');

-- --------------------------------------------------------

--
-- 表的结构 `ci_menu`
--

CREATE TABLE IF NOT EXISTS `ci_menu` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT '导航栏目',
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '栏目名称',
  `pid` smallint(5) DEFAULT '0' COMMENT '上级栏目ID',
  `path` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '栏目路径',
  `depth` tinyint(2) DEFAULT '1' COMMENT '层次',
  `ordnum` smallint(6) DEFAULT '0' COMMENT '排序',
  `url` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '外部链接',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='导航管理' AUTO_INCREMENT=85 ;

--
-- 转存表中的数据 `ci_menu`
--

INSERT INTO `ci_menu` (`id`, `title`, `pid`, `path`, `depth`, `ordnum`, `url`, `status`) VALUES
(1, '购货单', 1, '1', 1, 99, '', 1),
(2, '新增', 1, '1,2', 2, 99, '', 1),
(3, '修改', 1, '1,3', 2, 99, '', 1),
(4, '删除', 1, '1,4', 2, 99, '', 1),
(5, '导出', 1, '1,5', 2, 99, '', 1),
(6, '销货单', 6, '6', 1, 99, '', 1),
(7, '新增', 6, '6,7', 2, 99, '', 1),
(8, '修改', 6, '6,8', 2, 99, '', 1),
(9, '删除', 6, '6,9', 2, 99, '', 1),
(10, '导出', 6, '6,10', 2, 99, '', 1),
(11, '盘点', 11, '11', 1, 99, '', 1),
(12, '生成盘点记录', 11, '11,12', 2, 99, '', 1),
(13, '导出', 11, '11,13', 2, 99, '', 1),
(14, '其他入库', 14, '14', 1, 99, '', 1),
(15, '新增', 14, '14,15', 2, 99, '', 1),
(16, '修改', 14, '14,16', 2, 99, '', 1),
(17, '删除', 14, '14,17', 2, 99, '', 1),
(18, '其他出库', 18, '18', 1, 99, '', 1),
(19, '新增', 18, '18,19', 2, 99, '', 1),
(20, '修改', 18, '18,20', 2, 99, '', 1),
(21, '删除', 18, '18,21', 2, 99, '', 1),
(22, '采购明细表', 22, '22', 1, 99, '', 1),
(23, '导出', 22, '22,23', 2, 99, '', 1),
(24, '打印', 22, '22,24', 2, 99, '', 1),
(25, '采购汇总表（按商品）', 25, '25', 1, 99, '', 1),
(26, '导出', 25, '25,26', 2, 99, '', 1),
(27, '打印', 25, '25,27', 2, 99, '', 1),
(28, '采购汇总表（按供应商）', 28, '28', 1, 99, '', 1),
(29, '导出', 28, '28,29', 2, 99, '', 1),
(30, '打印', 28, '28,30', 2, 99, '', 1),
(31, '销售明细表', 31, '31', 1, 99, '', 1),
(32, '导出', 31, '31,32', 2, 99, '', 1),
(33, '打印', 31, '31,33', 2, 99, '', 1),
(34, '销售汇总表（按商品）', 34, '34', 1, 99, '', 1),
(35, '导出', 34, '34,35', 2, 99, '', 1),
(36, '打印', 34, '34,36', 2, 99, '', 1),
(37, '销售汇总表（按客户）', 37, '37', 1, 99, '', 1),
(38, '导出', 37, '37,38', 2, 99, '', 1),
(39, '打印', 37, '37,39', 2, 99, '', 1),
(40, '商品库存余额表', 40, '40', 1, 99, '', 1),
(41, '导出', 40, '40,41', 2, 99, '', 1),
(42, '打印', 40, '40,42', 2, 99, '', 1),
(43, '商品收发明细表', 43, '43', 1, 99, '', 1),
(44, '导出', 43, '43,44', 2, 99, '', 1),
(45, '打印', 43, '43,45', 2, 99, '', 1),
(46, '商品收发汇总表', 46, '46', 1, 99, '', 1),
(47, '导出', 46, '46,47', 2, 99, '', 1),
(48, '打印', 46, '46,48', 2, 99, '', 1),
(49, '往来单位欠款表', 49, '49', 1, 99, '', 1),
(50, '导出', 49, '49,50', 2, 99, '', 1),
(51, '打印', 49, '49,51', 2, 99, '', 1),
(52, '应付账款明细表', 52, '52', 1, 99, '', 1),
(53, '导出', 52, '52,53', 2, 99, '', 1),
(54, '打印', 52, '52,54', 2, 99, '', 1),
(55, '应收账款明细表', 55, '55', 1, 99, '', 1),
(56, '导出', 55, '55,56', 2, 99, '', 1),
(57, '打印', 55, '55,57', 2, 99, '', 1),
(58, '客户管理', 58, '58', 1, 99, '', 1),
(59, '新增', 58, '58,59', 2, 99, '', 1),
(60, '修改', 58, '58,60', 2, 99, '', 1),
(61, '删除', 58, '58,61', 2, 99, '', 1),
(62, '导出', 58, '58,62', 2, 99, '', 1),
(63, '供应商管理', 63, '63', 1, 99, '', 1),
(64, '新增', 63, '63,64', 2, 99, '', 1),
(65, '修改', 63, '63,65', 2, 99, '', 1),
(66, '删除', 63, '63,66', 2, 99, '', 1),
(67, '导出', 63, '63,67', 2, 99, '', 1),
(68, '商品管理', 68, '68', 1, 99, '', 1),
(69, '新增', 68, '68,69', 2, 99, '', 1),
(70, '修改', 68, '68,70', 2, 99, '', 1),
(71, '删除', 68, '68,71', 2, 99, '', 1),
(72, '导出', 68, '68,72', 2, 99, '', 1),
(73, '客户类别', 73, '73', 1, 99, '', 1),
(74, '新增', 73, '73,74', 2, 99, '', 1),
(75, '修改', 73, '73,75', 2, 99, '', 1),
(76, '删除', 73, '73,76', 2, 99, '', 1),
(77, '计量单位', 77, '77', 1, 99, '', 1),
(78, '新增', 77, '77,78', 2, 99, '', 1),
(79, '修改', 77, '77,79', 2, 99, '', 1),
(80, '删除', 77, '77,80', 2, 99, '', 1),
(81, '系统参数', 81, '81', 1, 99, '', 1),
(82, '权限设置', 82, '82', 1, 99, '', 1),
(83, '操作日志', 83, '83', 1, 99, '', 1),
(84, '数据备份', 84, '84', 1, 99, '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `ci_unit`
--

CREATE TABLE IF NOT EXISTS `ci_unit` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '客户名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='计量单位' AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `ci_unit`
--

INSERT INTO `ci_unit` (`id`, `name`, `status`) VALUES
(1, '个', 1),
(2, '件', 1),
(3, '斤', 1),
(4, '包', 1),
(5, '台', 1),
(6, '箱', 1),
(7, '套', 1),
(8, '桶', 1),
(9, '辆', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
