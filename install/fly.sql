/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : fly

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 22/11/2018 15:42:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for f_category
-- ----------------------------
DROP TABLE IF EXISTS `f_category`;
CREATE TABLE `f_category`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分类名',
  `parent` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1' COMMENT '父ID',
  `level` tinyint(2) NOT NULL DEFAULT 0,
  `code` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '税号',
  `scope` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '物品范围',
  `tax_rate` float NOT NULL DEFAULT 0 COMMENT '税率',
  `sort` int(11) NOT NULL DEFAULT 0,
  `root_id` int(11) NOT NULL DEFAULT 0 COMMENT '根节点下一级节点',
  `lang` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'zh-cn' COMMENT '国家',
  `img_id` int(11) DEFAULT 0 COMMENT '类目图片id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品分类' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_category_prop
-- ----------------------------
DROP TABLE IF EXISTS `f_category_prop`;
CREATE TABLE `f_category_prop`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cate_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品属性' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_category_prop_value
-- ----------------------------
DROP TABLE IF EXISTS `f_category_prop_value`;
CREATE TABLE `f_category_prop_value`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` varchar(126) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prop_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品属性值' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_cms
-- ----------------------------
DROP TABLE IF EXISTS `f_cms`;
CREATE TABLE `f_cms`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` int(10) UNSIGNED DEFAULT NULL,
  `update_time` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'cms 配置/统计/...' ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for f_cms_cate
-- ----------------------------
DROP TABLE IF EXISTS `f_cms_cate`;
CREATE TABLE `f_cms_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `parent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'admin/user/edit or http',
  `show` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=>显示,0=>隐藏, 对超管无效',
  `icon` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'id/ icon class',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `level` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'cms分类' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_cms_cate
-- ----------------------------
INSERT INTO `f_cms_cate` VALUES (1, '后端', 0, '', 1, '', 1, 0, '', 1526093420, 1542598319, 1);
INSERT INTO `f_cms_cate` VALUES (2, '前端', 0, '', 0, 'layui-icon layui-icon-face-smile-fine', 0, 1, '', 1526093431, 1526452902, 1);
INSERT INTO `f_cms_cate` VALUES (3, 'css', 2, '', 1, '', 0, 1, '', 1526093535, 1526093535, 2);

-- ----------------------------
-- Table structure for f_cms_post
-- ----------------------------
DROP TABLE IF EXISTS `f_cms_post`;
CREATE TABLE `f_cms_post`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章分类 id',
  `title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文章标题',
  `author` int(11) NOT NULL COMMENT '文章作者',
  `excerpt` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文章摘要,默认前50字',
  `dt_types` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '特殊(精华,置顶,...),格式为 ,id,id2,.., 搜索时最好搜索一种',
  `main_img` int(11) UNSIGNED NOT NULL,
  `status` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '0默认(草稿),1=>(发布),-1=>删除(不对外)',
  `comments` bigint(20) NOT NULL COMMENT '评论数',
  `views` bigint(20) NOT NULL DEFAULT 0,
  `publish_time` int(10) UNSIGNED NOT NULL COMMENT '发布时间',
  `create_time` int(11) NOT NULL COMMENT '开始时间',
  `update_time` int(11) NOT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文章主表 , 使用, 第三方评论' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_cms_post
-- ----------------------------
INSERT INTO `f_cms_post` VALUES (2, 3, 'css模块化', 1, 'css模块化css模块化css模块化', '', 8, '1', 0, 0, 1970, 1527241523, 1542701090);

-- ----------------------------
-- Table structure for f_cms_post_extra
-- ----------------------------
DROP TABLE IF EXISTS `f_cms_post_extra`;
CREATE TABLE `f_cms_post_extra`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content_kwords` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分词信息',
  `kwords` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pid` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '文章额外信息 , 内容, 分词,..' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_cms_post_extra
-- ----------------------------
INSERT INTO `f_cms_post_extra` VALUES (1, '<p>test</p>', 'test', '', 1);
INSERT INTO `f_cms_post_extra` VALUES (2, '&lt;p&gt;css模块化css模块化css模块化&lt;/p&gt;&lt;p&gt;css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化 css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化css模块化&lt;/p&gt;', '模块化,css,lt,gt', '模块化,css,辅导费', 2);

-- ----------------------------
-- Table structure for f_cms_post_tag
-- ----------------------------
DROP TABLE IF EXISTS `f_cms_post_tag`;
CREATE TABLE `f_cms_post_tag`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) UNSIGNED NOT NULL COMMENT '文章id',
  `tag_id` bigint(20) UNSIGNED NOT NULL COMMENT '标签id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '文章标签' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_address
-- ----------------------------
DROP TABLE IF EXISTS `f_com_address`;
CREATE TABLE `f_com_address`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `country` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '国家',
  `country_id` int(11) NOT NULL DEFAULT 1 COMMENT '国家编号',
  `province` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '省',
  `province_id` char(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '城市',
  `city_id` char(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `area` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '区域',
  `area_id` char(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `detail` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '详细地址',
  `postal_code` int(11) NOT NULL DEFAULT 0 COMMENT '邮政编码',
  `contact_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `mobile` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电话',
  `wx_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信号',
  `id_card` char(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '身份证号',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户地址' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_area
-- ----------------------------
DROP TABLE IF EXISTS `f_com_area`;
CREATE TABLE `f_com_area`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '区域编号',
  `code` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '编码',
  `province` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '省',
  `city` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '城市',
  `district` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '区(县)',
  `parent` int(10) UNSIGNED NOT NULL COMMENT '父级',
  `py` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '拼音',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_open` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开通',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3925 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '国省市区' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_com_area
-- ----------------------------
INSERT INTO `f_com_area` VALUES (1, '+86', '中华人民共和国', '', '', 0, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2, '100000', '直辖市', '', '', 1, '', 1, 0);
INSERT INTO `f_com_area` VALUES (3, '110101', '', '', '东城区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (4, '110102', '', '', '西城区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (5, '110105', '', '', '朝阳区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (6, '110106', '', '', '丰台区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (7, '110107', '', '', '石景山区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (8, '110108', '', '', '海淀区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (9, '110109', '', '', '门头沟区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (10, '110111', '', '', '房山区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (11, '110112', '', '', '通州区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (12, '110113', '', '', '顺义区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (13, '110114', '', '', '昌平区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (14, '110115', '', '', '大兴区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (15, '110116', '', '', '怀柔区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (16, '110117', '', '', '平谷区', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (17, '110228', '', '', '密云县', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (18, '110229', '', '', '延庆县', 3924, '', 0, 0);
INSERT INTO `f_com_area` VALUES (19, '120000', '', '天津市', '', 2, '', 0, 0);
INSERT INTO `f_com_area` VALUES (20, '120101', '', '', '和平区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (21, '120102', '', '', '河东区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (22, '120103', '', '', '河西区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (23, '120104', '', '', '南开区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (24, '120105', '', '', '河北区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (25, '120106', '', '', '红桥区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (26, '120110', '', '', '东丽区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (27, '120111', '', '', '西青区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (28, '120112', '', '', '津南区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (29, '120113', '', '', '北辰区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (30, '120114', '', '', '武清区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (31, '120115', '', '', '宝坻区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (32, '120116', '', '', '滨海新区', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (33, '120221', '', '', '宁河县', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (34, '120223', '', '', '静海县', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (35, '120225', '', '', '蓟县', 19, '', 0, 0);
INSERT INTO `f_com_area` VALUES (36, '130000', '河北省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (37, '130100', '', '石家庄市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (38, '130101', '', '', '市辖区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (39, '130102', '', '', '长安区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (40, '130104', '', '', '桥西区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (41, '130105', '', '', '新华区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (42, '130107', '', '', '井陉矿区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (43, '130108', '', '', '裕华区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (44, '130109', '', '', '藁城区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (45, '130110', '', '', '鹿泉区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (46, '130111', '', '', '栾城区', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (47, '130121', '', '', '井陉县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (48, '130123', '', '', '正定县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (49, '130125', '', '', '行唐县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (50, '130126', '', '', '灵寿县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (51, '130127', '', '', '高邑县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (52, '130128', '', '', '深泽县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (53, '130129', '', '', '赞皇县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (54, '130130', '', '', '无极县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (55, '130131', '', '', '平山县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (56, '130132', '', '', '元氏县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (57, '130133', '', '', '赵县', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (58, '130181', '', '', '辛集市', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (59, '130183', '', '', '晋州市', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (60, '130184', '', '', '新乐市', 37, '', 0, 0);
INSERT INTO `f_com_area` VALUES (61, '130200', '', '唐山市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (62, '130201', '', '', '市辖区', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (63, '130202', '', '', '路南区', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (64, '130203', '', '', '路北区', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (65, '130204', '', '', '古冶区', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (66, '130205', '', '', '开平区', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (67, '130207', '', '', '丰南区', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (68, '130208', '', '', '丰润区', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (69, '130209', '', '', '曹妃甸区', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (70, '130223', '', '', '滦县', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (71, '130224', '', '', '滦南县', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (72, '130225', '', '', '乐亭县', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (73, '130227', '', '', '迁西县', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (74, '130229', '', '', '玉田县', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (75, '130281', '', '', '遵化市', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (76, '130283', '', '', '迁安市', 61, '', 0, 0);
INSERT INTO `f_com_area` VALUES (77, '130300', '', '秦皇岛市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (78, '130301', '', '', '市辖区', 77, '', 0, 0);
INSERT INTO `f_com_area` VALUES (79, '130302', '', '', '海港区', 77, '', 0, 0);
INSERT INTO `f_com_area` VALUES (80, '130303', '', '', '山海关区', 77, '', 0, 0);
INSERT INTO `f_com_area` VALUES (81, '130304', '', '', '北戴河区', 77, '', 0, 0);
INSERT INTO `f_com_area` VALUES (82, '130321', '', '', '青龙满族自治县', 77, '', 0, 0);
INSERT INTO `f_com_area` VALUES (83, '130322', '', '', '昌黎县', 77, '', 0, 0);
INSERT INTO `f_com_area` VALUES (84, '130323', '', '', '抚宁县', 77, '', 0, 0);
INSERT INTO `f_com_area` VALUES (85, '130324', '', '', '卢龙县', 77, '', 0, 0);
INSERT INTO `f_com_area` VALUES (86, '130400', '', '邯郸市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (87, '130401', '', '', '市辖区', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (88, '130402', '', '', '邯山区', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (89, '130403', '', '', '丛台区', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (90, '130404', '', '', '复兴区', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (91, '130406', '', '', '峰峰矿区', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (92, '130421', '', '', '邯郸县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (93, '130423', '', '', '临漳县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (94, '130424', '', '', '成安县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (95, '130425', '', '', '大名县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (96, '130426', '', '', '涉县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (97, '130427', '', '', '磁县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (98, '130428', '', '', '肥乡县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (99, '130429', '', '', '永年县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (100, '130430', '', '', '邱县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (101, '130431', '', '', '鸡泽县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (102, '130432', '', '', '广平县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (103, '130433', '', '', '馆陶县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (104, '130434', '', '', '魏县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (105, '130435', '', '', '曲周县', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (106, '130481', '', '', '武安市', 86, '', 0, 0);
INSERT INTO `f_com_area` VALUES (107, '130500', '', '邢台市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (108, '130501', '', '', '市辖区', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (109, '130502', '', '', '桥东区', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (110, '130503', '', '', '桥西区', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (111, '130521', '', '', '邢台县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (112, '130522', '', '', '临城县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (113, '130523', '', '', '内丘县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (114, '130524', '', '', '柏乡县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (115, '130525', '', '', '隆尧县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (116, '130526', '', '', '任县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (117, '130527', '', '', '南和县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (118, '130528', '', '', '宁晋县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (119, '130529', '', '', '巨鹿县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (120, '130530', '', '', '新河县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (121, '130531', '', '', '广宗县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (122, '130532', '', '', '平乡县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (123, '130533', '', '', '威县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (124, '130534', '', '', '清河县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (125, '130535', '', '', '临西县', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (126, '130581', '', '', '南宫市', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (127, '130582', '', '', '沙河市', 107, '', 0, 0);
INSERT INTO `f_com_area` VALUES (128, '130600', '', '保定市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (129, '130601', '', '', '市辖区', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (130, '130602', '', '', '新市区', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (131, '130603', '', '', '北市区', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (132, '130604', '', '', '南市区', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (133, '130621', '', '', '满城县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (134, '130622', '', '', '清苑县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (135, '130623', '', '', '涞水县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (136, '130624', '', '', '阜平县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (137, '130625', '', '', '徐水县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (138, '130626', '', '', '定兴县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (139, '130627', '', '', '唐县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (140, '130628', '', '', '高阳县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (141, '130629', '', '', '容城县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (142, '130630', '', '', '涞源县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (143, '130631', '', '', '望都县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (144, '130632', '', '', '安新县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (145, '130633', '', '', '易县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (146, '130634', '', '', '曲阳县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (147, '130635', '', '', '蠡县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (148, '130636', '', '', '顺平县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (149, '130637', '', '', '博野县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (150, '130638', '', '', '雄县', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (151, '130681', '', '', '涿州市', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (152, '130682', '', '', '定州市', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (153, '130683', '', '', '安国市', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (154, '130684', '', '', '高碑店市', 128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (155, '130700', '', '张家口市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (156, '130701', '', '', '市辖区', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (157, '130702', '', '', '桥东区', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (158, '130703', '', '', '桥西区', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (159, '130705', '', '', '宣化区', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (160, '130706', '', '', '下花园区', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (161, '130721', '', '', '宣化县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (162, '130722', '', '', '张北县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (163, '130723', '', '', '康保县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (164, '130724', '', '', '沽源县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (165, '130725', '', '', '尚义县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (166, '130726', '', '', '蔚县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (167, '130727', '', '', '阳原县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (168, '130728', '', '', '怀安县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (169, '130729', '', '', '万全县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (170, '130730', '', '', '怀来县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (171, '130731', '', '', '涿鹿县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (172, '130732', '', '', '赤城县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (173, '130733', '', '', '崇礼县', 155, '', 0, 0);
INSERT INTO `f_com_area` VALUES (174, '130800', '', '承德市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (175, '130801', '', '', '市辖区', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (176, '130802', '', '', '双桥区', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (177, '130803', '', '', '双滦区', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (178, '130804', '', '', '鹰手营子矿区', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (179, '130821', '', '', '承德县', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (180, '130822', '', '', '兴隆县', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (181, '130823', '', '', '平泉县', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (182, '130824', '', '', '滦平县', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (183, '130825', '', '', '隆化县', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (184, '130826', '', '', '丰宁满族自治县', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (185, '130827', '', '', '宽城满族自治县', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (186, '130828', '', '', '围场满族蒙古族自治县', 174, '', 0, 0);
INSERT INTO `f_com_area` VALUES (187, '130900', '', '沧州市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (188, '130901', '', '', '市辖区', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (189, '130902', '', '', '新华区', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (190, '130903', '', '', '运河区', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (191, '130921', '', '', '沧县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (192, '130922', '', '', '青县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (193, '130923', '', '', '东光县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (194, '130924', '', '', '海兴县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (195, '130925', '', '', '盐山县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (196, '130926', '', '', '肃宁县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (197, '130927', '', '', '南皮县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (198, '130928', '', '', '吴桥县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (199, '130929', '', '', '献县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (200, '130930', '', '', '孟村回族自治县', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (201, '130981', '', '', '泊头市', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (202, '130982', '', '', '任丘市', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (203, '130983', '', '', '黄骅市', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (204, '130984', '', '', '河间市', 187, '', 0, 0);
INSERT INTO `f_com_area` VALUES (205, '131000', '', '廊坊市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (206, '131001', '', '', '市辖区', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (207, '131002', '', '', '安次区', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (208, '131003', '', '', '广阳区', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (209, '131022', '', '', '固安县', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (210, '131023', '', '', '永清县', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (211, '131024', '', '', '香河县', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (212, '131025', '', '', '大城县', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (213, '131026', '', '', '文安县', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (214, '131028', '', '', '大厂回族自治县', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (215, '131081', '', '', '霸州市', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (216, '131082', '', '', '三河市', 205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (217, '131100', '', '衡水市', '', 36, '', 0, 0);
INSERT INTO `f_com_area` VALUES (218, '131101', '', '', '市辖区', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (219, '131102', '', '', '桃城区', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (220, '131121', '', '', '枣强县', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (221, '131122', '', '', '武邑县', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (222, '131123', '', '', '武强县', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (223, '131124', '', '', '饶阳县', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (224, '131125', '', '', '安平县', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (225, '131126', '', '', '故城县', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (226, '131127', '', '', '景县', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (227, '131128', '', '', '阜城县', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (228, '131181', '', '', '冀州市', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (229, '131182', '', '', '深州市', 217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (230, '140000', '山西省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (231, '140100', '', '太原市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (232, '140101', '', '', '市辖区', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (233, '140105', '', '', '小店区', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (234, '140106', '', '', '迎泽区', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (235, '140107', '', '', '杏花岭区', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (236, '140108', '', '', '尖草坪区', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (237, '140109', '', '', '万柏林区', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (238, '140110', '', '', '晋源区', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (239, '140121', '', '', '清徐县', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (240, '140122', '', '', '阳曲县', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (241, '140123', '', '', '娄烦县', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (242, '140181', '', '', '古交市', 231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (243, '140200', '', '大同市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (244, '140201', '', '', '市辖区', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (245, '140202', '', '', '城区', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (246, '140203', '', '', '矿区', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (247, '140211', '', '', '南郊区', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (248, '140212', '', '', '新荣区', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (249, '140221', '', '', '阳高县', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (250, '140222', '', '', '天镇县', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (251, '140223', '', '', '广灵县', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (252, '140224', '', '', '灵丘县', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (253, '140225', '', '', '浑源县', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (254, '140226', '', '', '左云县', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (255, '140227', '', '', '大同县', 243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (256, '140300', '', '阳泉市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (257, '140301', '', '', '市辖区', 256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (258, '140302', '', '', '城区', 256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (259, '140303', '', '', '矿区', 256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (260, '140311', '', '', '郊区', 256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (261, '140321', '', '', '平定县', 256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (262, '140322', '', '', '盂县', 256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (263, '140400', '', '长治市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (264, '140401', '', '', '市辖区', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (265, '140402', '', '', '城区', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (266, '140411', '', '', '郊区', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (267, '140421', '', '', '长治县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (268, '140423', '', '', '襄垣县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (269, '140424', '', '', '屯留县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (270, '140425', '', '', '平顺县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (271, '140426', '', '', '黎城县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (272, '140427', '', '', '壶关县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (273, '140428', '', '', '长子县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (274, '140429', '', '', '武乡县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (275, '140430', '', '', '沁县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (276, '140431', '', '', '沁源县', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (277, '140481', '', '', '潞城市', 263, '', 0, 0);
INSERT INTO `f_com_area` VALUES (278, '140500', '', '晋城市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (279, '140501', '', '', '市辖区', 278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (280, '140502', '', '', '城区', 278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (281, '140521', '', '', '沁水县', 278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (282, '140522', '', '', '阳城县', 278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (283, '140524', '', '', '陵川县', 278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (284, '140525', '', '', '泽州县', 278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (285, '140581', '', '', '高平市', 278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (286, '140600', '', '朔州市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (287, '140601', '', '', '市辖区', 286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (288, '140602', '', '', '朔城区', 286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (289, '140603', '', '', '平鲁区', 286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (290, '140621', '', '', '山阴县', 286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (291, '140622', '', '', '应县', 286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (292, '140623', '', '', '右玉县', 286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (293, '140624', '', '', '怀仁县', 286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (294, '140700', '', '晋中市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (295, '140701', '', '', '市辖区', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (296, '140702', '', '', '榆次区', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (297, '140721', '', '', '榆社县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (298, '140722', '', '', '左权县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (299, '140723', '', '', '和顺县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (300, '140724', '', '', '昔阳县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (301, '140725', '', '', '寿阳县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (302, '140726', '', '', '太谷县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (303, '140727', '', '', '祁县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (304, '140728', '', '', '平遥县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (305, '140729', '', '', '灵石县', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (306, '140781', '', '', '介休市', 294, '', 0, 0);
INSERT INTO `f_com_area` VALUES (307, '140800', '', '运城市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (308, '140801', '', '', '市辖区', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (309, '140802', '', '', '盐湖区', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (310, '140821', '', '', '临猗县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (311, '140822', '', '', '万荣县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (312, '140823', '', '', '闻喜县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (313, '140824', '', '', '稷山县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (314, '140825', '', '', '新绛县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (315, '140826', '', '', '绛县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (316, '140827', '', '', '垣曲县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (317, '140828', '', '', '夏县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (318, '140829', '', '', '平陆县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (319, '140830', '', '', '芮城县', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (320, '140881', '', '', '永济市', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (321, '140882', '', '', '河津市', 307, '', 0, 0);
INSERT INTO `f_com_area` VALUES (322, '140900', '', '忻州市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (323, '140901', '', '', '市辖区', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (324, '140902', '', '', '忻府区', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (325, '140921', '', '', '定襄县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (326, '140922', '', '', '五台县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (327, '140923', '', '', '代县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (328, '140924', '', '', '繁峙县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (329, '140925', '', '', '宁武县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (330, '140926', '', '', '静乐县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (331, '140927', '', '', '神池县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (332, '140928', '', '', '五寨县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (333, '140929', '', '', '岢岚县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (334, '140930', '', '', '河曲县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (335, '140931', '', '', '保德县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (336, '140932', '', '', '偏关县', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (337, '140981', '', '', '原平市', 322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (338, '141000', '', '临汾市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (339, '141001', '', '', '市辖区', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (340, '141002', '', '', '尧都区', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (341, '141021', '', '', '曲沃县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (342, '141022', '', '', '翼城县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (343, '141023', '', '', '襄汾县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (344, '141024', '', '', '洪洞县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (345, '141025', '', '', '古县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (346, '141026', '', '', '安泽县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (347, '141027', '', '', '浮山县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (348, '141028', '', '', '吉县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (349, '141029', '', '', '乡宁县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (350, '141030', '', '', '大宁县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (351, '141031', '', '', '隰县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (352, '141032', '', '', '永和县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (353, '141033', '', '', '蒲县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (354, '141034', '', '', '汾西县', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (355, '141081', '', '', '侯马市', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (356, '141082', '', '', '霍州市', 338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (357, '141100', '', '吕梁市', '', 230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (358, '141101', '', '', '市辖区', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (359, '141102', '', '', '离石区', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (360, '141121', '', '', '文水县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (361, '141122', '', '', '交城县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (362, '141123', '', '', '兴县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (363, '141124', '', '', '临县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (364, '141125', '', '', '柳林县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (365, '141126', '', '', '石楼县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (366, '141127', '', '', '岚县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (367, '141128', '', '', '方山县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (368, '141129', '', '', '中阳县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (369, '141130', '', '', '交口县', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (370, '141181', '', '', '孝义市', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (371, '141182', '', '', '汾阳市', 357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (372, '150000', '内蒙古自治区', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (373, '150100', '', '呼和浩特市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (374, '150101', '', '', '市辖区', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (375, '150102', '', '', '新城区', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (376, '150103', '', '', '回民区', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (377, '150104', '', '', '玉泉区', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (378, '150105', '', '', '赛罕区', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (379, '150121', '', '', '土默特左旗', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (380, '150122', '', '', '托克托县', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (381, '150123', '', '', '和林格尔县', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (382, '150124', '', '', '清水河县', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (383, '150125', '', '', '武川县', 373, '', 0, 0);
INSERT INTO `f_com_area` VALUES (384, '150200', '', '包头市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (385, '150201', '', '', '市辖区', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (386, '150202', '', '', '东河区', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (387, '150203', '', '', '昆都仑区', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (388, '150204', '', '', '青山区', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (389, '150205', '', '', '石拐区', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (390, '150206', '', '', '白云鄂博矿区', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (391, '150207', '', '', '九原区', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (392, '150221', '', '', '土默特右旗', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (393, '150222', '', '', '固阳县', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (394, '150223', '', '', '达尔罕茂明安联合旗', 384, '', 0, 0);
INSERT INTO `f_com_area` VALUES (395, '150300', '', '乌海市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (396, '150301', '', '', '市辖区', 395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (397, '150302', '', '', '海勃湾区', 395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (398, '150303', '', '', '海南区', 395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (399, '150304', '', '', '乌达区', 395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (400, '150400', '', '赤峰市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (401, '150401', '', '', '市辖区', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (402, '150402', '', '', '红山区', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (403, '150403', '', '', '元宝山区', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (404, '150404', '', '', '松山区', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (405, '150421', '', '', '阿鲁科尔沁旗', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (406, '150422', '', '', '巴林左旗', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (407, '150423', '', '', '巴林右旗', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (408, '150424', '', '', '林西县', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (409, '150425', '', '', '克什克腾旗', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (410, '150426', '', '', '翁牛特旗', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (411, '150428', '', '', '喀喇沁旗', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (412, '150429', '', '', '宁城县', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (413, '150430', '', '', '敖汉旗', 400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (414, '150500', '', '通辽市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (415, '150501', '', '', '市辖区', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (416, '150502', '', '', '科尔沁区', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (417, '150521', '', '', '科尔沁左翼中旗', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (418, '150522', '', '', '科尔沁左翼后旗', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (419, '150523', '', '', '开鲁县', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (420, '150524', '', '', '库伦旗', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (421, '150525', '', '', '奈曼旗', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (422, '150526', '', '', '扎鲁特旗', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (423, '150581', '', '', '霍林郭勒市', 414, '', 0, 0);
INSERT INTO `f_com_area` VALUES (424, '150600', '', '鄂尔多斯市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (425, '150601', '', '', '市辖区', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (426, '150602', '', '', '东胜区', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (427, '150621', '', '', '达拉特旗', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (428, '150622', '', '', '准格尔旗', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (429, '150623', '', '', '鄂托克前旗', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (430, '150624', '', '', '鄂托克旗', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (431, '150625', '', '', '杭锦旗', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (432, '150626', '', '', '乌审旗', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (433, '150627', '', '', '伊金霍洛旗', 424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (434, '150700', '', '呼伦贝尔市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (435, '150701', '', '', '市辖区', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (436, '150702', '', '', '海拉尔区', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (437, '150703', '', '', '扎赉诺尔区', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (438, '150721', '', '', '阿荣旗', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (439, '150722', '', '', '莫力达瓦达斡尔族自治旗', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (440, '150723', '', '', '鄂伦春自治旗', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (441, '150724', '', '', '鄂温克族自治旗', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (442, '150725', '', '', '陈巴尔虎旗', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (443, '150726', '', '', '新巴尔虎左旗', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (444, '150727', '', '', '新巴尔虎右旗', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (445, '150781', '', '', '满洲里市', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (446, '150782', '', '', '牙克石市', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (447, '150783', '', '', '扎兰屯市', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (448, '150784', '', '', '额尔古纳市', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (449, '150785', '', '', '根河市', 434, '', 0, 0);
INSERT INTO `f_com_area` VALUES (450, '150800', '', '巴彦淖尔市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (451, '150801', '', '', '市辖区', 450, '', 0, 0);
INSERT INTO `f_com_area` VALUES (452, '150802', '', '', '临河区', 450, '', 0, 0);
INSERT INTO `f_com_area` VALUES (453, '150821', '', '', '五原县', 450, '', 0, 0);
INSERT INTO `f_com_area` VALUES (454, '150822', '', '', '磴口县', 450, '', 0, 0);
INSERT INTO `f_com_area` VALUES (455, '150823', '', '', '乌拉特前旗', 450, '', 0, 0);
INSERT INTO `f_com_area` VALUES (456, '150824', '', '', '乌拉特中旗', 450, '', 0, 0);
INSERT INTO `f_com_area` VALUES (457, '150825', '', '', '乌拉特后旗', 450, '', 0, 0);
INSERT INTO `f_com_area` VALUES (458, '150826', '', '', '杭锦后旗', 450, '', 0, 0);
INSERT INTO `f_com_area` VALUES (459, '150900', '', '乌兰察布市', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (460, '150901', '', '', '市辖区', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (461, '150902', '', '', '集宁区', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (462, '150921', '', '', '卓资县', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (463, '150922', '', '', '化德县', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (464, '150923', '', '', '商都县', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (465, '150924', '', '', '兴和县', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (466, '150925', '', '', '凉城县', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (467, '150926', '', '', '察哈尔右翼前旗', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (468, '150927', '', '', '察哈尔右翼中旗', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (469, '150928', '', '', '察哈尔右翼后旗', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (470, '150929', '', '', '四子王旗', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (471, '150981', '', '', '丰镇市', 459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (472, '152200', '', '兴安盟', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (473, '152201', '', '', '乌兰浩特市', 472, '', 0, 0);
INSERT INTO `f_com_area` VALUES (474, '152202', '', '', '阿尔山市', 472, '', 0, 0);
INSERT INTO `f_com_area` VALUES (475, '152221', '', '', '科尔沁右翼前旗', 472, '', 0, 0);
INSERT INTO `f_com_area` VALUES (476, '152222', '', '', '科尔沁右翼中旗', 472, '', 0, 0);
INSERT INTO `f_com_area` VALUES (477, '152223', '', '', '扎赉特旗', 472, '', 0, 0);
INSERT INTO `f_com_area` VALUES (478, '152224', '', '', '突泉县', 472, '', 0, 0);
INSERT INTO `f_com_area` VALUES (479, '152500', '', '锡林郭勒盟', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (480, '152501', '', '', '二连浩特市', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (481, '152502', '', '', '锡林浩特市', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (482, '152522', '', '', '阿巴嘎旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (483, '152523', '', '', '苏尼特左旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (484, '152524', '', '', '苏尼特右旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (485, '152525', '', '', '东乌珠穆沁旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (486, '152526', '', '', '西乌珠穆沁旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (487, '152527', '', '', '太仆寺旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (488, '152528', '', '', '镶黄旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (489, '152529', '', '', '正镶白旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (490, '152530', '', '', '正蓝旗', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (491, '152531', '', '', '多伦县', 479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (492, '152900', '', '阿拉善盟', '', 372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (493, '152921', '', '', '阿拉善左旗', 492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (494, '152922', '', '', '阿拉善右旗', 492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (495, '152923', '', '', '额济纳旗', 492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (496, '210000', '辽宁省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (497, '210100', '', '沈阳市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (498, '210101', '', '', '市辖区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (499, '210102', '', '', '和平区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (500, '210103', '', '', '沈河区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (501, '210104', '', '', '大东区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (502, '210105', '', '', '皇姑区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (503, '210106', '', '', '铁西区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (504, '210111', '', '', '苏家屯区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (505, '210112', '', '', '浑南区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (506, '210113', '', '', '沈北新区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (507, '210114', '', '', '于洪区', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (508, '210122', '', '', '辽中县', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (509, '210123', '', '', '康平县', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (510, '210124', '', '', '法库县', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (511, '210181', '', '', '新民市', 497, '', 0, 0);
INSERT INTO `f_com_area` VALUES (512, '210200', '', '大连市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (513, '210201', '', '', '市辖区', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (514, '210202', '', '', '中山区', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (515, '210203', '', '', '西岗区', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (516, '210204', '', '', '沙河口区', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (517, '210211', '', '', '甘井子区', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (518, '210212', '', '', '旅顺口区', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (519, '210213', '', '', '金州区', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (520, '210224', '', '', '长海县', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (521, '210281', '', '', '瓦房店市', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (522, '210282', '', '', '普兰店市', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (523, '210283', '', '', '庄河市', 512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (524, '210300', '', '鞍山市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (525, '210301', '', '', '市辖区', 524, '', 0, 0);
INSERT INTO `f_com_area` VALUES (526, '210302', '', '', '铁东区', 524, '', 0, 0);
INSERT INTO `f_com_area` VALUES (527, '210303', '', '', '铁西区', 524, '', 0, 0);
INSERT INTO `f_com_area` VALUES (528, '210304', '', '', '立山区', 524, '', 0, 0);
INSERT INTO `f_com_area` VALUES (529, '210311', '', '', '千山区', 524, '', 0, 0);
INSERT INTO `f_com_area` VALUES (530, '210321', '', '', '台安县', 524, '', 0, 0);
INSERT INTO `f_com_area` VALUES (531, '210323', '', '', '岫岩满族自治县', 524, '', 0, 0);
INSERT INTO `f_com_area` VALUES (532, '210381', '', '', '海城市', 524, '', 0, 0);
INSERT INTO `f_com_area` VALUES (533, '210400', '', '抚顺市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (534, '210401', '', '', '市辖区', 533, '', 0, 0);
INSERT INTO `f_com_area` VALUES (535, '210402', '', '', '新抚区', 533, '', 0, 0);
INSERT INTO `f_com_area` VALUES (536, '210403', '', '', '东洲区', 533, '', 0, 0);
INSERT INTO `f_com_area` VALUES (537, '210404', '', '', '望花区', 533, '', 0, 0);
INSERT INTO `f_com_area` VALUES (538, '210411', '', '', '顺城区', 533, '', 0, 0);
INSERT INTO `f_com_area` VALUES (539, '210421', '', '', '抚顺县', 533, '', 0, 0);
INSERT INTO `f_com_area` VALUES (540, '210422', '', '', '新宾满族自治县', 533, '', 0, 0);
INSERT INTO `f_com_area` VALUES (541, '210423', '', '', '清原满族自治县', 533, '', 0, 0);
INSERT INTO `f_com_area` VALUES (542, '210500', '', '本溪市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (543, '210501', '', '', '市辖区', 542, '', 0, 0);
INSERT INTO `f_com_area` VALUES (544, '210502', '', '', '平山区', 542, '', 0, 0);
INSERT INTO `f_com_area` VALUES (545, '210503', '', '', '溪湖区', 542, '', 0, 0);
INSERT INTO `f_com_area` VALUES (546, '210504', '', '', '明山区', 542, '', 0, 0);
INSERT INTO `f_com_area` VALUES (547, '210505', '', '', '南芬区', 542, '', 0, 0);
INSERT INTO `f_com_area` VALUES (548, '210521', '', '', '本溪满族自治县', 542, '', 0, 0);
INSERT INTO `f_com_area` VALUES (549, '210522', '', '', '桓仁满族自治县', 542, '', 0, 0);
INSERT INTO `f_com_area` VALUES (550, '210600', '', '丹东市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (551, '210601', '', '', '市辖区', 550, '', 0, 0);
INSERT INTO `f_com_area` VALUES (552, '210602', '', '', '元宝区', 550, '', 0, 0);
INSERT INTO `f_com_area` VALUES (553, '210603', '', '', '振兴区', 550, '', 0, 0);
INSERT INTO `f_com_area` VALUES (554, '210604', '', '', '振安区', 550, '', 0, 0);
INSERT INTO `f_com_area` VALUES (555, '210624', '', '', '宽甸满族自治县', 550, '', 0, 0);
INSERT INTO `f_com_area` VALUES (556, '210681', '', '', '东港市', 550, '', 0, 0);
INSERT INTO `f_com_area` VALUES (557, '210682', '', '', '凤城市', 550, '', 0, 0);
INSERT INTO `f_com_area` VALUES (558, '210700', '', '锦州市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (559, '210701', '', '', '市辖区', 558, '', 0, 0);
INSERT INTO `f_com_area` VALUES (560, '210702', '', '', '古塔区', 558, '', 0, 0);
INSERT INTO `f_com_area` VALUES (561, '210703', '', '', '凌河区', 558, '', 0, 0);
INSERT INTO `f_com_area` VALUES (562, '210711', '', '', '太和区', 558, '', 0, 0);
INSERT INTO `f_com_area` VALUES (563, '210726', '', '', '黑山县', 558, '', 0, 0);
INSERT INTO `f_com_area` VALUES (564, '210727', '', '', '义县', 558, '', 0, 0);
INSERT INTO `f_com_area` VALUES (565, '210781', '', '', '凌海市', 558, '', 0, 0);
INSERT INTO `f_com_area` VALUES (566, '210782', '', '', '北镇市', 558, '', 0, 0);
INSERT INTO `f_com_area` VALUES (567, '210800', '', '营口市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (568, '210801', '', '', '市辖区', 567, '', 0, 0);
INSERT INTO `f_com_area` VALUES (569, '210802', '', '', '站前区', 567, '', 0, 0);
INSERT INTO `f_com_area` VALUES (570, '210803', '', '', '西市区', 567, '', 0, 0);
INSERT INTO `f_com_area` VALUES (571, '210804', '', '', '鲅鱼圈区', 567, '', 0, 0);
INSERT INTO `f_com_area` VALUES (572, '210811', '', '', '老边区', 567, '', 0, 0);
INSERT INTO `f_com_area` VALUES (573, '210881', '', '', '盖州市', 567, '', 0, 0);
INSERT INTO `f_com_area` VALUES (574, '210882', '', '', '大石桥市', 567, '', 0, 0);
INSERT INTO `f_com_area` VALUES (575, '210900', '', '阜新市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (576, '210901', '', '', '市辖区', 575, '', 0, 0);
INSERT INTO `f_com_area` VALUES (577, '210902', '', '', '海州区', 575, '', 0, 0);
INSERT INTO `f_com_area` VALUES (578, '210903', '', '', '新邱区', 575, '', 0, 0);
INSERT INTO `f_com_area` VALUES (579, '210904', '', '', '太平区', 575, '', 0, 0);
INSERT INTO `f_com_area` VALUES (580, '210905', '', '', '清河门区', 575, '', 0, 0);
INSERT INTO `f_com_area` VALUES (581, '210911', '', '', '细河区', 575, '', 0, 0);
INSERT INTO `f_com_area` VALUES (582, '210921', '', '', '阜新蒙古族自治县', 575, '', 0, 0);
INSERT INTO `f_com_area` VALUES (583, '210922', '', '', '彰武县', 575, '', 0, 0);
INSERT INTO `f_com_area` VALUES (584, '211000', '', '辽阳市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (585, '211001', '', '', '市辖区', 584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (586, '211002', '', '', '白塔区', 584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (587, '211003', '', '', '文圣区', 584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (588, '211004', '', '', '宏伟区', 584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (589, '211005', '', '', '弓长岭区', 584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (590, '211011', '', '', '太子河区', 584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (591, '211021', '', '', '辽阳县', 584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (592, '211081', '', '', '灯塔市', 584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (593, '211100', '', '盘锦市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (594, '211101', '', '', '市辖区', 593, '', 0, 0);
INSERT INTO `f_com_area` VALUES (595, '211102', '', '', '双台子区', 593, '', 0, 0);
INSERT INTO `f_com_area` VALUES (596, '211103', '', '', '兴隆台区', 593, '', 0, 0);
INSERT INTO `f_com_area` VALUES (597, '211121', '', '', '大洼县', 593, '', 0, 0);
INSERT INTO `f_com_area` VALUES (598, '211122', '', '', '盘山县', 593, '', 0, 0);
INSERT INTO `f_com_area` VALUES (599, '211200', '', '铁岭市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (600, '211201', '', '', '市辖区', 599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (601, '211202', '', '', '银州区', 599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (602, '211204', '', '', '清河区', 599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (603, '211221', '', '', '铁岭县', 599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (604, '211223', '', '', '西丰县', 599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (605, '211224', '', '', '昌图县', 599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (606, '211281', '', '', '调兵山市', 599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (607, '211282', '', '', '开原市', 599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (608, '211300', '', '朝阳市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (609, '211301', '', '', '市辖区', 608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (610, '211302', '', '', '双塔区', 608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (611, '211303', '', '', '龙城区', 608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (612, '211321', '', '', '朝阳县', 608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (613, '211322', '', '', '建平县', 608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (614, '211324', '', '', '喀喇沁左翼蒙古族自治县', 608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (615, '211381', '', '', '北票市', 608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (616, '211382', '', '', '凌源市', 608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (617, '211400', '', '葫芦岛市', '', 496, '', 0, 0);
INSERT INTO `f_com_area` VALUES (618, '211401', '', '', '市辖区', 617, '', 0, 0);
INSERT INTO `f_com_area` VALUES (619, '211402', '', '', '连山区', 617, '', 0, 0);
INSERT INTO `f_com_area` VALUES (620, '211403', '', '', '龙港区', 617, '', 0, 0);
INSERT INTO `f_com_area` VALUES (621, '211404', '', '', '南票区', 617, '', 0, 0);
INSERT INTO `f_com_area` VALUES (622, '211421', '', '', '绥中县', 617, '', 0, 0);
INSERT INTO `f_com_area` VALUES (623, '211422', '', '', '建昌县', 617, '', 0, 0);
INSERT INTO `f_com_area` VALUES (624, '211481', '', '', '兴城市', 617, '', 0, 0);
INSERT INTO `f_com_area` VALUES (625, '220000', '吉林省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (626, '220100', '', '长春市', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (627, '220101', '', '', '市辖区', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (628, '220102', '', '', '南关区', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (629, '220103', '', '', '宽城区', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (630, '220104', '', '', '朝阳区', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (631, '220105', '', '', '二道区', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (632, '220106', '', '', '绿园区', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (633, '220112', '', '', '双阳区', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (634, '220113', '', '', '九台区', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (635, '220122', '', '', '农安县', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (636, '220182', '', '', '榆树市', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (637, '220183', '', '', '德惠市', 626, '', 0, 0);
INSERT INTO `f_com_area` VALUES (638, '220200', '', '吉林市', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (639, '220201', '', '', '市辖区', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (640, '220202', '', '', '昌邑区', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (641, '220203', '', '', '龙潭区', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (642, '220204', '', '', '船营区', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (643, '220211', '', '', '丰满区', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (644, '220221', '', '', '永吉县', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (645, '220281', '', '', '蛟河市', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (646, '220282', '', '', '桦甸市', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (647, '220283', '', '', '舒兰市', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (648, '220284', '', '', '磐石市', 638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (649, '220300', '', '四平市', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (650, '220301', '', '', '市辖区', 649, '', 0, 0);
INSERT INTO `f_com_area` VALUES (651, '220302', '', '', '铁西区', 649, '', 0, 0);
INSERT INTO `f_com_area` VALUES (652, '220303', '', '', '铁东区', 649, '', 0, 0);
INSERT INTO `f_com_area` VALUES (653, '220322', '', '', '梨树县', 649, '', 0, 0);
INSERT INTO `f_com_area` VALUES (654, '220323', '', '', '伊通满族自治县', 649, '', 0, 0);
INSERT INTO `f_com_area` VALUES (655, '220381', '', '', '公主岭市', 649, '', 0, 0);
INSERT INTO `f_com_area` VALUES (656, '220382', '', '', '双辽市', 649, '', 0, 0);
INSERT INTO `f_com_area` VALUES (657, '220400', '', '辽源市', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (658, '220401', '', '', '市辖区', 657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (659, '220402', '', '', '龙山区', 657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (660, '220403', '', '', '西安区', 657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (661, '220421', '', '', '东丰县', 657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (662, '220422', '', '', '东辽县', 657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (663, '220500', '', '通化市', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (664, '220501', '', '', '市辖区', 663, '', 0, 0);
INSERT INTO `f_com_area` VALUES (665, '220502', '', '', '东昌区', 663, '', 0, 0);
INSERT INTO `f_com_area` VALUES (666, '220503', '', '', '二道江区', 663, '', 0, 0);
INSERT INTO `f_com_area` VALUES (667, '220521', '', '', '通化县', 663, '', 0, 0);
INSERT INTO `f_com_area` VALUES (668, '220523', '', '', '辉南县', 663, '', 0, 0);
INSERT INTO `f_com_area` VALUES (669, '220524', '', '', '柳河县', 663, '', 0, 0);
INSERT INTO `f_com_area` VALUES (670, '220581', '', '', '梅河口市', 663, '', 0, 0);
INSERT INTO `f_com_area` VALUES (671, '220582', '', '', '集安市', 663, '', 0, 0);
INSERT INTO `f_com_area` VALUES (672, '220600', '', '白山市', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (673, '220601', '', '', '市辖区', 672, '', 0, 0);
INSERT INTO `f_com_area` VALUES (674, '220602', '', '', '浑江区', 672, '', 0, 0);
INSERT INTO `f_com_area` VALUES (675, '220605', '', '', '江源区', 672, '', 0, 0);
INSERT INTO `f_com_area` VALUES (676, '220621', '', '', '抚松县', 672, '', 0, 0);
INSERT INTO `f_com_area` VALUES (677, '220622', '', '', '靖宇县', 672, '', 0, 0);
INSERT INTO `f_com_area` VALUES (678, '220623', '', '', '长白朝鲜族自治县', 672, '', 0, 0);
INSERT INTO `f_com_area` VALUES (679, '220681', '', '', '临江市', 672, '', 0, 0);
INSERT INTO `f_com_area` VALUES (680, '220700', '', '松原市', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (681, '220701', '', '', '市辖区', 680, '', 0, 0);
INSERT INTO `f_com_area` VALUES (682, '220702', '', '', '宁江区', 680, '', 0, 0);
INSERT INTO `f_com_area` VALUES (683, '220721', '', '', '前郭尔罗斯蒙古族自治县', 680, '', 0, 0);
INSERT INTO `f_com_area` VALUES (684, '220722', '', '', '长岭县', 680, '', 0, 0);
INSERT INTO `f_com_area` VALUES (685, '220723', '', '', '乾安县', 680, '', 0, 0);
INSERT INTO `f_com_area` VALUES (686, '220781', '', '', '扶余市', 680, '', 0, 0);
INSERT INTO `f_com_area` VALUES (687, '220800', '', '白城市', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (688, '220801', '', '', '市辖区', 687, '', 0, 0);
INSERT INTO `f_com_area` VALUES (689, '220802', '', '', '洮北区', 687, '', 0, 0);
INSERT INTO `f_com_area` VALUES (690, '220821', '', '', '镇赉县', 687, '', 0, 0);
INSERT INTO `f_com_area` VALUES (691, '220822', '', '', '通榆县', 687, '', 0, 0);
INSERT INTO `f_com_area` VALUES (692, '220881', '', '', '洮南市', 687, '', 0, 0);
INSERT INTO `f_com_area` VALUES (693, '220882', '', '', '大安市', 687, '', 0, 0);
INSERT INTO `f_com_area` VALUES (694, '222400', '', '延边朝鲜族自治州', '', 625, '', 0, 0);
INSERT INTO `f_com_area` VALUES (695, '222401', '', '', '延吉市', 694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (696, '222402', '', '', '图们市', 694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (697, '222403', '', '', '敦化市', 694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (698, '222404', '', '', '珲春市', 694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (699, '222405', '', '', '龙井市', 694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (700, '222406', '', '', '和龙市', 694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (701, '222424', '', '', '汪清县', 694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (702, '222426', '', '', '安图县', 694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (703, '230000', '黑龙江省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (704, '230100', '', '哈尔滨市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (705, '230101', '', '', '市辖区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (706, '230102', '', '', '道里区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (707, '230103', '', '', '南岗区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (708, '230104', '', '', '道外区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (709, '230108', '', '', '平房区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (710, '230109', '', '', '松北区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (711, '230110', '', '', '香坊区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (712, '230111', '', '', '呼兰区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (713, '230112', '', '', '阿城区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (714, '230113', '', '', '双城区', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (715, '230123', '', '', '依兰县', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (716, '230124', '', '', '方正县', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (717, '230125', '', '', '宾县', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (718, '230126', '', '', '巴彦县', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (719, '230127', '', '', '木兰县', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (720, '230128', '', '', '通河县', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (721, '230129', '', '', '延寿县', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (722, '230183', '', '', '尚志市', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (723, '230184', '', '', '五常市', 704, '', 0, 0);
INSERT INTO `f_com_area` VALUES (724, '230200', '', '齐齐哈尔市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (725, '230201', '', '', '市辖区', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (726, '230202', '', '', '龙沙区', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (727, '230203', '', '', '建华区', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (728, '230204', '', '', '铁锋区', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (729, '230205', '', '', '昂昂溪区', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (730, '230206', '', '', '富拉尔基区', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (731, '230207', '', '', '碾子山区', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (732, '230208', '', '', '梅里斯达斡尔族区', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (733, '230221', '', '', '龙江县', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (734, '230223', '', '', '依安县', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (735, '230224', '', '', '泰来县', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (736, '230225', '', '', '甘南县', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (737, '230227', '', '', '富裕县', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (738, '230229', '', '', '克山县', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (739, '230230', '', '', '克东县', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (740, '230231', '', '', '拜泉县', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (741, '230281', '', '', '讷河市', 724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (742, '230300', '', '鸡西市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (743, '230301', '', '', '市辖区', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (744, '230302', '', '', '鸡冠区', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (745, '230303', '', '', '恒山区', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (746, '230304', '', '', '滴道区', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (747, '230305', '', '', '梨树区', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (748, '230306', '', '', '城子河区', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (749, '230307', '', '', '麻山区', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (750, '230321', '', '', '鸡东县', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (751, '230381', '', '', '虎林市', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (752, '230382', '', '', '密山市', 742, '', 0, 0);
INSERT INTO `f_com_area` VALUES (753, '230400', '', '鹤岗市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (754, '230401', '', '', '市辖区', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (755, '230402', '', '', '向阳区', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (756, '230403', '', '', '工农区', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (757, '230404', '', '', '南山区', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (758, '230405', '', '', '兴安区', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (759, '230406', '', '', '东山区', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (760, '230407', '', '', '兴山区', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (761, '230421', '', '', '萝北县', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (762, '230422', '', '', '绥滨县', 753, '', 0, 0);
INSERT INTO `f_com_area` VALUES (763, '230500', '', '双鸭山市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (764, '230501', '', '', '市辖区', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (765, '230502', '', '', '尖山区', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (766, '230503', '', '', '岭东区', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (767, '230505', '', '', '四方台区', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (768, '230506', '', '', '宝山区', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (769, '230521', '', '', '集贤县', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (770, '230522', '', '', '友谊县', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (771, '230523', '', '', '宝清县', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (772, '230524', '', '', '饶河县', 763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (773, '230600', '', '大庆市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (774, '230601', '', '', '市辖区', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (775, '230602', '', '', '萨尔图区', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (776, '230603', '', '', '龙凤区', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (777, '230604', '', '', '让胡路区', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (778, '230605', '', '', '红岗区', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (779, '230606', '', '', '大同区', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (780, '230621', '', '', '肇州县', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (781, '230622', '', '', '肇源县', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (782, '230623', '', '', '林甸县', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (783, '230624', '', '', '杜尔伯特蒙古族自治县', 773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (784, '230700', '', '伊春市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (785, '230701', '', '', '市辖区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (786, '230702', '', '', '伊春区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (787, '230703', '', '', '南岔区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (788, '230704', '', '', '友好区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (789, '230705', '', '', '西林区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (790, '230706', '', '', '翠峦区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (791, '230707', '', '', '新青区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (792, '230708', '', '', '美溪区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (793, '230709', '', '', '金山屯区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (794, '230710', '', '', '五营区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (795, '230711', '', '', '乌马河区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (796, '230712', '', '', '汤旺河区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (797, '230713', '', '', '带岭区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (798, '230714', '', '', '乌伊岭区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (799, '230715', '', '', '红星区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (800, '230716', '', '', '上甘岭区', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (801, '230722', '', '', '嘉荫县', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (802, '230781', '', '', '铁力市', 784, '', 0, 0);
INSERT INTO `f_com_area` VALUES (803, '230800', '', '佳木斯市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (804, '230801', '', '', '市辖区', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (805, '230803', '', '', '向阳区', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (806, '230804', '', '', '前进区', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (807, '230805', '', '', '东风区', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (808, '230811', '', '', '郊区', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (809, '230822', '', '', '桦南县', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (810, '230826', '', '', '桦川县', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (811, '230828', '', '', '汤原县', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (812, '230833', '', '', '抚远县', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (813, '230881', '', '', '同江市', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (814, '230882', '', '', '富锦市', 803, '', 0, 0);
INSERT INTO `f_com_area` VALUES (815, '230900', '', '七台河市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (816, '230901', '', '', '市辖区', 815, '', 0, 0);
INSERT INTO `f_com_area` VALUES (817, '230902', '', '', '新兴区', 815, '', 0, 0);
INSERT INTO `f_com_area` VALUES (818, '230903', '', '', '桃山区', 815, '', 0, 0);
INSERT INTO `f_com_area` VALUES (819, '230904', '', '', '茄子河区', 815, '', 0, 0);
INSERT INTO `f_com_area` VALUES (820, '230921', '', '', '勃利县', 815, '', 0, 0);
INSERT INTO `f_com_area` VALUES (821, '231000', '', '牡丹江市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (822, '231001', '', '', '市辖区', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (823, '231002', '', '', '东安区', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (824, '231003', '', '', '阳明区', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (825, '231004', '', '', '爱民区', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (826, '231005', '', '', '西安区', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (827, '231024', '', '', '东宁县', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (828, '231025', '', '', '林口县', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (829, '231081', '', '', '绥芬河市', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (830, '231083', '', '', '海林市', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (831, '231084', '', '', '宁安市', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (832, '231085', '', '', '穆棱市', 821, '', 0, 0);
INSERT INTO `f_com_area` VALUES (833, '231100', '', '黑河市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (834, '231101', '', '', '市辖区', 833, '', 0, 0);
INSERT INTO `f_com_area` VALUES (835, '231102', '', '', '爱辉区', 833, '', 0, 0);
INSERT INTO `f_com_area` VALUES (836, '231121', '', '', '嫩江县', 833, '', 0, 0);
INSERT INTO `f_com_area` VALUES (837, '231123', '', '', '逊克县', 833, '', 0, 0);
INSERT INTO `f_com_area` VALUES (838, '231124', '', '', '孙吴县', 833, '', 0, 0);
INSERT INTO `f_com_area` VALUES (839, '231181', '', '', '北安市', 833, '', 0, 0);
INSERT INTO `f_com_area` VALUES (840, '231182', '', '', '五大连池市', 833, '', 0, 0);
INSERT INTO `f_com_area` VALUES (841, '231200', '', '绥化市', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (842, '231201', '', '', '市辖区', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (843, '231202', '', '', '北林区', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (844, '231221', '', '', '望奎县', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (845, '231222', '', '', '兰西县', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (846, '231223', '', '', '青冈县', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (847, '231224', '', '', '庆安县', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (848, '231225', '', '', '明水县', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (849, '231226', '', '', '绥棱县', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (850, '231281', '', '', '安达市', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (851, '231282', '', '', '肇东市', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (852, '231283', '', '', '海伦市', 841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (853, '232700', '', '大兴安岭地区', '', 703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (854, '232721', '', '', '呼玛县', 853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (855, '232722', '', '', '塔河县', 853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (856, '232723', '', '', '漠河县', 853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (857, '310000', '', '上海市', '', 2, '', 0, 0);
INSERT INTO `f_com_area` VALUES (858, '310101', '', '', '黄浦区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (859, '310104', '', '', '徐汇区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (860, '310105', '', '', '长宁区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (861, '310106', '', '', '静安区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (862, '310107', '', '', '普陀区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (863, '310108', '', '', '闸北区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (864, '310109', '', '', '虹口区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (865, '310110', '', '', '杨浦区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (866, '310112', '', '', '闵行区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (867, '310113', '', '', '宝山区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (868, '310114', '', '', '嘉定区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (869, '310115', '', '', '浦东新区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (870, '310116', '', '', '金山区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (871, '310117', '', '', '松江区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (872, '310118', '', '', '青浦区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (873, '310120', '', '', '奉贤区', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (874, '310230', '', '', '崇明县', 857, '', 0, 0);
INSERT INTO `f_com_area` VALUES (875, '320000', '江苏省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (876, '320100', '', '南京市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (877, '320101', '', '', '市辖区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (878, '320102', '', '', '玄武区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (879, '320104', '', '', '秦淮区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (880, '320105', '', '', '建邺区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (881, '320106', '', '', '鼓楼区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (882, '320111', '', '', '浦口区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (883, '320113', '', '', '栖霞区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (884, '320114', '', '', '雨花台区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (885, '320115', '', '', '江宁区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (886, '320116', '', '', '六合区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (887, '320117', '', '', '溧水区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (888, '320118', '', '', '高淳区', 876, '', 0, 0);
INSERT INTO `f_com_area` VALUES (889, '320200', '', '无锡市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (890, '320201', '', '', '市辖区', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (891, '320202', '', '', '崇安区', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (892, '320203', '', '', '南长区', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (893, '320204', '', '', '北塘区', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (894, '320205', '', '', '锡山区', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (895, '320206', '', '', '惠山区', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (896, '320211', '', '', '滨湖区', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (897, '320281', '', '', '江阴市', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (898, '320282', '', '', '宜兴市', 889, '', 0, 0);
INSERT INTO `f_com_area` VALUES (899, '320300', '', '徐州市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (900, '320301', '', '', '市辖区', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (901, '320302', '', '', '鼓楼区', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (902, '320303', '', '', '云龙区', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (903, '320305', '', '', '贾汪区', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (904, '320311', '', '', '泉山区', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (905, '320312', '', '', '铜山区', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (906, '320321', '', '', '丰县', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (907, '320322', '', '', '沛县', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (908, '320324', '', '', '睢宁县', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (909, '320381', '', '', '新沂市', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (910, '320382', '', '', '邳州市', 899, '', 0, 0);
INSERT INTO `f_com_area` VALUES (911, '320400', '', '常州市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (912, '320401', '', '', '市辖区', 911, '', 0, 0);
INSERT INTO `f_com_area` VALUES (913, '320402', '', '', '天宁区', 911, '', 0, 0);
INSERT INTO `f_com_area` VALUES (914, '320404', '', '', '钟楼区', 911, '', 0, 0);
INSERT INTO `f_com_area` VALUES (915, '320405', '', '', '戚墅堰区', 911, '', 0, 0);
INSERT INTO `f_com_area` VALUES (916, '320411', '', '', '新北区', 911, '', 0, 0);
INSERT INTO `f_com_area` VALUES (917, '320412', '', '', '武进区', 911, '', 0, 0);
INSERT INTO `f_com_area` VALUES (918, '320481', '', '', '溧阳市', 911, '', 0, 0);
INSERT INTO `f_com_area` VALUES (919, '320482', '', '', '金坛市', 911, '', 0, 0);
INSERT INTO `f_com_area` VALUES (920, '320500', '', '苏州市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (921, '320501', '', '', '市辖区', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (922, '320505', '', '', '虎丘区', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (923, '320506', '', '', '吴中区', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (924, '320507', '', '', '相城区', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (925, '320508', '', '', '姑苏区', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (926, '320509', '', '', '吴江区', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (927, '320581', '', '', '常熟市', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (928, '320582', '', '', '张家港市', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (929, '320583', '', '', '昆山市', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (930, '320585', '', '', '太仓市', 920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (931, '320600', '', '南通市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (932, '320601', '', '', '市辖区', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (933, '320602', '', '', '崇川区', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (934, '320611', '', '', '港闸区', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (935, '320612', '', '', '通州区', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (936, '320621', '', '', '海安县', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (937, '320623', '', '', '如东县', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (938, '320681', '', '', '启东市', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (939, '320682', '', '', '如皋市', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (940, '320684', '', '', '海门市', 931, '', 0, 0);
INSERT INTO `f_com_area` VALUES (941, '320700', '', '连云港市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (942, '320701', '', '', '市辖区', 941, '', 0, 0);
INSERT INTO `f_com_area` VALUES (943, '320703', '', '', '连云区', 941, '', 0, 0);
INSERT INTO `f_com_area` VALUES (944, '320706', '', '', '海州区', 941, '', 0, 0);
INSERT INTO `f_com_area` VALUES (945, '320707', '', '', '赣榆区', 941, '', 0, 0);
INSERT INTO `f_com_area` VALUES (946, '320722', '', '', '东海县', 941, '', 0, 0);
INSERT INTO `f_com_area` VALUES (947, '320723', '', '', '灌云县', 941, '', 0, 0);
INSERT INTO `f_com_area` VALUES (948, '320724', '', '', '灌南县', 941, '', 0, 0);
INSERT INTO `f_com_area` VALUES (949, '320800', '', '淮安市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (950, '320801', '', '', '市辖区', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (951, '320802', '', '', '清河区', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (952, '320803', '', '', '淮安区', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (953, '320804', '', '', '淮阴区', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (954, '320811', '', '', '清浦区', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (955, '320826', '', '', '涟水县', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (956, '320829', '', '', '洪泽县', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (957, '320830', '', '', '盱眙县', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (958, '320831', '', '', '金湖县', 949, '', 0, 0);
INSERT INTO `f_com_area` VALUES (959, '320900', '', '盐城市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (960, '320901', '', '', '市辖区', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (961, '320902', '', '', '亭湖区', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (962, '320903', '', '', '盐都区', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (963, '320921', '', '', '响水县', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (964, '320922', '', '', '滨海县', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (965, '320923', '', '', '阜宁县', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (966, '320924', '', '', '射阳县', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (967, '320925', '', '', '建湖县', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (968, '320981', '', '', '东台市', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (969, '320982', '', '', '大丰市', 959, '', 0, 0);
INSERT INTO `f_com_area` VALUES (970, '321000', '', '扬州市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (971, '321001', '', '', '市辖区', 970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (972, '321002', '', '', '广陵区', 970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (973, '321003', '', '', '邗江区', 970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (974, '321012', '', '', '江都区', 970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (975, '321023', '', '', '宝应县', 970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (976, '321081', '', '', '仪征市', 970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (977, '321084', '', '', '高邮市', 970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (978, '321100', '', '镇江市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (979, '321101', '', '', '市辖区', 978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (980, '321102', '', '', '京口区', 978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (981, '321111', '', '', '润州区', 978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (982, '321112', '', '', '丹徒区', 978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (983, '321181', '', '', '丹阳市', 978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (984, '321182', '', '', '扬中市', 978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (985, '321183', '', '', '句容市', 978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (986, '321200', '', '泰州市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (987, '321201', '', '', '市辖区', 986, '', 0, 0);
INSERT INTO `f_com_area` VALUES (988, '321202', '', '', '海陵区', 986, '', 0, 0);
INSERT INTO `f_com_area` VALUES (989, '321203', '', '', '高港区', 986, '', 0, 0);
INSERT INTO `f_com_area` VALUES (990, '321204', '', '', '姜堰区', 986, '', 0, 0);
INSERT INTO `f_com_area` VALUES (991, '321281', '', '', '兴化市', 986, '', 0, 0);
INSERT INTO `f_com_area` VALUES (992, '321282', '', '', '靖江市', 986, '', 0, 0);
INSERT INTO `f_com_area` VALUES (993, '321283', '', '', '泰兴市', 986, '', 0, 0);
INSERT INTO `f_com_area` VALUES (994, '321300', '', '宿迁市', '', 875, '', 0, 0);
INSERT INTO `f_com_area` VALUES (995, '321301', '', '', '市辖区', 994, '', 0, 0);
INSERT INTO `f_com_area` VALUES (996, '321302', '', '', '宿城区', 994, '', 0, 0);
INSERT INTO `f_com_area` VALUES (997, '321311', '', '', '宿豫区', 994, '', 0, 0);
INSERT INTO `f_com_area` VALUES (998, '321322', '', '', '沭阳县', 994, '', 0, 0);
INSERT INTO `f_com_area` VALUES (999, '321323', '', '', '泗阳县', 994, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1000, '321324', '', '', '泗洪县', 994, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1001, '330000', '浙江省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1002, '330100', '', '杭州市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1003, '330101', '', '', '市辖区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1004, '330102', '', '', '上城区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1005, '330103', '', '', '下城区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1006, '330104', '', '', '江干区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1007, '330105', '', '', '拱墅区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1008, '330106', '', '', '西湖区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1009, '330108', '', '', '滨江区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1010, '330109', '', '', '萧山区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1011, '330110', '', '', '余杭区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1012, '330111', '', '', '富阳区', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1013, '330122', '', '', '桐庐县', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1014, '330127', '', '', '淳安县', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1015, '330182', '', '', '建德市', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1016, '330185', '', '', '临安市', 1002, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1017, '330200', '', '宁波市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1018, '330201', '', '', '市辖区', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1019, '330203', '', '', '海曙区', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1020, '330204', '', '', '江东区', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1021, '330205', '', '', '江北区', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1022, '330206', '', '', '北仑区', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1023, '330211', '', '', '镇海区', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1024, '330212', '', '', '鄞州区', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1025, '330225', '', '', '象山县', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1026, '330226', '', '', '宁海县', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1027, '330281', '', '', '余姚市', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1028, '330282', '', '', '慈溪市', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1029, '330283', '', '', '奉化市', 1017, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1030, '330300', '', '温州市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1031, '330301', '', '', '市辖区', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1032, '330302', '', '', '鹿城区', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1033, '330303', '', '', '龙湾区', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1034, '330304', '', '', '瓯海区', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1035, '330322', '', '', '洞头县', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1036, '330324', '', '', '永嘉县', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1037, '330326', '', '', '平阳县', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1038, '330327', '', '', '苍南县', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1039, '330328', '', '', '文成县', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1040, '330329', '', '', '泰顺县', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1041, '330381', '', '', '瑞安市', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1042, '330382', '', '', '乐清市', 1030, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1043, '330400', '', '嘉兴市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1044, '330401', '', '', '市辖区', 1043, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1045, '330402', '', '', '南湖区', 1043, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1046, '330411', '', '', '秀洲区', 1043, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1047, '330421', '', '', '嘉善县', 1043, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1048, '330424', '', '', '海盐县', 1043, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1049, '330481', '', '', '海宁市', 1043, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1050, '330482', '', '', '平湖市', 1043, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1051, '330483', '', '', '桐乡市', 1043, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1052, '330500', '', '湖州市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1053, '330501', '', '', '市辖区', 1052, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1054, '330502', '', '', '吴兴区', 1052, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1055, '330503', '', '', '南浔区', 1052, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1056, '330521', '', '', '德清县', 1052, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1057, '330522', '', '', '长兴县', 1052, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1058, '330523', '', '', '安吉县', 1052, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1059, '330600', '', '绍兴市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1060, '330601', '', '', '市辖区', 1059, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1061, '330602', '', '', '越城区', 1059, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1062, '330603', '', '', '柯桥区', 1059, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1063, '330604', '', '', '上虞区', 1059, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1064, '330624', '', '', '新昌县', 1059, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1065, '330681', '', '', '诸暨市', 1059, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1066, '330683', '', '', '嵊州市', 1059, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1067, '330700', '', '金华市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1068, '330701', '', '', '市辖区', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1069, '330702', '', '', '婺城区', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1070, '330703', '', '', '金东区', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1071, '330723', '', '', '武义县', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1072, '330726', '', '', '浦江县', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1073, '330727', '', '', '磐安县', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1074, '330781', '', '', '兰溪市', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1075, '330782', '', '', '义乌市', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1076, '330783', '', '', '东阳市', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1077, '330784', '', '', '永康市', 1067, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1078, '330800', '', '衢州市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1079, '330801', '', '', '市辖区', 1078, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1080, '330802', '', '', '柯城区', 1078, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1081, '330803', '', '', '衢江区', 1078, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1082, '330822', '', '', '常山县', 1078, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1083, '330824', '', '', '开化县', 1078, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1084, '330825', '', '', '龙游县', 1078, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1085, '330881', '', '', '江山市', 1078, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1086, '330900', '', '舟山市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1087, '330901', '', '', '市辖区', 1086, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1088, '330902', '', '', '定海区', 1086, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1089, '330903', '', '', '普陀区', 1086, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1090, '330921', '', '', '岱山县', 1086, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1091, '330922', '', '', '嵊泗县', 1086, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1092, '331000', '', '台州市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1093, '331001', '', '', '市辖区', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1094, '331002', '', '', '椒江区', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1095, '331003', '', '', '黄岩区', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1096, '331004', '', '', '路桥区', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1097, '331021', '', '', '玉环县', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1098, '331022', '', '', '三门县', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1099, '331023', '', '', '天台县', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1100, '331024', '', '', '仙居县', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1101, '331081', '', '', '温岭市', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1102, '331082', '', '', '临海市', 1092, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1103, '331100', '', '丽水市', '', 1001, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1104, '331101', '', '', '市辖区', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1105, '331102', '', '', '莲都区', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1106, '331121', '', '', '青田县', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1107, '331122', '', '', '缙云县', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1108, '331123', '', '', '遂昌县', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1109, '331124', '', '', '松阳县', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1110, '331125', '', '', '云和县', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1111, '331126', '', '', '庆元县', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1112, '331127', '', '', '景宁畲族自治县', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1113, '331181', '', '', '龙泉市', 1103, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1114, '340000', '安徽省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1115, '340100', '', '合肥市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1116, '340101', '', '', '市辖区', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1117, '340102', '', '', '瑶海区', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1118, '340103', '', '', '庐阳区', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1119, '340104', '', '', '蜀山区', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1120, '340111', '', '', '包河区', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1121, '340121', '', '', '长丰县', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1122, '340122', '', '', '肥东县', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1123, '340123', '', '', '肥西县', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1124, '340124', '', '', '庐江县', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1125, '340181', '', '', '巢湖市', 1115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1126, '340200', '', '芜湖市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1127, '340201', '', '', '市辖区', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1128, '340202', '', '', '镜湖区', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1129, '340203', '', '', '弋江区', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1130, '340207', '', '', '鸠江区', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1131, '340208', '', '', '三山区', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1132, '340221', '', '', '芜湖县', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1133, '340222', '', '', '繁昌县', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1134, '340223', '', '', '南陵县', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1135, '340225', '', '', '无为县', 1126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1136, '340300', '', '蚌埠市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1137, '340301', '', '', '市辖区', 1136, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1138, '340302', '', '', '龙子湖区', 1136, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1139, '340303', '', '', '蚌山区', 1136, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1140, '340304', '', '', '禹会区', 1136, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1141, '340311', '', '', '淮上区', 1136, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1142, '340321', '', '', '怀远县', 1136, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1143, '340322', '', '', '五河县', 1136, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1144, '340323', '', '', '固镇县', 1136, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1145, '340400', '', '淮南市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1146, '340401', '', '', '市辖区', 1145, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1147, '340402', '', '', '大通区', 1145, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1148, '340403', '', '', '田家庵区', 1145, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1149, '340404', '', '', '谢家集区', 1145, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1150, '340405', '', '', '八公山区', 1145, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1151, '340406', '', '', '潘集区', 1145, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1152, '340421', '', '', '凤台县', 1145, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1153, '340500', '', '马鞍山市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1154, '340501', '', '', '市辖区', 1153, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1155, '340503', '', '', '花山区', 1153, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1156, '340504', '', '', '雨山区', 1153, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1157, '340506', '', '', '博望区', 1153, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1158, '340521', '', '', '当涂县', 1153, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1159, '340522', '', '', '含山县', 1153, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1160, '340523', '', '', '和县', 1153, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1161, '340600', '', '淮北市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1162, '340601', '', '', '市辖区', 1161, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1163, '340602', '', '', '杜集区', 1161, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1164, '340603', '', '', '相山区', 1161, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1165, '340604', '', '', '烈山区', 1161, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1166, '340621', '', '', '濉溪县', 1161, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1167, '340700', '', '铜陵市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1168, '340701', '', '', '市辖区', 1167, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1169, '340702', '', '', '铜官山区', 1167, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1170, '340703', '', '', '狮子山区', 1167, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1171, '340711', '', '', '郊区', 1167, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1172, '340721', '', '', '铜陵县', 1167, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1173, '340800', '', '安庆市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1174, '340801', '', '', '市辖区', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1175, '340802', '', '', '迎江区', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1176, '340803', '', '', '大观区', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1177, '340811', '', '', '宜秀区', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1178, '340822', '', '', '怀宁县', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1179, '340823', '', '', '枞阳县', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1180, '340824', '', '', '潜山县', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1181, '340825', '', '', '太湖县', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1182, '340826', '', '', '宿松县', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1183, '340827', '', '', '望江县', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1184, '340828', '', '', '岳西县', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1185, '340881', '', '', '桐城市', 1173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1186, '341000', '', '黄山市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1187, '341001', '', '', '市辖区', 1186, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1188, '341002', '', '', '屯溪区', 1186, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1189, '341003', '', '', '黄山区', 1186, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1190, '341004', '', '', '徽州区', 1186, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1191, '341021', '', '', '歙县', 1186, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1192, '341022', '', '', '休宁县', 1186, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1193, '341023', '', '', '黟县', 1186, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1194, '341024', '', '', '祁门县', 1186, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1195, '341100', '', '滁州市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1196, '341101', '', '', '市辖区', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1197, '341102', '', '', '琅琊区', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1198, '341103', '', '', '南谯区', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1199, '341122', '', '', '来安县', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1200, '341124', '', '', '全椒县', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1201, '341125', '', '', '定远县', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1202, '341126', '', '', '凤阳县', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1203, '341181', '', '', '天长市', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1204, '341182', '', '', '明光市', 1195, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1205, '341200', '', '阜阳市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1206, '341201', '', '', '市辖区', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1207, '341202', '', '', '颍州区', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1208, '341203', '', '', '颍东区', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1209, '341204', '', '', '颍泉区', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1210, '341221', '', '', '临泉县', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1211, '341222', '', '', '太和县', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1212, '341225', '', '', '阜南县', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1213, '341226', '', '', '颍上县', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1214, '341282', '', '', '界首市', 1205, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1215, '341300', '', '宿州市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1216, '341301', '', '', '市辖区', 1215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1217, '341302', '', '', '埇桥区', 1215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1218, '341321', '', '', '砀山县', 1215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1219, '341322', '', '', '萧县', 1215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1220, '341323', '', '', '灵璧县', 1215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1221, '341324', '', '', '泗县', 1215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1222, '341500', '', '六安市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1223, '341501', '', '', '市辖区', 1222, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1224, '341502', '', '', '金安区', 1222, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1225, '341503', '', '', '裕安区', 1222, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1226, '341521', '', '', '寿县', 1222, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1227, '341522', '', '', '霍邱县', 1222, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1228, '341523', '', '', '舒城县', 1222, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1229, '341524', '', '', '金寨县', 1222, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1230, '341525', '', '', '霍山县', 1222, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1231, '341600', '', '亳州市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1232, '341601', '', '', '市辖区', 1231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1233, '341602', '', '', '谯城区', 1231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1234, '341621', '', '', '涡阳县', 1231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1235, '341622', '', '', '蒙城县', 1231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1236, '341623', '', '', '利辛县', 1231, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1237, '341700', '', '池州市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1238, '341701', '', '', '市辖区', 1237, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1239, '341702', '', '', '贵池区', 1237, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1240, '341721', '', '', '东至县', 1237, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1241, '341722', '', '', '石台县', 1237, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1242, '341723', '', '', '青阳县', 1237, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1243, '341800', '', '宣城市', '', 1114, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1244, '341801', '', '', '市辖区', 1243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1245, '341802', '', '', '宣州区', 1243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1246, '341821', '', '', '郎溪县', 1243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1247, '341822', '', '', '广德县', 1243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1248, '341823', '', '', '泾县', 1243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1249, '341824', '', '', '绩溪县', 1243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1250, '341825', '', '', '旌德县', 1243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1251, '341881', '', '', '宁国市', 1243, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1252, '350000', '福建省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1253, '350100', '', '福州市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1254, '350101', '', '', '市辖区', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1255, '350102', '', '', '鼓楼区', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1256, '350103', '', '', '台江区', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1257, '350104', '', '', '仓山区', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1258, '350105', '', '', '马尾区', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1259, '350111', '', '', '晋安区', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1260, '350121', '', '', '闽侯县', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1261, '350122', '', '', '连江县', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1262, '350123', '', '', '罗源县', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1263, '350124', '', '', '闽清县', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1264, '350125', '', '', '永泰县', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1265, '350128', '', '', '平潭县', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1266, '350181', '', '', '福清市', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1267, '350182', '', '', '长乐市', 1253, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1268, '350200', '', '厦门市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1269, '350201', '', '', '市辖区', 1268, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1270, '350203', '', '', '思明区', 1268, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1271, '350205', '', '', '海沧区', 1268, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1272, '350206', '', '', '湖里区', 1268, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1273, '350211', '', '', '集美区', 1268, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1274, '350212', '', '', '同安区', 1268, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1275, '350213', '', '', '翔安区', 1268, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1276, '350300', '', '莆田市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1277, '350301', '', '', '市辖区', 1276, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1278, '350302', '', '', '城厢区', 1276, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1279, '350303', '', '', '涵江区', 1276, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1280, '350304', '', '', '荔城区', 1276, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1281, '350305', '', '', '秀屿区', 1276, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1282, '350322', '', '', '仙游县', 1276, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1283, '350400', '', '三明市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1284, '350401', '', '', '市辖区', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1285, '350402', '', '', '梅列区', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1286, '350403', '', '', '三元区', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1287, '350421', '', '', '明溪县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1288, '350423', '', '', '清流县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1289, '350424', '', '', '宁化县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1290, '350425', '', '', '大田县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1291, '350426', '', '', '尤溪县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1292, '350427', '', '', '沙县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1293, '350428', '', '', '将乐县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1294, '350429', '', '', '泰宁县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1295, '350430', '', '', '建宁县', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1296, '350481', '', '', '永安市', 1283, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1297, '350500', '', '泉州市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1298, '350501', '', '', '市辖区', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1299, '350502', '', '', '鲤城区', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1300, '350503', '', '', '丰泽区', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1301, '350504', '', '', '洛江区', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1302, '350505', '', '', '泉港区', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1303, '350521', '', '', '惠安县', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1304, '350524', '', '', '安溪县', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1305, '350525', '', '', '永春县', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1306, '350526', '', '', '德化县', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1307, '350527', '', '', '金门县', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1308, '350581', '', '', '石狮市', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1309, '350582', '', '', '晋江市', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1310, '350583', '', '', '南安市', 1297, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1311, '350600', '', '漳州市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1312, '350601', '', '', '市辖区', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1313, '350602', '', '', '芗城区', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1314, '350603', '', '', '龙文区', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1315, '350622', '', '', '云霄县', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1316, '350623', '', '', '漳浦县', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1317, '350624', '', '', '诏安县', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1318, '350625', '', '', '长泰县', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1319, '350626', '', '', '东山县', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1320, '350627', '', '', '南靖县', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1321, '350628', '', '', '平和县', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1322, '350629', '', '', '华安县', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1323, '350681', '', '', '龙海市', 1311, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1324, '350700', '', '南平市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1325, '350701', '', '', '市辖区', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1326, '350702', '', '', '延平区', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1327, '350703', '', '', '建阳区', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1328, '350721', '', '', '顺昌县', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1329, '350722', '', '', '浦城县', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1330, '350723', '', '', '光泽县', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1331, '350724', '', '', '松溪县', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1332, '350725', '', '', '政和县', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1333, '350781', '', '', '邵武市', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1334, '350782', '', '', '武夷山市', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1335, '350783', '', '', '建瓯市', 1324, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1336, '350800', '', '龙岩市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1337, '350801', '', '', '市辖区', 1336, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1338, '350802', '', '', '新罗区', 1336, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1339, '350803', '', '', '永定区', 1336, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1340, '350821', '', '', '长汀县', 1336, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1341, '350823', '', '', '上杭县', 1336, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1342, '350824', '', '', '武平县', 1336, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1343, '350825', '', '', '连城县', 1336, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1344, '350881', '', '', '漳平市', 1336, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1345, '350900', '', '宁德市', '', 1252, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1346, '350901', '', '', '市辖区', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1347, '350902', '', '', '蕉城区', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1348, '350921', '', '', '霞浦县', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1349, '350922', '', '', '古田县', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1350, '350923', '', '', '屏南县', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1351, '350924', '', '', '寿宁县', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1352, '350925', '', '', '周宁县', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1353, '350926', '', '', '柘荣县', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1354, '350981', '', '', '福安市', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1355, '350982', '', '', '福鼎市', 1345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1356, '360000', '江西省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1357, '360100', '', '南昌市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1358, '360101', '', '', '市辖区', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1359, '360102', '', '', '东湖区', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1360, '360103', '', '', '西湖区', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1361, '360104', '', '', '青云谱区', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1362, '360105', '', '', '湾里区', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1363, '360111', '', '', '青山湖区', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1364, '360121', '', '', '南昌县', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1365, '360122', '', '', '新建县', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1366, '360123', '', '', '安义县', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1367, '360124', '', '', '进贤县', 1357, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1368, '360200', '', '景德镇市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1369, '360201', '', '', '市辖区', 1368, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1370, '360202', '', '', '昌江区', 1368, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1371, '360203', '', '', '珠山区', 1368, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1372, '360222', '', '', '浮梁县', 1368, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1373, '360281', '', '', '乐平市', 1368, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1374, '360300', '', '萍乡市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1375, '360301', '', '', '市辖区', 1374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1376, '360302', '', '', '安源区', 1374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1377, '360313', '', '', '湘东区', 1374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1378, '360321', '', '', '莲花县', 1374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1379, '360322', '', '', '上栗县', 1374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1380, '360323', '', '', '芦溪县', 1374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1381, '360400', '', '九江市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1382, '360401', '', '', '市辖区', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1383, '360402', '', '', '庐山区', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1384, '360403', '', '', '浔阳区', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1385, '360421', '', '', '九江县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1386, '360423', '', '', '武宁县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1387, '360424', '', '', '修水县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1388, '360425', '', '', '永修县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1389, '360426', '', '', '德安县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1390, '360427', '', '', '星子县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1391, '360428', '', '', '都昌县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1392, '360429', '', '', '湖口县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1393, '360430', '', '', '彭泽县', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1394, '360481', '', '', '瑞昌市', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1395, '360482', '', '', '共青城市', 1381, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1396, '360500', '', '新余市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1397, '360501', '', '', '市辖区', 1396, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1398, '360502', '', '', '渝水区', 1396, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1399, '360521', '', '', '分宜县', 1396, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1400, '360600', '', '鹰潭市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1401, '360601', '', '', '市辖区', 1400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1402, '360602', '', '', '月湖区', 1400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1403, '360622', '', '', '余江县', 1400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1404, '360681', '', '', '贵溪市', 1400, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1405, '360700', '', '赣州市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1406, '360701', '', '', '市辖区', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1407, '360702', '', '', '章贡区', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1408, '360703', '', '', '南康区', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1409, '360721', '', '', '赣县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1410, '360722', '', '', '信丰县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1411, '360723', '', '', '大余县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1412, '360724', '', '', '上犹县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1413, '360725', '', '', '崇义县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1414, '360726', '', '', '安远县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1415, '360727', '', '', '龙南县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1416, '360728', '', '', '定南县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1417, '360729', '', '', '全南县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1418, '360730', '', '', '宁都县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1419, '360731', '', '', '于都县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1420, '360732', '', '', '兴国县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1421, '360733', '', '', '会昌县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1422, '360734', '', '', '寻乌县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1423, '360735', '', '', '石城县', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1424, '360781', '', '', '瑞金市', 1405, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1425, '360800', '', '吉安市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1426, '360801', '', '', '市辖区', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1427, '360802', '', '', '吉州区', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1428, '360803', '', '', '青原区', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1429, '360821', '', '', '吉安县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1430, '360822', '', '', '吉水县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1431, '360823', '', '', '峡江县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1432, '360824', '', '', '新干县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1433, '360825', '', '', '永丰县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1434, '360826', '', '', '泰和县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1435, '360827', '', '', '遂川县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1436, '360828', '', '', '万安县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1437, '360829', '', '', '安福县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1438, '360830', '', '', '永新县', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1439, '360881', '', '', '井冈山市', 1425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1440, '360900', '', '宜春市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1441, '360901', '', '', '市辖区', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1442, '360902', '', '', '袁州区', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1443, '360921', '', '', '奉新县', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1444, '360922', '', '', '万载县', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1445, '360923', '', '', '上高县', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1446, '360924', '', '', '宜丰县', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1447, '360925', '', '', '靖安县', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1448, '360926', '', '', '铜鼓县', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1449, '360981', '', '', '丰城市', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1450, '360982', '', '', '樟树市', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1451, '360983', '', '', '高安市', 1440, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1452, '361000', '', '抚州市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1453, '361001', '', '', '市辖区', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1454, '361002', '', '', '临川区', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1455, '361021', '', '', '南城县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1456, '361022', '', '', '黎川县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1457, '361023', '', '', '南丰县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1458, '361024', '', '', '崇仁县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1459, '361025', '', '', '乐安县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1460, '361026', '', '', '宜黄县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1461, '361027', '', '', '金溪县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1462, '361028', '', '', '资溪县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1463, '361029', '', '', '东乡县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1464, '361030', '', '', '广昌县', 1452, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1465, '361100', '', '上饶市', '', 1356, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1466, '361101', '', '', '市辖区', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1467, '361102', '', '', '信州区', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1468, '361121', '', '', '上饶县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1469, '361122', '', '', '广丰县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1470, '361123', '', '', '玉山县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1471, '361124', '', '', '铅山县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1472, '361125', '', '', '横峰县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1473, '361126', '', '', '弋阳县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1474, '361127', '', '', '余干县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1475, '361128', '', '', '鄱阳县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1476, '361129', '', '', '万年县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1477, '361130', '', '', '婺源县', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1478, '361181', '', '', '德兴市', 1465, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1479, '370000', '山东省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1480, '370100', '', '济南市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1481, '370101', '', '', '市辖区', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1482, '370102', '', '', '历下区', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1483, '370103', '', '', '市中区', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1484, '370104', '', '', '槐荫区', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1485, '370105', '', '', '天桥区', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1486, '370112', '', '', '历城区', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1487, '370113', '', '', '长清区', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1488, '370124', '', '', '平阴县', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1489, '370125', '', '', '济阳县', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1490, '370126', '', '', '商河县', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1491, '370181', '', '', '章丘市', 1480, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1492, '370200', '', '青岛市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1493, '370201', '', '', '市辖区', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1494, '370202', '', '', '市南区', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1495, '370203', '', '', '市北区', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1496, '370211', '', '', '黄岛区', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1497, '370212', '', '', '崂山区', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1498, '370213', '', '', '李沧区', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1499, '370214', '', '', '城阳区', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1500, '370281', '', '', '胶州市', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1501, '370282', '', '', '即墨市', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1502, '370283', '', '', '平度市', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1503, '370285', '', '', '莱西市', 1492, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1504, '370300', '', '淄博市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1505, '370301', '', '', '市辖区', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1506, '370302', '', '', '淄川区', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1507, '370303', '', '', '张店区', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1508, '370304', '', '', '博山区', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1509, '370305', '', '', '临淄区', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1510, '370306', '', '', '周村区', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1511, '370321', '', '', '桓台县', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1512, '370322', '', '', '高青县', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1513, '370323', '', '', '沂源县', 1504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1514, '370400', '', '枣庄市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1515, '370401', '', '', '市辖区', 1514, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1516, '370402', '', '', '市中区', 1514, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1517, '370403', '', '', '薛城区', 1514, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1518, '370404', '', '', '峄城区', 1514, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1519, '370405', '', '', '台儿庄区', 1514, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1520, '370406', '', '', '山亭区', 1514, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1521, '370481', '', '', '滕州市', 1514, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1522, '370500', '', '东营市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1523, '370501', '', '', '市辖区', 1522, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1524, '370502', '', '', '东营区', 1522, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1525, '370503', '', '', '河口区', 1522, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1526, '370521', '', '', '垦利县', 1522, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1527, '370522', '', '', '利津县', 1522, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1528, '370523', '', '', '广饶县', 1522, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1529, '370600', '', '烟台市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1530, '370601', '', '', '市辖区', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1531, '370602', '', '', '芝罘区', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1532, '370611', '', '', '福山区', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1533, '370612', '', '', '牟平区', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1534, '370613', '', '', '莱山区', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1535, '370634', '', '', '长岛县', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1536, '370681', '', '', '龙口市', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1537, '370682', '', '', '莱阳市', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1538, '370683', '', '', '莱州市', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1539, '370684', '', '', '蓬莱市', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1540, '370685', '', '', '招远市', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1541, '370686', '', '', '栖霞市', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1542, '370687', '', '', '海阳市', 1529, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1543, '370700', '', '潍坊市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1544, '370701', '', '', '市辖区', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1545, '370702', '', '', '潍城区', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1546, '370703', '', '', '寒亭区', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1547, '370704', '', '', '坊子区', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1548, '370705', '', '', '奎文区', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1549, '370724', '', '', '临朐县', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1550, '370725', '', '', '昌乐县', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1551, '370781', '', '', '青州市', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1552, '370782', '', '', '诸城市', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1553, '370783', '', '', '寿光市', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1554, '370784', '', '', '安丘市', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1555, '370785', '', '', '高密市', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1556, '370786', '', '', '昌邑市', 1543, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1557, '370800', '', '济宁市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1558, '370801', '', '', '市辖区', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1559, '370811', '', '', '任城区', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1560, '370812', '', '', '兖州区', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1561, '370826', '', '', '微山县', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1562, '370827', '', '', '鱼台县', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1563, '370828', '', '', '金乡县', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1564, '370829', '', '', '嘉祥县', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1565, '370830', '', '', '汶上县', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1566, '370831', '', '', '泗水县', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1567, '370832', '', '', '梁山县', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1568, '370881', '', '', '曲阜市', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1569, '370883', '', '', '邹城市', 1557, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1570, '370900', '', '泰安市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1571, '370901', '', '', '市辖区', 1570, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1572, '370902', '', '', '泰山区', 1570, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1573, '370911', '', '', '岱岳区', 1570, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1574, '370921', '', '', '宁阳县', 1570, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1575, '370923', '', '', '东平县', 1570, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1576, '370982', '', '', '新泰市', 1570, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1577, '370983', '', '', '肥城市', 1570, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1578, '371000', '', '威海市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1579, '371001', '', '', '市辖区', 1578, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1580, '371002', '', '', '环翠区', 1578, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1581, '371081', '', '', '文登市', 1578, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1582, '371082', '', '', '荣成市', 1578, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1583, '371083', '', '', '乳山市', 1578, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1584, '371100', '', '日照市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1585, '371101', '', '', '市辖区', 1584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1586, '371102', '', '', '东港区', 1584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1587, '371103', '', '', '岚山区', 1584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1588, '371121', '', '', '五莲县', 1584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1589, '371122', '', '', '莒县', 1584, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1590, '371200', '', '莱芜市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1591, '371201', '', '', '市辖区', 1590, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1592, '371202', '', '', '莱城区', 1590, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1593, '371203', '', '', '钢城区', 1590, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1594, '371300', '', '临沂市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1595, '371301', '', '', '市辖区', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1596, '371302', '', '', '兰山区', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1597, '371311', '', '', '罗庄区', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1598, '371312', '', '', '河东区', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1599, '371321', '', '', '沂南县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1600, '371322', '', '', '郯城县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1601, '371323', '', '', '沂水县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1602, '371324', '', '', '兰陵县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1603, '371325', '', '', '费县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1604, '371326', '', '', '平邑县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1605, '371327', '', '', '莒南县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1606, '371328', '', '', '蒙阴县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1607, '371329', '', '', '临沭县', 1594, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1608, '371400', '', '德州市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1609, '371401', '', '', '市辖区', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1610, '371402', '', '', '德城区', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1611, '371403', '', '', '陵城区', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1612, '371422', '', '', '宁津县', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1613, '371423', '', '', '庆云县', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1614, '371424', '', '', '临邑县', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1615, '371425', '', '', '齐河县', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1616, '371426', '', '', '平原县', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1617, '371427', '', '', '夏津县', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1618, '371428', '', '', '武城县', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1619, '371481', '', '', '乐陵市', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1620, '371482', '', '', '禹城市', 1608, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1621, '371500', '', '聊城市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1622, '371501', '', '', '市辖区', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1623, '371502', '', '', '东昌府区', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1624, '371521', '', '', '阳谷县', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1625, '371522', '', '', '莘县', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1626, '371523', '', '', '茌平县', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1627, '371524', '', '', '东阿县', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1628, '371525', '', '', '冠县', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1629, '371526', '', '', '高唐县', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1630, '371581', '', '', '临清市', 1621, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1631, '371600', '', '滨州市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1632, '371601', '', '', '市辖区', 1631, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1633, '371602', '', '', '滨城区', 1631, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1634, '371603', '', '', '沾化区', 1631, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1635, '371621', '', '', '惠民县', 1631, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1636, '371622', '', '', '阳信县', 1631, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1637, '371623', '', '', '无棣县', 1631, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1638, '371625', '', '', '博兴县', 1631, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1639, '371626', '', '', '邹平县', 1631, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1640, '371700', '', '菏泽市', '', 1479, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1641, '371701', '', '', '市辖区', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1642, '371702', '', '', '牡丹区', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1643, '371721', '', '', '曹县', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1644, '371722', '', '', '单县', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1645, '371723', '', '', '成武县', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1646, '371724', '', '', '巨野县', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1647, '371725', '', '', '郓城县', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1648, '371726', '', '', '鄄城县', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1649, '371727', '', '', '定陶县', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1650, '371728', '', '', '东明县', 1640, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1651, '410000', '河南省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1652, '410100', '', '郑州市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1653, '410101', '', '', '市辖区', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1654, '410102', '', '', '中原区', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1655, '410103', '', '', '二七区', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1656, '410104', '', '', '管城回族区', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1657, '410105', '', '', '金水区', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1658, '410106', '', '', '上街区', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1659, '410108', '', '', '惠济区', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1660, '410122', '', '', '中牟县', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1661, '410181', '', '', '巩义市', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1662, '410182', '', '', '荥阳市', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1663, '410183', '', '', '新密市', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1664, '410184', '', '', '新郑市', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1665, '410185', '', '', '登封市', 1652, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1666, '410200', '', '开封市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1667, '410201', '', '', '市辖区', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1668, '410202', '', '', '龙亭区', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1669, '410203', '', '', '顺河回族区', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1670, '410204', '', '', '鼓楼区', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1671, '410205', '', '', '禹王台区', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1672, '410212', '', '', '祥符区', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1673, '410221', '', '', '杞县', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1674, '410222', '', '', '通许县', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1675, '410223', '', '', '尉氏县', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1676, '410225', '', '', '兰考县', 1666, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1677, '410300', '', '洛阳市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1678, '410301', '', '', '市辖区', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1679, '410302', '', '', '老城区', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1680, '410303', '', '', '西工区', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1681, '410304', '', '', '瀍河回族区', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1682, '410305', '', '', '涧西区', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1683, '410306', '', '', '吉利区', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1684, '410311', '', '', '洛龙区', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1685, '410322', '', '', '孟津县', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1686, '410323', '', '', '新安县', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1687, '410324', '', '', '栾川县', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1688, '410325', '', '', '嵩县', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1689, '410326', '', '', '汝阳县', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1690, '410327', '', '', '宜阳县', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1691, '410328', '', '', '洛宁县', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1692, '410329', '', '', '伊川县', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1693, '410381', '', '', '偃师市', 1677, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1694, '410400', '', '平顶山市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1695, '410401', '', '', '市辖区', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1696, '410402', '', '', '新华区', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1697, '410403', '', '', '卫东区', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1698, '410404', '', '', '石龙区', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1699, '410411', '', '', '湛河区', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1700, '410421', '', '', '宝丰县', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1701, '410422', '', '', '叶县', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1702, '410423', '', '', '鲁山县', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1703, '410425', '', '', '郏县', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1704, '410481', '', '', '舞钢市', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1705, '410482', '', '', '汝州市', 1694, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1706, '410500', '', '安阳市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1707, '410501', '', '', '市辖区', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1708, '410502', '', '', '文峰区', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1709, '410503', '', '', '北关区', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1710, '410505', '', '', '殷都区', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1711, '410506', '', '', '龙安区', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1712, '410522', '', '', '安阳县', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1713, '410523', '', '', '汤阴县', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1714, '410526', '', '', '滑县', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1715, '410527', '', '', '内黄县', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1716, '410581', '', '', '林州市', 1706, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1717, '410600', '', '鹤壁市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1718, '410601', '', '', '市辖区', 1717, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1719, '410602', '', '', '鹤山区', 1717, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1720, '410603', '', '', '山城区', 1717, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1721, '410611', '', '', '淇滨区', 1717, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1722, '410621', '', '', '浚县', 1717, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1723, '410622', '', '', '淇县', 1717, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1724, '410700', '', '新乡市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1725, '410701', '', '', '市辖区', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1726, '410702', '', '', '红旗区', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1727, '410703', '', '', '卫滨区', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1728, '410704', '', '', '凤泉区', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1729, '410711', '', '', '牧野区', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1730, '410721', '', '', '新乡县', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1731, '410724', '', '', '获嘉县', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1732, '410725', '', '', '原阳县', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1733, '410726', '', '', '延津县', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1734, '410727', '', '', '封丘县', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1735, '410728', '', '', '长垣县', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1736, '410781', '', '', '卫辉市', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1737, '410782', '', '', '辉县市', 1724, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1738, '410800', '', '焦作市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1739, '410801', '', '', '市辖区', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1740, '410802', '', '', '解放区', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1741, '410803', '', '', '中站区', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1742, '410804', '', '', '马村区', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1743, '410811', '', '', '山阳区', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1744, '410821', '', '', '修武县', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1745, '410822', '', '', '博爱县', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1746, '410823', '', '', '武陟县', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1747, '410825', '', '', '温县', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1748, '410882', '', '', '沁阳市', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1749, '410883', '', '', '孟州市', 1738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1750, '410900', '', '濮阳市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1751, '410901', '', '', '市辖区', 1750, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1752, '410902', '', '', '华龙区', 1750, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1753, '410922', '', '', '清丰县', 1750, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1754, '410923', '', '', '南乐县', 1750, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1755, '410926', '', '', '范县', 1750, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1756, '410927', '', '', '台前县', 1750, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1757, '410928', '', '', '濮阳县', 1750, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1758, '411000', '', '许昌市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1759, '411001', '', '', '市辖区', 1758, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1760, '411002', '', '', '魏都区', 1758, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1761, '411023', '', '', '许昌县', 1758, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1762, '411024', '', '', '鄢陵县', 1758, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1763, '411025', '', '', '襄城县', 1758, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1764, '411081', '', '', '禹州市', 1758, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1765, '411082', '', '', '长葛市', 1758, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1766, '411100', '', '漯河市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1767, '411101', '', '', '市辖区', 1766, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1768, '411102', '', '', '源汇区', 1766, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1769, '411103', '', '', '郾城区', 1766, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1770, '411104', '', '', '召陵区', 1766, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1771, '411121', '', '', '舞阳县', 1766, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1772, '411122', '', '', '临颍县', 1766, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1773, '411200', '', '三门峡市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1774, '411201', '', '', '市辖区', 1773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1775, '411202', '', '', '湖滨区', 1773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1776, '411221', '', '', '渑池县', 1773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1777, '411222', '', '', '陕县', 1773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1778, '411224', '', '', '卢氏县', 1773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1779, '411281', '', '', '义马市', 1773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1780, '411282', '', '', '灵宝市', 1773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1781, '411300', '', '南阳市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1782, '411301', '', '', '市辖区', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1783, '411302', '', '', '宛城区', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1784, '411303', '', '', '卧龙区', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1785, '411321', '', '', '南召县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1786, '411322', '', '', '方城县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1787, '411323', '', '', '西峡县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1788, '411324', '', '', '镇平县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1789, '411325', '', '', '内乡县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1790, '411326', '', '', '淅川县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1791, '411327', '', '', '社旗县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1792, '411328', '', '', '唐河县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1793, '411329', '', '', '新野县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1794, '411330', '', '', '桐柏县', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1795, '411381', '', '', '邓州市', 1781, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1796, '411400', '', '商丘市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1797, '411401', '', '', '市辖区', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1798, '411402', '', '', '梁园区', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1799, '411403', '', '', '睢阳区', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1800, '411421', '', '', '民权县', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1801, '411422', '', '', '睢县', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1802, '411423', '', '', '宁陵县', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1803, '411424', '', '', '柘城县', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1804, '411425', '', '', '虞城县', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1805, '411426', '', '', '夏邑县', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1806, '411481', '', '', '永城市', 1796, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1807, '411500', '', '信阳市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1808, '411501', '', '', '市辖区', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1809, '411502', '', '', '浉河区', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1810, '411503', '', '', '平桥区', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1811, '411521', '', '', '罗山县', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1812, '411522', '', '', '光山县', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1813, '411523', '', '', '新县', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1814, '411524', '', '', '商城县', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1815, '411525', '', '', '固始县', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1816, '411526', '', '', '潢川县', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1817, '411527', '', '', '淮滨县', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1818, '411528', '', '', '息县', 1807, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1819, '411600', '', '周口市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1820, '411601', '', '', '市辖区', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1821, '411602', '', '', '川汇区', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1822, '411621', '', '', '扶沟县', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1823, '411622', '', '', '西华县', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1824, '411623', '', '', '商水县', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1825, '411624', '', '', '沈丘县', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1826, '411625', '', '', '郸城县', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1827, '411626', '', '', '淮阳县', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1828, '411627', '', '', '太康县', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1829, '411628', '', '', '鹿邑县', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1830, '411681', '', '', '项城市', 1819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1831, '411700', '', '驻马店市', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1832, '411701', '', '', '市辖区', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1833, '411702', '', '', '驿城区', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1834, '411721', '', '', '西平县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1835, '411722', '', '', '上蔡县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1836, '411723', '', '', '平舆县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1837, '411724', '', '', '正阳县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1838, '411725', '', '', '确山县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1839, '411726', '', '', '泌阳县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1840, '411727', '', '', '汝南县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1841, '411728', '', '', '遂平县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1842, '411729', '', '', '新蔡县', 1831, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1843, '419000', '', '省直辖县级行政区划', '', 1651, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1844, '419001', '', '', '济源市', 1843, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1845, '420000', '湖北省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1846, '420100', '', '武汉市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1847, '420101', '', '', '市辖区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1848, '420102', '', '', '江岸区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1849, '420103', '', '', '江汉区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1850, '420104', '', '', '硚口区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1851, '420105', '', '', '汉阳区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1852, '420106', '', '', '武昌区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1853, '420107', '', '', '青山区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1854, '420111', '', '', '洪山区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1855, '420112', '', '', '东西湖区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1856, '420113', '', '', '汉南区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1857, '420114', '', '', '蔡甸区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1858, '420115', '', '', '江夏区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1859, '420116', '', '', '黄陂区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1860, '420117', '', '', '新洲区', 1846, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1861, '420200', '', '黄石市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1862, '420201', '', '', '市辖区', 1861, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1863, '420202', '', '', '黄石港区', 1861, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1864, '420203', '', '', '西塞山区', 1861, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1865, '420204', '', '', '下陆区', 1861, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1866, '420205', '', '', '铁山区', 1861, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1867, '420222', '', '', '阳新县', 1861, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1868, '420281', '', '', '大冶市', 1861, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1869, '420300', '', '十堰市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1870, '420301', '', '', '市辖区', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1871, '420302', '', '', '茅箭区', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1872, '420303', '', '', '张湾区', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1873, '420304', '', '', '郧阳区', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1874, '420322', '', '', '郧西县', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1875, '420323', '', '', '竹山县', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1876, '420324', '', '', '竹溪县', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1877, '420325', '', '', '房县', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1878, '420381', '', '', '丹江口市', 1869, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1879, '420500', '', '宜昌市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1880, '420501', '', '', '市辖区', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1881, '420502', '', '', '西陵区', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1882, '420503', '', '', '伍家岗区', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1883, '420504', '', '', '点军区', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1884, '420505', '', '', '猇亭区', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1885, '420506', '', '', '夷陵区', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1886, '420525', '', '', '远安县', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1887, '420526', '', '', '兴山县', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1888, '420527', '', '', '秭归县', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1889, '420528', '', '', '长阳土家族自治县', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1890, '420529', '', '', '五峰土家族自治县', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1891, '420581', '', '', '宜都市', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1892, '420582', '', '', '当阳市', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1893, '420583', '', '', '枝江市', 1879, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1894, '420600', '', '襄阳市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1895, '420601', '', '', '市辖区', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1896, '420602', '', '', '襄城区', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1897, '420606', '', '', '樊城区', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1898, '420607', '', '', '襄州区', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1899, '420624', '', '', '南漳县', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1900, '420625', '', '', '谷城县', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1901, '420626', '', '', '保康县', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1902, '420682', '', '', '老河口市', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1903, '420683', '', '', '枣阳市', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1904, '420684', '', '', '宜城市', 1894, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1905, '420700', '', '鄂州市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1906, '420701', '', '', '市辖区', 1905, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1907, '420702', '', '', '梁子湖区', 1905, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1908, '420703', '', '', '华容区', 1905, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1909, '420704', '', '', '鄂城区', 1905, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1910, '420800', '', '荆门市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1911, '420801', '', '', '市辖区', 1910, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1912, '420802', '', '', '东宝区', 1910, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1913, '420804', '', '', '掇刀区', 1910, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1914, '420821', '', '', '京山县', 1910, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1915, '420822', '', '', '沙洋县', 1910, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1916, '420881', '', '', '钟祥市', 1910, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1917, '420900', '', '孝感市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1918, '420901', '', '', '市辖区', 1917, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1919, '420902', '', '', '孝南区', 1917, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1920, '420921', '', '', '孝昌县', 1917, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1921, '420922', '', '', '大悟县', 1917, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1922, '420923', '', '', '云梦县', 1917, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1923, '420981', '', '', '应城市', 1917, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1924, '420982', '', '', '安陆市', 1917, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1925, '420984', '', '', '汉川市', 1917, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1926, '421000', '', '荆州市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1927, '421001', '', '', '市辖区', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1928, '421002', '', '', '沙市区', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1929, '421003', '', '', '荆州区', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1930, '421022', '', '', '公安县', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1931, '421023', '', '', '监利县', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1932, '421024', '', '', '江陵县', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1933, '421081', '', '', '石首市', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1934, '421083', '', '', '洪湖市', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1935, '421087', '', '', '松滋市', 1926, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1936, '421100', '', '黄冈市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1937, '421101', '', '', '市辖区', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1938, '421102', '', '', '黄州区', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1939, '421121', '', '', '团风县', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1940, '421122', '', '', '红安县', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1941, '421123', '', '', '罗田县', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1942, '421124', '', '', '英山县', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1943, '421125', '', '', '浠水县', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1944, '421126', '', '', '蕲春县', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1945, '421127', '', '', '黄梅县', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1946, '421181', '', '', '麻城市', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1947, '421182', '', '', '武穴市', 1936, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1948, '421200', '', '咸宁市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1949, '421201', '', '', '市辖区', 1948, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1950, '421202', '', '', '咸安区', 1948, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1951, '421221', '', '', '嘉鱼县', 1948, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1952, '421222', '', '', '通城县', 1948, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1953, '421223', '', '', '崇阳县', 1948, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1954, '421224', '', '', '通山县', 1948, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1955, '421281', '', '', '赤壁市', 1948, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1956, '421300', '', '随州市', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1957, '421301', '', '', '市辖区', 1956, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1958, '421303', '', '', '曾都区', 1956, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1959, '421321', '', '', '随县', 1956, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1960, '421381', '', '', '广水市', 1956, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1961, '422800', '', '恩施土家族苗族自治州', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1962, '422801', '', '', '恩施市', 1961, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1963, '422802', '', '', '利川市', 1961, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1964, '422822', '', '', '建始县', 1961, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1965, '422823', '', '', '巴东县', 1961, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1966, '422825', '', '', '宣恩县', 1961, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1967, '422826', '', '', '咸丰县', 1961, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1968, '422827', '', '', '来凤县', 1961, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1969, '422828', '', '', '鹤峰县', 1961, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1970, '429000', '', '省直辖县级行政区划', '', 1845, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1971, '429004', '', '', '仙桃市', 1970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1972, '429005', '', '', '潜江市', 1970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1973, '429006', '', '', '天门市', 1970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1974, '429021', '', '', '神农架林区', 1970, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1975, '430000', '湖南省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1976, '430100', '', '长沙市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1977, '430101', '', '', '市辖区', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1978, '430102', '', '', '芙蓉区', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1979, '430103', '', '', '天心区', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1980, '430104', '', '', '岳麓区', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1981, '430105', '', '', '开福区', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1982, '430111', '', '', '雨花区', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1983, '430112', '', '', '望城区', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1984, '430121', '', '', '长沙县', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1985, '430124', '', '', '宁乡县', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1986, '430181', '', '', '浏阳市', 1976, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1987, '430200', '', '株洲市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1988, '430201', '', '', '市辖区', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1989, '430202', '', '', '荷塘区', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1990, '430203', '', '', '芦淞区', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1991, '430204', '', '', '石峰区', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1992, '430211', '', '', '天元区', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1993, '430221', '', '', '株洲县', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1994, '430223', '', '', '攸县', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1995, '430224', '', '', '茶陵县', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1996, '430225', '', '', '炎陵县', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1997, '430281', '', '', '醴陵市', 1987, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1998, '430300', '', '湘潭市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (1999, '430301', '', '', '市辖区', 1998, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2000, '430302', '', '', '雨湖区', 1998, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2001, '430304', '', '', '岳塘区', 1998, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2002, '430321', '', '', '湘潭县', 1998, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2003, '430381', '', '', '湘乡市', 1998, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2004, '430382', '', '', '韶山市', 1998, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2005, '430400', '', '衡阳市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2006, '430401', '', '', '市辖区', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2007, '430405', '', '', '珠晖区', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2008, '430406', '', '', '雁峰区', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2009, '430407', '', '', '石鼓区', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2010, '430408', '', '', '蒸湘区', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2011, '430412', '', '', '南岳区', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2012, '430421', '', '', '衡阳县', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2013, '430422', '', '', '衡南县', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2014, '430423', '', '', '衡山县', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2015, '430424', '', '', '衡东县', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2016, '430426', '', '', '祁东县', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2017, '430481', '', '', '耒阳市', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2018, '430482', '', '', '常宁市', 2005, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2019, '430500', '', '邵阳市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2020, '430501', '', '', '市辖区', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2021, '430502', '', '', '双清区', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2022, '430503', '', '', '大祥区', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2023, '430511', '', '', '北塔区', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2024, '430521', '', '', '邵东县', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2025, '430522', '', '', '新邵县', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2026, '430523', '', '', '邵阳县', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2027, '430524', '', '', '隆回县', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2028, '430525', '', '', '洞口县', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2029, '430527', '', '', '绥宁县', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2030, '430528', '', '', '新宁县', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2031, '430529', '', '', '城步苗族自治县', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2032, '430581', '', '', '武冈市', 2019, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2033, '430600', '', '岳阳市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2034, '430601', '', '', '市辖区', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2035, '430602', '', '', '岳阳楼区', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2036, '430603', '', '', '云溪区', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2037, '430611', '', '', '君山区', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2038, '430621', '', '', '岳阳县', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2039, '430623', '', '', '华容县', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2040, '430624', '', '', '湘阴县', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2041, '430626', '', '', '平江县', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2042, '430681', '', '', '汨罗市', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2043, '430682', '', '', '临湘市', 2033, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2044, '430700', '', '常德市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2045, '430701', '', '', '市辖区', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2046, '430702', '', '', '武陵区', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2047, '430703', '', '', '鼎城区', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2048, '430721', '', '', '安乡县', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2049, '430722', '', '', '汉寿县', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2050, '430723', '', '', '澧县', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2051, '430724', '', '', '临澧县', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2052, '430725', '', '', '桃源县', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2053, '430726', '', '', '石门县', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2054, '430781', '', '', '津市市', 2044, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2055, '430800', '', '张家界市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2056, '430801', '', '', '市辖区', 2055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2057, '430802', '', '', '永定区', 2055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2058, '430811', '', '', '武陵源区', 2055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2059, '430821', '', '', '慈利县', 2055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2060, '430822', '', '', '桑植县', 2055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2061, '430900', '', '益阳市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2062, '430901', '', '', '市辖区', 2061, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2063, '430902', '', '', '资阳区', 2061, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2064, '430903', '', '', '赫山区', 2061, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2065, '430921', '', '', '南县', 2061, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2066, '430922', '', '', '桃江县', 2061, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2067, '430923', '', '', '安化县', 2061, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2068, '430981', '', '', '沅江市', 2061, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2069, '431000', '', '郴州市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2070, '431001', '', '', '市辖区', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2071, '431002', '', '', '北湖区', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2072, '431003', '', '', '苏仙区', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2073, '431021', '', '', '桂阳县', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2074, '431022', '', '', '宜章县', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2075, '431023', '', '', '永兴县', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2076, '431024', '', '', '嘉禾县', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2077, '431025', '', '', '临武县', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2078, '431026', '', '', '汝城县', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2079, '431027', '', '', '桂东县', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2080, '431028', '', '', '安仁县', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2081, '431081', '', '', '资兴市', 2069, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2082, '431100', '', '永州市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2083, '431101', '', '', '市辖区', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2084, '431102', '', '', '零陵区', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2085, '431103', '', '', '冷水滩区', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2086, '431121', '', '', '祁阳县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2087, '431122', '', '', '东安县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2088, '431123', '', '', '双牌县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2089, '431124', '', '', '道县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2090, '431125', '', '', '江永县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2091, '431126', '', '', '宁远县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2092, '431127', '', '', '蓝山县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2093, '431128', '', '', '新田县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2094, '431129', '', '', '江华瑶族自治县', 2082, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2095, '431200', '', '怀化市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2096, '431201', '', '', '市辖区', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2097, '431202', '', '', '鹤城区', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2098, '431221', '', '', '中方县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2099, '431222', '', '', '沅陵县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2100, '431223', '', '', '辰溪县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2101, '431224', '', '', '溆浦县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2102, '431225', '', '', '会同县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2103, '431226', '', '', '麻阳苗族自治县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2104, '431227', '', '', '新晃侗族自治县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2105, '431228', '', '', '芷江侗族自治县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2106, '431229', '', '', '靖州苗族侗族自治县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2107, '431230', '', '', '通道侗族自治县', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2108, '431281', '', '', '洪江市', 2095, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2109, '431300', '', '娄底市', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2110, '431301', '', '', '市辖区', 2109, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2111, '431302', '', '', '娄星区', 2109, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2112, '431321', '', '', '双峰县', 2109, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2113, '431322', '', '', '新化县', 2109, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2114, '431381', '', '', '冷水江市', 2109, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2115, '431382', '', '', '涟源市', 2109, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2116, '433100', '', '湘西土家族苗族自治州', '', 1975, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2117, '433101', '', '', '吉首市', 2116, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2118, '433122', '', '', '泸溪县', 2116, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2119, '433123', '', '', '凤凰县', 2116, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2120, '433124', '', '', '花垣县', 2116, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2121, '433125', '', '', '保靖县', 2116, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2122, '433126', '', '', '古丈县', 2116, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2123, '433127', '', '', '永顺县', 2116, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2124, '433130', '', '', '龙山县', 2116, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2125, '440000', '广东省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2126, '440100', '', '广州市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2127, '440101', '', '', '市辖区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2128, '440103', '', '', '荔湾区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2129, '440104', '', '', '越秀区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2130, '440105', '', '', '海珠区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2131, '440106', '', '', '天河区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2132, '440111', '', '', '白云区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2133, '440112', '', '', '黄埔区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2134, '440113', '', '', '番禺区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2135, '440114', '', '', '花都区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2136, '440115', '', '', '南沙区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2137, '440117', '', '', '从化区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2138, '440118', '', '', '增城区', 2126, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2139, '440200', '', '韶关市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2140, '440201', '', '', '市辖区', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2141, '440203', '', '', '武江区', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2142, '440204', '', '', '浈江区', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2143, '440205', '', '', '曲江区', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2144, '440222', '', '', '始兴县', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2145, '440224', '', '', '仁化县', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2146, '440229', '', '', '翁源县', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2147, '440232', '', '', '乳源瑶族自治县', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2148, '440233', '', '', '新丰县', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2149, '440281', '', '', '乐昌市', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2150, '440282', '', '', '南雄市', 2139, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2151, '440300', '', '深圳市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2152, '440301', '', '', '市辖区', 2151, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2153, '440303', '', '', '罗湖区', 2151, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2154, '440304', '', '', '福田区', 2151, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2155, '440305', '', '', '南山区', 2151, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2156, '440306', '', '', '宝安区', 2151, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2157, '440307', '', '', '龙岗区', 2151, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2158, '440308', '', '', '盐田区', 2151, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2159, '440400', '', '珠海市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2160, '440401', '', '', '市辖区', 2159, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2161, '440402', '', '', '香洲区', 2159, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2162, '440403', '', '', '斗门区', 2159, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2163, '440404', '', '', '金湾区', 2159, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2164, '440500', '', '汕头市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2165, '440501', '', '', '市辖区', 2164, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2166, '440507', '', '', '龙湖区', 2164, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2167, '440511', '', '', '金平区', 2164, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2168, '440512', '', '', '濠江区', 2164, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2169, '440513', '', '', '潮阳区', 2164, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2170, '440514', '', '', '潮南区', 2164, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2171, '440515', '', '', '澄海区', 2164, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2172, '440523', '', '', '南澳县', 2164, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2173, '440600', '', '佛山市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2174, '440601', '', '', '市辖区', 2173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2175, '440604', '', '', '禅城区', 2173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2176, '440605', '', '', '南海区', 2173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2177, '440606', '', '', '顺德区', 2173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2178, '440607', '', '', '三水区', 2173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2179, '440608', '', '', '高明区', 2173, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2180, '440700', '', '江门市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2181, '440701', '', '', '市辖区', 2180, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2182, '440703', '', '', '蓬江区', 2180, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2183, '440704', '', '', '江海区', 2180, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2184, '440705', '', '', '新会区', 2180, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2185, '440781', '', '', '台山市', 2180, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2186, '440783', '', '', '开平市', 2180, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2187, '440784', '', '', '鹤山市', 2180, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2188, '440785', '', '', '恩平市', 2180, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2189, '440800', '', '湛江市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2190, '440801', '', '', '市辖区', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2191, '440802', '', '', '赤坎区', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2192, '440803', '', '', '霞山区', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2193, '440804', '', '', '坡头区', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2194, '440811', '', '', '麻章区', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2195, '440823', '', '', '遂溪县', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2196, '440825', '', '', '徐闻县', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2197, '440881', '', '', '廉江市', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2198, '440882', '', '', '雷州市', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2199, '440883', '', '', '吴川市', 2189, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2200, '440900', '', '茂名市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2201, '440901', '', '', '市辖区', 2200, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2202, '440902', '', '', '茂南区', 2200, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2203, '440904', '', '', '电白区', 2200, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2204, '440981', '', '', '高州市', 2200, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2205, '440982', '', '', '化州市', 2200, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2206, '440983', '', '', '信宜市', 2200, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2207, '441200', '', '肇庆市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2208, '441201', '', '', '市辖区', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2209, '441202', '', '', '端州区', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2210, '441203', '', '', '鼎湖区', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2211, '441223', '', '', '广宁县', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2212, '441224', '', '', '怀集县', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2213, '441225', '', '', '封开县', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2214, '441226', '', '', '德庆县', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2215, '441283', '', '', '高要市', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2216, '441284', '', '', '四会市', 2207, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2217, '441300', '', '惠州市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2218, '441301', '', '', '市辖区', 2217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2219, '441302', '', '', '惠城区', 2217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2220, '441303', '', '', '惠阳区', 2217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2221, '441322', '', '', '博罗县', 2217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2222, '441323', '', '', '惠东县', 2217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2223, '441324', '', '', '龙门县', 2217, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2224, '441400', '', '梅州市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2225, '441401', '', '', '市辖区', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2226, '441402', '', '', '梅江区', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2227, '441403', '', '', '梅县区', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2228, '441422', '', '', '大埔县', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2229, '441423', '', '', '丰顺县', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2230, '441424', '', '', '五华县', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2231, '441426', '', '', '平远县', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2232, '441427', '', '', '蕉岭县', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2233, '441481', '', '', '兴宁市', 2224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2234, '441500', '', '汕尾市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2235, '441501', '', '', '市辖区', 2234, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2236, '441502', '', '', '城区', 2234, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2237, '441521', '', '', '海丰县', 2234, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2238, '441523', '', '', '陆河县', 2234, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2239, '441581', '', '', '陆丰市', 2234, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2240, '441600', '', '河源市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2241, '441601', '', '', '市辖区', 2240, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2242, '441602', '', '', '源城区', 2240, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2243, '441621', '', '', '紫金县', 2240, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2244, '441622', '', '', '龙川县', 2240, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2245, '441623', '', '', '连平县', 2240, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2246, '441624', '', '', '和平县', 2240, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2247, '441625', '', '', '东源县', 2240, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2248, '441700', '', '阳江市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2249, '441701', '', '', '市辖区', 2248, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2250, '441702', '', '', '江城区', 2248, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2251, '441704', '', '', '阳东区', 2248, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2252, '441721', '', '', '阳西县', 2248, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2253, '441781', '', '', '阳春市', 2248, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2254, '441800', '', '清远市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2255, '441801', '', '', '市辖区', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2256, '441802', '', '', '清城区', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2257, '441803', '', '', '清新区', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2258, '441821', '', '', '佛冈县', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2259, '441823', '', '', '阳山县', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2260, '441825', '', '', '连山壮族瑶族自治县', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2261, '441826', '', '', '连南瑶族自治县', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2262, '441881', '', '', '英德市', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2263, '441882', '', '', '连州市', 2254, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2264, '441900', '', '东莞市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2265, '442000', '', '中山市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2266, '445100', '', '潮州市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2267, '445101', '', '', '市辖区', 2266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2268, '445102', '', '', '湘桥区', 2266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2269, '445103', '', '', '潮安区', 2266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2270, '445122', '', '', '饶平县', 2266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2271, '445200', '', '揭阳市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2272, '445201', '', '', '市辖区', 2271, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2273, '445202', '', '', '榕城区', 2271, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2274, '445203', '', '', '揭东区', 2271, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2275, '445222', '', '', '揭西县', 2271, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2276, '445224', '', '', '惠来县', 2271, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2277, '445281', '', '', '普宁市', 2271, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2278, '445300', '', '云浮市', '', 2125, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2279, '445301', '', '', '市辖区', 2278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2280, '445302', '', '', '云城区', 2278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2281, '445303', '', '', '云安区', 2278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2282, '445321', '', '', '新兴县', 2278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2283, '445322', '', '', '郁南县', 2278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2284, '445381', '', '', '罗定市', 2278, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2285, '450000', '广西壮族自治区', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2286, '450100', '', '南宁市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2287, '450101', '', '', '市辖区', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2288, '450102', '', '', '兴宁区', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2289, '450103', '', '', '青秀区', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2290, '450105', '', '', '江南区', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2291, '450107', '', '', '西乡塘区', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2292, '450108', '', '', '良庆区', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2293, '450109', '', '', '邕宁区', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2294, '450122', '', '', '武鸣县', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2295, '450123', '', '', '隆安县', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2296, '450124', '', '', '马山县', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2297, '450125', '', '', '上林县', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2298, '450126', '', '', '宾阳县', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2299, '450127', '', '', '横县', 2286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2300, '450200', '', '柳州市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2301, '450201', '', '', '市辖区', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2302, '450202', '', '', '城中区', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2303, '450203', '', '', '鱼峰区', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2304, '450204', '', '', '柳南区', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2305, '450205', '', '', '柳北区', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2306, '450221', '', '', '柳江县', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2307, '450222', '', '', '柳城县', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2308, '450223', '', '', '鹿寨县', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2309, '450224', '', '', '融安县', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2310, '450225', '', '', '融水苗族自治县', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2311, '450226', '', '', '三江侗族自治县', 2300, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2312, '450300', '', '桂林市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2313, '450301', '', '', '市辖区', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2314, '450302', '', '', '秀峰区', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2315, '450303', '', '', '叠彩区', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2316, '450304', '', '', '象山区', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2317, '450305', '', '', '七星区', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2318, '450311', '', '', '雁山区', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2319, '450312', '', '', '临桂区', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2320, '450321', '', '', '阳朔县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2321, '450323', '', '', '灵川县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2322, '450324', '', '', '全州县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2323, '450325', '', '', '兴安县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2324, '450326', '', '', '永福县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2325, '450327', '', '', '灌阳县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2326, '450328', '', '', '龙胜各族自治县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2327, '450329', '', '', '资源县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2328, '450330', '', '', '平乐县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2329, '450331', '', '', '荔浦县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2330, '450332', '', '', '恭城瑶族自治县', 2312, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2331, '450400', '', '梧州市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2332, '450401', '', '', '市辖区', 2331, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2333, '450403', '', '', '万秀区', 2331, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2334, '450405', '', '', '长洲区', 2331, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2335, '450406', '', '', '龙圩区', 2331, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2336, '450421', '', '', '苍梧县', 2331, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2337, '450422', '', '', '藤县', 2331, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2338, '450423', '', '', '蒙山县', 2331, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2339, '450481', '', '', '岑溪市', 2331, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2340, '450500', '', '北海市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2341, '450501', '', '', '市辖区', 2340, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2342, '450502', '', '', '海城区', 2340, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2343, '450503', '', '', '银海区', 2340, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2344, '450512', '', '', '铁山港区', 2340, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2345, '450521', '', '', '合浦县', 2340, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2346, '450600', '', '防城港市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2347, '450601', '', '', '市辖区', 2346, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2348, '450602', '', '', '港口区', 2346, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2349, '450603', '', '', '防城区', 2346, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2350, '450621', '', '', '上思县', 2346, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2351, '450681', '', '', '东兴市', 2346, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2352, '450700', '', '钦州市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2353, '450701', '', '', '市辖区', 2352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2354, '450702', '', '', '钦南区', 2352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2355, '450703', '', '', '钦北区', 2352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2356, '450721', '', '', '灵山县', 2352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2357, '450722', '', '', '浦北县', 2352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2358, '450800', '', '贵港市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2359, '450801', '', '', '市辖区', 2358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2360, '450802', '', '', '港北区', 2358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2361, '450803', '', '', '港南区', 2358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2362, '450804', '', '', '覃塘区', 2358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2363, '450821', '', '', '平南县', 2358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2364, '450881', '', '', '桂平市', 2358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2365, '450900', '', '玉林市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2366, '450901', '', '', '市辖区', 2365, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2367, '450902', '', '', '玉州区', 2365, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2368, '450903', '', '', '福绵区', 2365, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2369, '450921', '', '', '容县', 2365, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2370, '450922', '', '', '陆川县', 2365, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2371, '450923', '', '', '博白县', 2365, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2372, '450924', '', '', '兴业县', 2365, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2373, '450981', '', '', '北流市', 2365, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2374, '451000', '', '百色市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2375, '451001', '', '', '市辖区', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2376, '451002', '', '', '右江区', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2377, '451021', '', '', '田阳县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2378, '451022', '', '', '田东县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2379, '451023', '', '', '平果县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2380, '451024', '', '', '德保县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2381, '451025', '', '', '靖西县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2382, '451026', '', '', '那坡县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2383, '451027', '', '', '凌云县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2384, '451028', '', '', '乐业县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2385, '451029', '', '', '田林县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2386, '451030', '', '', '西林县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2387, '451031', '', '', '隆林各族自治县', 2374, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2388, '451100', '', '贺州市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2389, '451101', '', '', '市辖区', 2388, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2390, '451102', '', '', '八步区', 2388, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2391, '451119', '', '', '平桂管理区', 2388, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2392, '451121', '', '', '昭平县', 2388, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2393, '451122', '', '', '钟山县', 2388, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2394, '451123', '', '', '富川瑶族自治县', 2388, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2395, '451200', '', '河池市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2396, '451201', '', '', '市辖区', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2397, '451202', '', '', '金城江区', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2398, '451221', '', '', '南丹县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2399, '451222', '', '', '天峨县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2400, '451223', '', '', '凤山县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2401, '451224', '', '', '东兰县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2402, '451225', '', '', '罗城仫佬族自治县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2403, '451226', '', '', '环江毛南族自治县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2404, '451227', '', '', '巴马瑶族自治县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2405, '451228', '', '', '都安瑶族自治县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2406, '451229', '', '', '大化瑶族自治县', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2407, '451281', '', '', '宜州市', 2395, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2408, '451300', '', '来宾市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2409, '451301', '', '', '市辖区', 2408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2410, '451302', '', '', '兴宾区', 2408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2411, '451321', '', '', '忻城县', 2408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2412, '451322', '', '', '象州县', 2408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2413, '451323', '', '', '武宣县', 2408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2414, '451324', '', '', '金秀瑶族自治县', 2408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2415, '451381', '', '', '合山市', 2408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2416, '451400', '', '崇左市', '', 2285, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2417, '451401', '', '', '市辖区', 2416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2418, '451402', '', '', '江州区', 2416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2419, '451421', '', '', '扶绥县', 2416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2420, '451422', '', '', '宁明县', 2416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2421, '451423', '', '', '龙州县', 2416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2422, '451424', '', '', '大新县', 2416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2423, '451425', '', '', '天等县', 2416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2424, '451481', '', '', '凭祥市', 2416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2425, '460000', '海南省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2426, '460100', '', '海口市', '', 2425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2427, '460101', '', '', '市辖区', 2426, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2428, '460105', '', '', '秀英区', 2426, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2429, '460106', '', '', '龙华区', 2426, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2430, '460107', '', '', '琼山区', 2426, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2431, '460108', '', '', '美兰区', 2426, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2432, '460200', '', '三亚市', '', 2425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2433, '460201', '', '', '市辖区', 2432, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2434, '460202', '', '', '海棠区', 2432, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2435, '460203', '', '', '吉阳区', 2432, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2436, '460204', '', '', '天涯区', 2432, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2437, '460205', '', '', '崖州区', 2432, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2438, '460300', '', '三沙市', '', 2425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2439, '460321', '', '', '西沙群岛', 2438, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2440, '460322', '', '', '南沙群岛', 2438, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2441, '460323', '', '', '中沙群岛的岛礁及其海域', 2438, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2442, '469000', '', '省直辖县级行政区划', '', 2425, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2443, '469001', '', '', '五指山市', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2444, '469002', '', '', '琼海市', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2445, '469003', '', '', '儋州市', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2446, '469005', '', '', '文昌市', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2447, '469006', '', '', '万宁市', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2448, '469007', '', '', '东方市', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2449, '469021', '', '', '定安县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2450, '469022', '', '', '屯昌县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2451, '469023', '', '', '澄迈县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2452, '469024', '', '', '临高县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2453, '469025', '', '', '白沙黎族自治县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2454, '469026', '', '', '昌江黎族自治县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2455, '469027', '', '', '乐东黎族自治县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2456, '469028', '', '', '陵水黎族自治县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2457, '469029', '', '', '保亭黎族苗族自治县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2458, '469030', '', '', '琼中黎族苗族自治县', 2442, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2459, '500000', '', '重庆市', '', 2, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2460, '500101', '', '', '万州区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2461, '500102', '', '', '涪陵区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2462, '500103', '', '', '渝中区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2463, '500104', '', '', '大渡口区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2464, '500105', '', '', '江北区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2465, '500106', '', '', '沙坪坝区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2466, '500107', '', '', '九龙坡区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2467, '500108', '', '', '南岸区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2468, '500109', '', '', '北碚区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2469, '500110', '', '', '綦江区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2470, '500111', '', '', '大足区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2471, '500112', '', '', '渝北区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2472, '500113', '', '', '巴南区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2473, '500114', '', '', '黔江区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2474, '500115', '', '', '长寿区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2475, '500116', '', '', '江津区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2476, '500117', '', '', '合川区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2477, '500118', '', '', '永川区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2478, '500119', '', '', '南川区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2479, '500120', '', '', '璧山区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2480, '500151', '', '', '铜梁区', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2481, '500223', '', '', '潼南县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2482, '500226', '', '', '荣昌县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2483, '500228', '', '', '梁平县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2484, '500229', '', '', '城口县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2485, '500230', '', '', '丰都县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2486, '500231', '', '', '垫江县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2487, '500232', '', '', '武隆县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2488, '500233', '', '', '忠县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2489, '500234', '', '', '开县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2490, '500235', '', '', '云阳县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2491, '500236', '', '', '奉节县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2492, '500237', '', '', '巫山县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2493, '500238', '', '', '巫溪县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2494, '500240', '', '', '石柱土家族自治县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2495, '500241', '', '', '秀山土家族苗族自治县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2496, '500242', '', '', '酉阳土家族苗族自治县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2497, '500243', '', '', '彭水苗族土家族自治县', 2459, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2498, '510000', '四川省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2499, '510100', '', '成都市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2500, '510101', '', '', '市辖区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2501, '510104', '', '', '锦江区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2502, '510105', '', '', '青羊区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2503, '510106', '', '', '金牛区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2504, '510107', '', '', '武侯区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2505, '510108', '', '', '成华区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2506, '510112', '', '', '龙泉驿区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2507, '510113', '', '', '青白江区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2508, '510114', '', '', '新都区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2509, '510115', '', '', '温江区', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2510, '510121', '', '', '金堂县', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2511, '510122', '', '', '双流县', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2512, '510124', '', '', '郫县', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2513, '510129', '', '', '大邑县', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2514, '510131', '', '', '蒲江县', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2515, '510132', '', '', '新津县', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2516, '510181', '', '', '都江堰市', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2517, '510182', '', '', '彭州市', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2518, '510183', '', '', '邛崃市', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2519, '510184', '', '', '崇州市', 2499, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2520, '510300', '', '自贡市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2521, '510301', '', '', '市辖区', 2520, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2522, '510302', '', '', '自流井区', 2520, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2523, '510303', '', '', '贡井区', 2520, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2524, '510304', '', '', '大安区', 2520, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2525, '510311', '', '', '沿滩区', 2520, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2526, '510321', '', '', '荣县', 2520, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2527, '510322', '', '', '富顺县', 2520, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2528, '510400', '', '攀枝花市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2529, '510401', '', '', '市辖区', 2528, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2530, '510402', '', '', '东区', 2528, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2531, '510403', '', '', '西区', 2528, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2532, '510411', '', '', '仁和区', 2528, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2533, '510421', '', '', '米易县', 2528, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2534, '510422', '', '', '盐边县', 2528, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2535, '510500', '', '泸州市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2536, '510501', '', '', '市辖区', 2535, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2537, '510502', '', '', '江阳区', 2535, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2538, '510503', '', '', '纳溪区', 2535, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2539, '510504', '', '', '龙马潭区', 2535, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2540, '510521', '', '', '泸县', 2535, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2541, '510522', '', '', '合江县', 2535, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2542, '510524', '', '', '叙永县', 2535, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2543, '510525', '', '', '古蔺县', 2535, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2544, '510600', '', '德阳市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2545, '510601', '', '', '市辖区', 2544, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2546, '510603', '', '', '旌阳区', 2544, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2547, '510623', '', '', '中江县', 2544, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2548, '510626', '', '', '罗江县', 2544, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2549, '510681', '', '', '广汉市', 2544, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2550, '510682', '', '', '什邡市', 2544, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2551, '510683', '', '', '绵竹市', 2544, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2552, '510700', '', '绵阳市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2553, '510701', '', '', '市辖区', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2554, '510703', '', '', '涪城区', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2555, '510704', '', '', '游仙区', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2556, '510722', '', '', '三台县', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2557, '510723', '', '', '盐亭县', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2558, '510724', '', '', '安县', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2559, '510725', '', '', '梓潼县', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2560, '510726', '', '', '北川羌族自治县', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2561, '510727', '', '', '平武县', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2562, '510781', '', '', '江油市', 2552, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2563, '510800', '', '广元市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2564, '510801', '', '', '市辖区', 2563, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2565, '510802', '', '', '利州区', 2563, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2566, '510811', '', '', '昭化区', 2563, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2567, '510812', '', '', '朝天区', 2563, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2568, '510821', '', '', '旺苍县', 2563, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2569, '510822', '', '', '青川县', 2563, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2570, '510823', '', '', '剑阁县', 2563, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2571, '510824', '', '', '苍溪县', 2563, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2572, '510900', '', '遂宁市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2573, '510901', '', '', '市辖区', 2572, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2574, '510903', '', '', '船山区', 2572, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2575, '510904', '', '', '安居区', 2572, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2576, '510921', '', '', '蓬溪县', 2572, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2577, '510922', '', '', '射洪县', 2572, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2578, '510923', '', '', '大英县', 2572, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2579, '511000', '', '内江市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2580, '511001', '', '', '市辖区', 2579, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2581, '511002', '', '', '市中区', 2579, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2582, '511011', '', '', '东兴区', 2579, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2583, '511024', '', '', '威远县', 2579, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2584, '511025', '', '', '资中县', 2579, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2585, '511028', '', '', '隆昌县', 2579, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2586, '511100', '', '乐山市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2587, '511101', '', '', '市辖区', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2588, '511102', '', '', '市中区', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2589, '511111', '', '', '沙湾区', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2590, '511112', '', '', '五通桥区', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2591, '511113', '', '', '金口河区', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2592, '511123', '', '', '犍为县', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2593, '511124', '', '', '井研县', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2594, '511126', '', '', '夹江县', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2595, '511129', '', '', '沐川县', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2596, '511132', '', '', '峨边彝族自治县', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2597, '511133', '', '', '马边彝族自治县', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2598, '511181', '', '', '峨眉山市', 2586, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2599, '511300', '', '南充市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2600, '511301', '', '', '市辖区', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2601, '511302', '', '', '顺庆区', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2602, '511303', '', '', '高坪区', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2603, '511304', '', '', '嘉陵区', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2604, '511321', '', '', '南部县', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2605, '511322', '', '', '营山县', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2606, '511323', '', '', '蓬安县', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2607, '511324', '', '', '仪陇县', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2608, '511325', '', '', '西充县', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2609, '511381', '', '', '阆中市', 2599, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2610, '511400', '', '眉山市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2611, '511401', '', '', '市辖区', 2610, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2612, '511402', '', '', '东坡区', 2610, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2613, '511403', '', '', '彭山区', 2610, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2614, '511421', '', '', '仁寿县', 2610, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2615, '511423', '', '', '洪雅县', 2610, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2616, '511424', '', '', '丹棱县', 2610, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2617, '511425', '', '', '青神县', 2610, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2618, '511500', '', '宜宾市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2619, '511501', '', '', '市辖区', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2620, '511502', '', '', '翠屏区', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2621, '511503', '', '', '南溪区', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2622, '511521', '', '', '宜宾县', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2623, '511523', '', '', '江安县', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2624, '511524', '', '', '长宁县', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2625, '511525', '', '', '高县', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2626, '511526', '', '', '珙县', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2627, '511527', '', '', '筠连县', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2628, '511528', '', '', '兴文县', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2629, '511529', '', '', '屏山县', 2618, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2630, '511600', '', '广安市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2631, '511601', '', '', '市辖区', 2630, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2632, '511602', '', '', '广安区', 2630, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2633, '511603', '', '', '前锋区', 2630, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2634, '511621', '', '', '岳池县', 2630, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2635, '511622', '', '', '武胜县', 2630, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2636, '511623', '', '', '邻水县', 2630, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2637, '511681', '', '', '华蓥市', 2630, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2638, '511700', '', '达州市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2639, '511701', '', '', '市辖区', 2638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2640, '511702', '', '', '通川区', 2638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2641, '511703', '', '', '达川区', 2638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2642, '511722', '', '', '宣汉县', 2638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2643, '511723', '', '', '开江县', 2638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2644, '511724', '', '', '大竹县', 2638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2645, '511725', '', '', '渠县', 2638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2646, '511781', '', '', '万源市', 2638, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2647, '511800', '', '雅安市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2648, '511801', '', '', '市辖区', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2649, '511802', '', '', '雨城区', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2650, '511803', '', '', '名山区', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2651, '511822', '', '', '荥经县', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2652, '511823', '', '', '汉源县', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2653, '511824', '', '', '石棉县', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2654, '511825', '', '', '天全县', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2655, '511826', '', '', '芦山县', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2656, '511827', '', '', '宝兴县', 2647, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2657, '511900', '', '巴中市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2658, '511901', '', '', '市辖区', 2657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2659, '511902', '', '', '巴州区', 2657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2660, '511903', '', '', '恩阳区', 2657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2661, '511921', '', '', '通江县', 2657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2662, '511922', '', '', '南江县', 2657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2663, '511923', '', '', '平昌县', 2657, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2664, '512000', '', '资阳市', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2665, '512001', '', '', '市辖区', 2664, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2666, '512002', '', '', '雁江区', 2664, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2667, '512021', '', '', '安岳县', 2664, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2668, '512022', '', '', '乐至县', 2664, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2669, '512081', '', '', '简阳市', 2664, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2670, '513200', '', '阿坝藏族羌族自治州', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2671, '513221', '', '', '汶川县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2672, '513222', '', '', '理县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2673, '513223', '', '', '茂县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2674, '513224', '', '', '松潘县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2675, '513225', '', '', '九寨沟县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2676, '513226', '', '', '金川县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2677, '513227', '', '', '小金县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2678, '513228', '', '', '黑水县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2679, '513229', '', '', '马尔康县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2680, '513230', '', '', '壤塘县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2681, '513231', '', '', '阿坝县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2682, '513232', '', '', '若尔盖县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2683, '513233', '', '', '红原县', 2670, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2684, '513300', '', '甘孜藏族自治州', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2685, '513321', '', '', '康定县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2686, '513322', '', '', '泸定县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2687, '513323', '', '', '丹巴县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2688, '513324', '', '', '九龙县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2689, '513325', '', '', '雅江县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2690, '513326', '', '', '道孚县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2691, '513327', '', '', '炉霍县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2692, '513328', '', '', '甘孜县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2693, '513329', '', '', '新龙县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2694, '513330', '', '', '德格县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2695, '513331', '', '', '白玉县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2696, '513332', '', '', '石渠县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2697, '513333', '', '', '色达县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2698, '513334', '', '', '理塘县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2699, '513335', '', '', '巴塘县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2700, '513336', '', '', '乡城县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2701, '513337', '', '', '稻城县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2702, '513338', '', '', '得荣县', 2684, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2703, '513400', '', '凉山彝族自治州', '', 2498, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2704, '513401', '', '', '西昌市', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2705, '513422', '', '', '木里藏族自治县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2706, '513423', '', '', '盐源县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2707, '513424', '', '', '德昌县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2708, '513425', '', '', '会理县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2709, '513426', '', '', '会东县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2710, '513427', '', '', '宁南县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2711, '513428', '', '', '普格县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2712, '513429', '', '', '布拖县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2713, '513430', '', '', '金阳县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2714, '513431', '', '', '昭觉县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2715, '513432', '', '', '喜德县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2716, '513433', '', '', '冕宁县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2717, '513434', '', '', '越西县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2718, '513435', '', '', '甘洛县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2719, '513436', '', '', '美姑县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2720, '513437', '', '', '雷波县', 2703, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2721, '520000', '贵州省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2722, '520100', '', '贵阳市', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2723, '520101', '', '', '市辖区', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2724, '520102', '', '', '南明区', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2725, '520103', '', '', '云岩区', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2726, '520111', '', '', '花溪区', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2727, '520112', '', '', '乌当区', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2728, '520113', '', '', '白云区', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2729, '520115', '', '', '观山湖区', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2730, '520121', '', '', '开阳县', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2731, '520122', '', '', '息烽县', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2732, '520123', '', '', '修文县', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2733, '520181', '', '', '清镇市', 2722, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2734, '520200', '', '六盘水市', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2735, '520201', '', '', '钟山区', 2734, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2736, '520203', '', '', '六枝特区', 2734, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2737, '520221', '', '', '水城县', 2734, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2738, '520222', '', '', '盘县', 2734, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2739, '520300', '', '遵义市', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2740, '520301', '', '', '市辖区', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2741, '520302', '', '', '红花岗区', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2742, '520303', '', '', '汇川区', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2743, '520321', '', '', '遵义县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2744, '520322', '', '', '桐梓县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2745, '520323', '', '', '绥阳县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2746, '520324', '', '', '正安县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2747, '520325', '', '', '道真仡佬族苗族自治县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2748, '520326', '', '', '务川仡佬族苗族自治县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2749, '520327', '', '', '凤冈县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2750, '520328', '', '', '湄潭县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2751, '520329', '', '', '余庆县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2752, '520330', '', '', '习水县', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2753, '520381', '', '', '赤水市', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2754, '520382', '', '', '仁怀市', 2739, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2755, '520400', '', '安顺市', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2756, '520401', '', '', '市辖区', 2755, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2757, '520402', '', '', '西秀区', 2755, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2758, '520403', '', '', '平坝区', 2755, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2759, '520422', '', '', '普定县', 2755, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2760, '520423', '', '', '镇宁布依族苗族自治县', 2755, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2761, '520424', '', '', '关岭布依族苗族自治县', 2755, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2762, '520425', '', '', '紫云苗族布依族自治县', 2755, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2763, '520500', '', '毕节市', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2764, '520501', '', '', '市辖区', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2765, '520502', '', '', '七星关区', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2766, '520521', '', '', '大方县', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2767, '520522', '', '', '黔西县', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2768, '520523', '', '', '金沙县', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2769, '520524', '', '', '织金县', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2770, '520525', '', '', '纳雍县', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2771, '520526', '', '', '威宁彝族回族苗族自治县', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2772, '520527', '', '', '赫章县', 2763, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2773, '520600', '', '铜仁市', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2774, '520601', '', '', '市辖区', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2775, '520602', '', '', '碧江区', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2776, '520603', '', '', '万山区', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2777, '520621', '', '', '江口县', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2778, '520622', '', '', '玉屏侗族自治县', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2779, '520623', '', '', '石阡县', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2780, '520624', '', '', '思南县', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2781, '520625', '', '', '印江土家族苗族自治县', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2782, '520626', '', '', '德江县', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2783, '520627', '', '', '沿河土家族自治县', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2784, '520628', '', '', '松桃苗族自治县', 2773, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2785, '522300', '', '黔西南布依族苗族自治州', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2786, '522301', '', '', '兴义市', 2785, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2787, '522322', '', '', '兴仁县', 2785, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2788, '522323', '', '', '普安县', 2785, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2789, '522324', '', '', '晴隆县', 2785, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2790, '522325', '', '', '贞丰县', 2785, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2791, '522326', '', '', '望谟县', 2785, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2792, '522327', '', '', '册亨县', 2785, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2793, '522328', '', '', '安龙县', 2785, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2794, '522600', '', '黔东南苗族侗族自治州', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2795, '522601', '', '', '凯里市', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2796, '522622', '', '', '黄平县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2797, '522623', '', '', '施秉县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2798, '522624', '', '', '三穗县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2799, '522625', '', '', '镇远县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2800, '522626', '', '', '岑巩县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2801, '522627', '', '', '天柱县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2802, '522628', '', '', '锦屏县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2803, '522629', '', '', '剑河县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2804, '522630', '', '', '台江县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2805, '522631', '', '', '黎平县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2806, '522632', '', '', '榕江县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2807, '522633', '', '', '从江县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2808, '522634', '', '', '雷山县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2809, '522635', '', '', '麻江县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2810, '522636', '', '', '丹寨县', 2794, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2811, '522700', '', '黔南布依族苗族自治州', '', 2721, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2812, '522701', '', '', '都匀市', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2813, '522702', '', '', '福泉市', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2814, '522722', '', '', '荔波县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2815, '522723', '', '', '贵定县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2816, '522725', '', '', '瓮安县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2817, '522726', '', '', '独山县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2818, '522727', '', '', '平塘县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2819, '522728', '', '', '罗甸县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2820, '522729', '', '', '长顺县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2821, '522730', '', '', '龙里县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2822, '522731', '', '', '惠水县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2823, '522732', '', '', '三都水族自治县', 2811, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2824, '530000', '云南省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2825, '530100', '', '昆明市', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2826, '530101', '', '', '市辖区', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2827, '530102', '', '', '五华区', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2828, '530103', '', '', '盘龙区', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2829, '530111', '', '', '官渡区', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2830, '530112', '', '', '西山区', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2831, '530113', '', '', '东川区', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2832, '530114', '', '', '呈贡区', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2833, '530122', '', '', '晋宁县', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2834, '530124', '', '', '富民县', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2835, '530125', '', '', '宜良县', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2836, '530126', '', '', '石林彝族自治县', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2837, '530127', '', '', '嵩明县', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2838, '530128', '', '', '禄劝彝族苗族自治县', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2839, '530129', '', '', '寻甸回族彝族自治县', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2840, '530181', '', '', '安宁市', 2825, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2841, '530300', '', '曲靖市', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2842, '530301', '', '', '市辖区', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2843, '530302', '', '', '麒麟区', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2844, '530321', '', '', '马龙县', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2845, '530322', '', '', '陆良县', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2846, '530323', '', '', '师宗县', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2847, '530324', '', '', '罗平县', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2848, '530325', '', '', '富源县', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2849, '530326', '', '', '会泽县', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2850, '530328', '', '', '沾益县', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2851, '530381', '', '', '宣威市', 2841, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2852, '530400', '', '玉溪市', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2853, '530401', '', '', '市辖区', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2854, '530402', '', '', '红塔区', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2855, '530421', '', '', '江川县', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2856, '530422', '', '', '澄江县', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2857, '530423', '', '', '通海县', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2858, '530424', '', '', '华宁县', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2859, '530425', '', '', '易门县', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2860, '530426', '', '', '峨山彝族自治县', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2861, '530427', '', '', '新平彝族傣族自治县', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2862, '530428', '', '', '元江哈尼族彝族傣族自治县', 2852, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2863, '530500', '', '保山市', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2864, '530501', '', '', '市辖区', 2863, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2865, '530502', '', '', '隆阳区', 2863, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2866, '530521', '', '', '施甸县', 2863, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2867, '530522', '', '', '腾冲县', 2863, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2868, '530523', '', '', '龙陵县', 2863, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2869, '530524', '', '', '昌宁县', 2863, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2870, '530600', '', '昭通市', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2871, '530601', '', '', '市辖区', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2872, '530602', '', '', '昭阳区', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2873, '530621', '', '', '鲁甸县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2874, '530622', '', '', '巧家县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2875, '530623', '', '', '盐津县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2876, '530624', '', '', '大关县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2877, '530625', '', '', '永善县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2878, '530626', '', '', '绥江县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2879, '530627', '', '', '镇雄县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2880, '530628', '', '', '彝良县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2881, '530629', '', '', '威信县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2882, '530630', '', '', '水富县', 2870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2883, '530700', '', '丽江市', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2884, '530701', '', '', '市辖区', 2883, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2885, '530702', '', '', '古城区', 2883, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2886, '530721', '', '', '玉龙纳西族自治县', 2883, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2887, '530722', '', '', '永胜县', 2883, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2888, '530723', '', '', '华坪县', 2883, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2889, '530724', '', '', '宁蒗彝族自治县', 2883, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2890, '530800', '', '普洱市', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2891, '530801', '', '', '市辖区', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2892, '530802', '', '', '思茅区', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2893, '530821', '', '', '宁洱哈尼族彝族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2894, '530822', '', '', '墨江哈尼族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2895, '530823', '', '', '景东彝族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2896, '530824', '', '', '景谷傣族彝族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2897, '530825', '', '', '镇沅彝族哈尼族拉祜族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2898, '530826', '', '', '江城哈尼族彝族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2899, '530827', '', '', '孟连傣族拉祜族佤族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2900, '530828', '', '', '澜沧拉祜族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2901, '530829', '', '', '西盟佤族自治县', 2890, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2902, '530900', '', '临沧市', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2903, '530901', '', '', '市辖区', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2904, '530902', '', '', '临翔区', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2905, '530921', '', '', '凤庆县', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2906, '530922', '', '', '云县', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2907, '530923', '', '', '永德县', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2908, '530924', '', '', '镇康县', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2909, '530925', '', '', '双江拉祜族佤族布朗族傣族自治县', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2910, '530926', '', '', '耿马傣族佤族自治县', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2911, '530927', '', '', '沧源佤族自治县', 2902, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2912, '532300', '', '楚雄彝族自治州', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2913, '532301', '', '', '楚雄市', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2914, '532322', '', '', '双柏县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2915, '532323', '', '', '牟定县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2916, '532324', '', '', '南华县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2917, '532325', '', '', '姚安县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2918, '532326', '', '', '大姚县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2919, '532327', '', '', '永仁县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2920, '532328', '', '', '元谋县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2921, '532329', '', '', '武定县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2922, '532331', '', '', '禄丰县', 2912, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2923, '532500', '', '红河哈尼族彝族自治州', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2924, '532501', '', '', '个旧市', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2925, '532502', '', '', '开远市', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2926, '532503', '', '', '蒙自市', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2927, '532504', '', '', '弥勒市', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2928, '532523', '', '', '屏边苗族自治县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2929, '532524', '', '', '建水县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2930, '532525', '', '', '石屏县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2931, '532527', '', '', '泸西县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2932, '532528', '', '', '元阳县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2933, '532529', '', '', '红河县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2934, '532530', '', '', '金平苗族瑶族傣族自治县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2935, '532531', '', '', '绿春县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2936, '532532', '', '', '河口瑶族自治县', 2923, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2937, '532600', '', '文山壮族苗族自治州', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2938, '532601', '', '', '文山市', 2937, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2939, '532622', '', '', '砚山县', 2937, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2940, '532623', '', '', '西畴县', 2937, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2941, '532624', '', '', '麻栗坡县', 2937, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2942, '532625', '', '', '马关县', 2937, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2943, '532626', '', '', '丘北县', 2937, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2944, '532627', '', '', '广南县', 2937, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2945, '532628', '', '', '富宁县', 2937, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2946, '532800', '', '西双版纳傣族自治州', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2947, '532801', '', '', '景洪市', 2946, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2948, '532822', '', '', '勐海县', 2946, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2949, '532823', '', '', '勐腊县', 2946, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2950, '532900', '', '大理白族自治州', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2951, '532901', '', '', '大理市', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2952, '532922', '', '', '漾濞彝族自治县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2953, '532923', '', '', '祥云县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2954, '532924', '', '', '宾川县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2955, '532925', '', '', '弥渡县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2956, '532926', '', '', '南涧彝族自治县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2957, '532927', '', '', '巍山彝族回族自治县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2958, '532928', '', '', '永平县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2959, '532929', '', '', '云龙县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2960, '532930', '', '', '洱源县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2961, '532931', '', '', '剑川县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2962, '532932', '', '', '鹤庆县', 2950, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2963, '533100', '', '德宏傣族景颇族自治州', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2964, '533102', '', '', '瑞丽市', 2963, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2965, '533103', '', '', '芒市', 2963, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2966, '533122', '', '', '梁河县', 2963, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2967, '533123', '', '', '盈江县', 2963, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2968, '533124', '', '', '陇川县', 2963, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2969, '533300', '', '怒江傈僳族自治州', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2970, '533321', '', '', '泸水县', 2969, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2971, '533323', '', '', '福贡县', 2969, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2972, '533324', '', '', '贡山独龙族怒族自治县', 2969, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2973, '533325', '', '', '兰坪白族普米族自治县', 2969, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2974, '533400', '', '迪庆藏族自治州', '', 2824, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2975, '533401', '', '', '香格里拉市', 2974, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2976, '533422', '', '', '德钦县', 2974, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2977, '533423', '', '', '维西傈僳族自治县', 2974, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2978, '540000', '西藏自治区', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2979, '540100', '', '拉萨市', '', 2978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2980, '540101', '', '', '市辖区', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2981, '540102', '', '', '城关区', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2982, '540121', '', '', '林周县', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2983, '540122', '', '', '当雄县', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2984, '540123', '', '', '尼木县', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2985, '540124', '', '', '曲水县', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2986, '540125', '', '', '堆龙德庆县', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2987, '540126', '', '', '达孜县', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2988, '540127', '', '', '墨竹工卡县', 2979, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2989, '540200', '', '日喀则市', '', 2978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2990, '540201', '', '', '市辖区', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2991, '540202', '', '', '桑珠孜区', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2992, '540221', '', '', '南木林县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2993, '540222', '', '', '江孜县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2994, '540223', '', '', '定日县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2995, '540224', '', '', '萨迦县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2996, '540225', '', '', '拉孜县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2997, '540226', '', '', '昂仁县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2998, '540227', '', '', '谢通门县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (2999, '540228', '', '', '白朗县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3000, '540229', '', '', '仁布县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3001, '540230', '', '', '康马县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3002, '540231', '', '', '定结县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3003, '540232', '', '', '仲巴县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3004, '540233', '', '', '亚东县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3005, '540234', '', '', '吉隆县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3006, '540235', '', '', '聂拉木县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3007, '540236', '', '', '萨嘎县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3008, '540237', '', '', '岗巴县', 2989, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3009, '540300', '', '昌都市', '', 2978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3010, '540301', '', '', '市辖区', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3011, '540302', '', '', '卡若区', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3012, '540321', '', '', '江达县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3013, '540322', '', '', '贡觉县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3014, '540323', '', '', '类乌齐县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3015, '540324', '', '', '丁青县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3016, '540325', '', '', '察雅县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3017, '540326', '', '', '八宿县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3018, '540327', '', '', '左贡县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3019, '540328', '', '', '芒康县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3020, '540329', '', '', '洛隆县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3021, '540330', '', '', '边坝县', 3009, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3022, '542200', '', '山南地区', '', 2978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3023, '542221', '', '', '乃东县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3024, '542222', '', '', '扎囊县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3025, '542223', '', '', '贡嘎县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3026, '542224', '', '', '桑日县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3027, '542225', '', '', '琼结县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3028, '542226', '', '', '曲松县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3029, '542227', '', '', '措美县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3030, '542228', '', '', '洛扎县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3031, '542229', '', '', '加查县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3032, '542231', '', '', '隆子县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3033, '542232', '', '', '错那县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3034, '542233', '', '', '浪卡子县', 3022, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3035, '542400', '', '那曲地区', '', 2978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3036, '542421', '', '', '那曲县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3037, '542422', '', '', '嘉黎县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3038, '542423', '', '', '比如县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3039, '542424', '', '', '聂荣县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3040, '542425', '', '', '安多县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3041, '542426', '', '', '申扎县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3042, '542427', '', '', '索县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3043, '542428', '', '', '班戈县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3044, '542429', '', '', '巴青县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3045, '542430', '', '', '尼玛县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3046, '542431', '', '', '双湖县', 3035, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3047, '542500', '', '阿里地区', '', 2978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3048, '542521', '', '', '普兰县', 3047, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3049, '542522', '', '', '札达县', 3047, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3050, '542523', '', '', '噶尔县', 3047, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3051, '542524', '', '', '日土县', 3047, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3052, '542525', '', '', '革吉县', 3047, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3053, '542526', '', '', '改则县', 3047, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3054, '542527', '', '', '措勤县', 3047, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3055, '542600', '', '林芝地区', '', 2978, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3056, '542621', '', '', '林芝县', 3055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3057, '542622', '', '', '工布江达县', 3055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3058, '542623', '', '', '米林县', 3055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3059, '542624', '', '', '墨脱县', 3055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3060, '542625', '', '', '波密县', 3055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3061, '542626', '', '', '察隅县', 3055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3062, '542627', '', '', '朗县', 3055, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3063, '610000', '陕西省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3064, '610100', '', '西安市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3065, '610101', '', '', '市辖区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3066, '610102', '', '', '新城区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3067, '610103', '', '', '碑林区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3068, '610104', '', '', '莲湖区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3069, '610111', '', '', '灞桥区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3070, '610112', '', '', '未央区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3071, '610113', '', '', '雁塔区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3072, '610114', '', '', '阎良区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3073, '610115', '', '', '临潼区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3074, '610116', '', '', '长安区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3075, '610117', '', '', '高陵区', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3076, '610122', '', '', '蓝田县', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3077, '610124', '', '', '周至县', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3078, '610125', '', '', '户县', 3064, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3079, '610200', '', '铜川市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3080, '610201', '', '', '市辖区', 3079, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3081, '610202', '', '', '王益区', 3079, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3082, '610203', '', '', '印台区', 3079, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3083, '610204', '', '', '耀州区', 3079, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3084, '610222', '', '', '宜君县', 3079, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3085, '610300', '', '宝鸡市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3086, '610301', '', '', '市辖区', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3087, '610302', '', '', '渭滨区', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3088, '610303', '', '', '金台区', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3089, '610304', '', '', '陈仓区', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3090, '610322', '', '', '凤翔县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3091, '610323', '', '', '岐山县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3092, '610324', '', '', '扶风县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3093, '610326', '', '', '眉县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3094, '610327', '', '', '陇县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3095, '610328', '', '', '千阳县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3096, '610329', '', '', '麟游县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3097, '610330', '', '', '凤县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3098, '610331', '', '', '太白县', 3085, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3099, '610400', '', '咸阳市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3100, '610401', '', '', '市辖区', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3101, '610402', '', '', '秦都区', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3102, '610403', '', '', '杨陵区', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3103, '610404', '', '', '渭城区', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3104, '610422', '', '', '三原县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3105, '610423', '', '', '泾阳县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3106, '610424', '', '', '乾县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3107, '610425', '', '', '礼泉县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3108, '610426', '', '', '永寿县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3109, '610427', '', '', '彬县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3110, '610428', '', '', '长武县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3111, '610429', '', '', '旬邑县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3112, '610430', '', '', '淳化县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3113, '610431', '', '', '武功县', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3114, '610481', '', '', '兴平市', 3099, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3115, '610500', '', '渭南市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3116, '610501', '', '', '市辖区', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3117, '610502', '', '', '临渭区', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3118, '610521', '', '', '华县', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3119, '610522', '', '', '潼关县', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3120, '610523', '', '', '大荔县', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3121, '610524', '', '', '合阳县', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3122, '610525', '', '', '澄城县', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3123, '610526', '', '', '蒲城县', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3124, '610527', '', '', '白水县', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3125, '610528', '', '', '富平县', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3126, '610581', '', '', '韩城市', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3127, '610582', '', '', '华阴市', 3115, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3128, '610600', '', '延安市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3129, '610601', '', '', '市辖区', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3130, '610602', '', '', '宝塔区', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3131, '610621', '', '', '延长县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3132, '610622', '', '', '延川县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3133, '610623', '', '', '子长县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3134, '610624', '', '', '安塞县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3135, '610625', '', '', '志丹县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3136, '610626', '', '', '吴起县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3137, '610627', '', '', '甘泉县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3138, '610628', '', '', '富县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3139, '610629', '', '', '洛川县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3140, '610630', '', '', '宜川县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3141, '610631', '', '', '黄龙县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3142, '610632', '', '', '黄陵县', 3128, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3143, '610700', '', '汉中市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3144, '610701', '', '', '市辖区', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3145, '610702', '', '', '汉台区', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3146, '610721', '', '', '南郑县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3147, '610722', '', '', '城固县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3148, '610723', '', '', '洋县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3149, '610724', '', '', '西乡县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3150, '610725', '', '', '勉县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3151, '610726', '', '', '宁强县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3152, '610727', '', '', '略阳县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3153, '610728', '', '', '镇巴县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3154, '610729', '', '', '留坝县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3155, '610730', '', '', '佛坪县', 3143, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3156, '610800', '', '榆林市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3157, '610801', '', '', '市辖区', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3158, '610802', '', '', '榆阳区', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3159, '610821', '', '', '神木县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3160, '610822', '', '', '府谷县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3161, '610823', '', '', '横山县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3162, '610824', '', '', '靖边县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3163, '610825', '', '', '定边县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3164, '610826', '', '', '绥德县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3165, '610827', '', '', '米脂县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3166, '610828', '', '', '佳县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3167, '610829', '', '', '吴堡县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3168, '610830', '', '', '清涧县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3169, '610831', '', '', '子洲县', 3156, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3170, '610900', '', '安康市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3171, '610901', '', '', '市辖区', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3172, '610902000汉滨区', '', '', '', 610902000, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3173, '610921', '', '', '汉阴县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3174, '610922', '', '', '石泉县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3175, '610923', '', '', '宁陕县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3176, '610924', '', '', '紫阳县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3177, '610925', '', '', '岚皋县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3178, '610926', '', '', '平利县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3179, '610927', '', '', '镇坪县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3180, '610928', '', '', '旬阳县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3181, '610929', '', '', '白河县', 3170, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3182, '611000', '', '商洛市', '', 3063, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3183, '611001', '', '', '市辖区', 3182, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3184, '611002', '', '', '商州区', 3182, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3185, '611021', '', '', '洛南县', 3182, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3186, '611022', '', '', '丹凤县', 3182, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3187, '611023', '', '', '商南县', 3182, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3188, '611024', '', '', '山阳县', 3182, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3189, '611025', '', '', '镇安县', 3182, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3190, '611026', '', '', '柞水县', 3182, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3191, '620000', '甘肃省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3192, '620100', '', '兰州市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3193, '620101', '', '', '市辖区', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3194, '620102', '', '', '城关区', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3195, '620103', '', '', '七里河区', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3196, '620104', '', '', '西固区', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3197, '620105', '', '', '安宁区', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3198, '620111', '', '', '红古区', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3199, '620121', '', '', '永登县', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3200, '620122', '', '', '皋兰县', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3201, '620123', '', '', '榆中县', 3192, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3202, '620200', '', '嘉峪关市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3203, '620201', '', '', '市辖区', 3202, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3204, '620300', '', '金昌市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3205, '620301', '', '', '市辖区', 3204, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3206, '620302', '', '', '金川区', 3204, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3207, '620321', '', '', '永昌县', 3204, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3208, '620400', '', '白银市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3209, '620401', '', '', '市辖区', 3208, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3210, '620402', '', '', '白银区', 3208, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3211, '620403', '', '', '平川区', 3208, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3212, '620421', '', '', '靖远县', 3208, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3213, '620422', '', '', '会宁县', 3208, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3214, '620423', '', '', '景泰县', 3208, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3215, '620500', '', '天水市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3216, '620501', '', '', '市辖区', 3215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3217, '620502', '', '', '秦州区', 3215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3218, '620503', '', '', '麦积区', 3215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3219, '620521', '', '', '清水县', 3215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3220, '620522', '', '', '秦安县', 3215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3221, '620523', '', '', '甘谷县', 3215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3222, '620524', '', '', '武山县', 3215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3223, '620525', '', '', '张家川回族自治县', 3215, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3224, '620600', '', '武威市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3225, '620601', '', '', '市辖区', 3224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3226, '620602', '', '', '凉州区', 3224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3227, '620621', '', '', '民勤县', 3224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3228, '620622', '', '', '古浪县', 3224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3229, '620623', '', '', '天祝藏族自治县', 3224, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3230, '620700', '', '张掖市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3231, '620701', '', '', '市辖区', 3230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3232, '620702', '', '', '甘州区', 3230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3233, '620721', '', '', '肃南裕固族自治县', 3230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3234, '620722', '', '', '民乐县', 3230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3235, '620723', '', '', '临泽县', 3230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3236, '620724', '', '', '高台县', 3230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3237, '620725', '', '', '山丹县', 3230, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3238, '620800', '', '平凉市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3239, '620801', '', '', '市辖区', 3238, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3240, '620802', '', '', '崆峒区', 3238, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3241, '620821', '', '', '泾川县', 3238, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3242, '620822', '', '', '灵台县', 3238, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3243, '620823', '', '', '崇信县', 3238, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3244, '620824', '', '', '华亭县', 3238, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3245, '620825', '', '', '庄浪县', 3238, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3246, '620826', '', '', '静宁县', 3238, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3247, '620900', '', '酒泉市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3248, '620901', '', '', '市辖区', 3247, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3249, '620902', '', '', '肃州区', 3247, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3250, '620921', '', '', '金塔县', 3247, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3251, '620922', '', '', '瓜州县', 3247, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3252, '620923', '', '', '肃北蒙古族自治县', 3247, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3253, '620924', '', '', '阿克塞哈萨克族自治县', 3247, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3254, '620981', '', '', '玉门市', 3247, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3255, '620982', '', '', '敦煌市', 3247, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3256, '621000', '', '庆阳市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3257, '621001', '', '', '市辖区', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3258, '621002', '', '', '西峰区', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3259, '621021', '', '', '庆城县', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3260, '621022', '', '', '环县', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3261, '621023', '', '', '华池县', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3262, '621024', '', '', '合水县', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3263, '621025', '', '', '正宁县', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3264, '621026', '', '', '宁县', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3265, '621027', '', '', '镇原县', 3256, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3266, '621100', '', '定西市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3267, '621101', '', '', '市辖区', 3266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3268, '621102', '', '', '安定区', 3266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3269, '621121', '', '', '通渭县', 3266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3270, '621122', '', '', '陇西县', 3266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3271, '621123', '', '', '渭源县', 3266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3272, '621124', '', '', '临洮县', 3266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3273, '621125', '', '', '漳县', 3266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3274, '621126', '', '', '岷县', 3266, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3275, '621200', '', '陇南市', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3276, '621201', '', '', '市辖区', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3277, '621202', '', '', '武都区', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3278, '621221', '', '', '成县', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3279, '621222', '', '', '文县', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3280, '621223', '', '', '宕昌县', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3281, '621224', '', '', '康县', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3282, '621225', '', '', '西和县', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3283, '621226', '', '', '礼县', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3284, '621227', '', '', '徽县', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3285, '621228', '', '', '两当县', 3275, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3286, '622900', '', '临夏回族自治州', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3287, '622901', '', '', '临夏市', 3286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3288, '622921', '', '', '临夏县', 3286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3289, '622922', '', '', '康乐县', 3286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3290, '622923', '', '', '永靖县', 3286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3291, '622924', '', '', '广河县', 3286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3292, '622925', '', '', '和政县', 3286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3293, '622926', '', '', '东乡族自治县', 3286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3294, '622927', '', '', '积石山保安族东乡族撒拉族自治县', 3286, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3295, '623000', '', '甘南藏族自治州', '', 3191, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3296, '623001', '', '', '合作市', 3295, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3297, '623021', '', '', '临潭县', 3295, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3298, '623022', '', '', '卓尼县', 3295, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3299, '623023', '', '', '舟曲县', 3295, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3300, '623024', '', '', '迭部县', 3295, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3301, '623025', '', '', '玛曲县', 3295, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3302, '623026', '', '', '碌曲县', 3295, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3303, '623027', '', '', '夏河县', 3295, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3304, '630000', '青海省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3305, '630100', '', '西宁市', '', 3304, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3306, '630101', '', '', '市辖区', 3305, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3307, '630102', '', '', '城东区', 3305, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3308, '630103', '', '', '城中区', 3305, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3309, '630104', '', '', '城西区', 3305, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3310, '630105', '', '', '城北区', 3305, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3311, '630121', '', '', '大通回族土族自治县', 3305, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3312, '630122', '', '', '湟中县', 3305, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3313, '630123', '', '', '湟源县', 3305, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3314, '630200', '', '海东市', '', 3304, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3315, '630201', '', '', '市辖区', 3314, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3316, '630202', '', '', '乐都区', 3314, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3317, '630221', '', '', '平安县', 3314, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3318, '630222', '', '', '民和回族土族自治县', 3314, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3319, '630223', '', '', '互助土族自治县', 3314, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3320, '630224', '', '', '化隆回族自治县', 3314, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3321, '630225', '', '', '循化撒拉族自治县', 3314, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3322, '632200', '', '海北藏族自治州', '', 3304, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3323, '632221', '', '', '门源回族自治县', 3322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3324, '632222', '', '', '祁连县', 3322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3325, '632223', '', '', '海晏县', 3322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3326, '632224', '', '', '刚察县', 3322, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3327, '632300', '', '黄南藏族自治州', '', 3304, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3328, '632321', '', '', '同仁县', 3327, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3329, '632322', '', '', '尖扎县', 3327, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3330, '632323', '', '', '泽库县', 3327, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3331, '632324', '', '', '河南蒙古族自治县', 3327, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3332, '632500', '', '海南藏族自治州', '', 3304, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3333, '632521', '', '', '共和县', 3332, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3334, '632522', '', '', '同德县', 3332, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3335, '632523', '', '', '贵德县', 3332, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3336, '632524', '', '', '兴海县', 3332, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3337, '632525', '', '', '贵南县', 3332, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3338, '632600', '', '果洛藏族自治州', '', 3304, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3339, '632621', '', '', '玛沁县', 3338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3340, '632622', '', '', '班玛县', 3338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3341, '632623', '', '', '甘德县', 3338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3342, '632624', '', '', '达日县', 3338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3343, '632625', '', '', '久治县', 3338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3344, '632626', '', '', '玛多县', 3338, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3345, '632700', '', '玉树藏族自治州', '', 3304, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3346, '632701', '', '', '玉树市', 3345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3347, '632722', '', '', '杂多县', 3345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3348, '632723', '', '', '称多县', 3345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3349, '632724', '', '', '治多县', 3345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3350, '632725', '', '', '囊谦县', 3345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3351, '632726', '', '', '曲麻莱县', 3345, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3352, '632800', '', '海西蒙古族藏族自治州', '', 3304, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3353, '632801', '', '', '格尔木市', 3352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3354, '632802', '', '', '德令哈市', 3352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3355, '632821', '', '', '乌兰县', 3352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3356, '632822', '', '', '都兰县', 3352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3357, '632823', '', '', '天峻县', 3352, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3358, '640000', '宁夏回族自治区', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3359, '640100', '', '银川市', '', 3358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3360, '640101', '', '', '市辖区', 3359, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3361, '640104', '', '', '兴庆区', 3359, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3362, '640105', '', '', '西夏区', 3359, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3363, '640106', '', '', '金凤区', 3359, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3364, '640121', '', '', '永宁县', 3359, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3365, '640122', '', '', '贺兰县', 3359, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3366, '640181', '', '', '灵武市', 3359, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3367, '640200', '', '石嘴山市', '', 3358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3368, '640201', '', '', '市辖区', 3367, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3369, '640202', '', '', '大武口区', 3367, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3370, '640205', '', '', '惠农区', 3367, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3371, '640221', '', '', '平罗县', 3367, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3372, '640300', '', '吴忠市', '', 3358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3373, '640301', '', '', '市辖区', 3372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3374, '640302', '', '', '利通区', 3372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3375, '640303', '', '', '红寺堡区', 3372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3376, '640323', '', '', '盐池县', 3372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3377, '640324', '', '', '同心县', 3372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3378, '640381', '', '', '青铜峡市', 3372, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3379, '640400', '', '固原市', '', 3358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3380, '640401', '', '', '市辖区', 3379, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3381, '640402', '', '', '原州区', 3379, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3382, '640422', '', '', '西吉县', 3379, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3383, '640423', '', '', '隆德县', 3379, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3384, '640424', '', '', '泾源县', 3379, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3385, '640425', '', '', '彭阳县', 3379, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3386, '640500', '', '中卫市', '', 3358, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3387, '640501', '', '', '市辖区', 3386, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3388, '640502', '', '', '沙坡头区', 3386, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3389, '640521', '', '', '中宁县', 3386, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3390, '640522', '', '', '海原县', 3386, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3391, '650000', '新疆维吾尔自治区', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3392, '650100', '', '乌鲁木齐市', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3393, '650101', '', '', '市辖区', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3394, '650102', '', '', '天山区', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3395, '650103', '', '', '沙依巴克区', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3396, '650104', '', '', '新市区', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3397, '650105', '', '', '水磨沟区', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3398, '650106', '', '', '头屯河区', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3399, '650107', '', '', '达坂城区', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3400, '650109', '', '', '米东区', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3401, '650121', '', '', '乌鲁木齐县', 3392, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3402, '650200', '', '克拉玛依市', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3403, '650201', '', '', '市辖区', 3402, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3404, '650202', '', '', '独山子区', 3402, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3405, '650203', '', '', '克拉玛依区', 3402, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3406, '650204', '', '', '白碱滩区', 3402, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3407, '650205', '', '', '乌尔禾区', 3402, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3408, '652100', '', '吐鲁番地区', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3409, '652101', '', '', '吐鲁番市', 3408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3410, '652122', '', '', '鄯善县', 3408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3411, '652123', '', '', '托克逊县', 3408, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3412, '652200', '', '哈密地区', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3413, '652201', '', '', '哈密市', 3412, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3414, '652222', '', '', '巴里坤哈萨克自治县', 3412, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3415, '652223', '', '', '伊吾县', 3412, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3416, '652300', '', '昌吉回族自治州', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3417, '652301', '', '', '昌吉市', 3416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3418, '652302', '', '', '阜康市', 3416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3419, '652323', '', '', '呼图壁县', 3416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3420, '652324', '', '', '玛纳斯县', 3416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3421, '652325', '', '', '奇台县', 3416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3422, '652327', '', '', '吉木萨尔县', 3416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3423, '652328', '', '', '木垒哈萨克自治县', 3416, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3424, '652700', '', '博尔塔拉蒙古自治州', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3425, '652701', '', '', '博乐市', 3424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3426, '652702', '', '', '阿拉山口市', 3424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3427, '652722', '', '', '精河县', 3424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3428, '652723', '', '', '温泉县', 3424, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3429, '652800', '', '巴音郭楞蒙古自治州', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3430, '652801', '', '', '库尔勒市', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3431, '652822', '', '', '轮台县', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3432, '652823', '', '', '尉犁县', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3433, '652824', '', '', '若羌县', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3434, '652825', '', '', '且末县', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3435, '652826', '', '', '焉耆回族自治县', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3436, '652827', '', '', '和静县', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3437, '652828', '', '', '和硕县', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3438, '652829', '', '', '博湖县', 3429, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3439, '652900', '', '阿克苏地区', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3440, '652901', '', '', '阿克苏市', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3441, '652922', '', '', '温宿县', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3442, '652923', '', '', '库车县', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3443, '652924', '', '', '沙雅县', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3444, '652925', '', '', '新和县', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3445, '652926', '', '', '拜城县', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3446, '652927', '', '', '乌什县', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3447, '652928', '', '', '阿瓦提县', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3448, '652929', '', '', '柯坪县', 3439, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3449, '653000', '', '克孜勒苏柯尔克孜自治州', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3450, '653001', '', '', '阿图什市', 3449, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3451, '653022', '', '', '阿克陶县', 3449, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3452, '653023', '', '', '阿合奇县', 3449, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3453, '653024', '', '', '乌恰县', 3449, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3454, '653100', '', '喀什地区', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3455, '653101', '', '', '喀什市', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3456, '653121', '', '', '疏附县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3457, '653122', '', '', '疏勒县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3458, '653123', '', '', '英吉沙县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3459, '653124', '', '', '泽普县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3460, '653125', '', '', '莎车县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3461, '653126', '', '', '叶城县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3462, '653127', '', '', '麦盖提县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3463, '653128', '', '', '岳普湖县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3464, '653129', '', '', '伽师县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3465, '653130', '', '', '巴楚县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3466, '653131', '', '', '塔什库尔干塔吉克自治县', 3454, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3467, '653200', '', '和田地区', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3468, '653201', '', '', '和田市', 3467, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3469, '653221', '', '', '和田县', 3467, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3470, '653222', '', '', '墨玉县', 3467, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3471, '653223', '', '', '皮山县', 3467, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3472, '653224', '', '', '洛浦县', 3467, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3473, '653225', '', '', '策勒县', 3467, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3474, '653226', '', '', '于田县', 3467, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3475, '653227', '', '', '民丰县', 3467, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3476, '654000', '', '伊犁哈萨克自治州', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3477, '654002', '', '', '伊宁市', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3478, '654003', '', '', '奎屯市', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3479, '654004', '', '', '霍尔果斯市', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3480, '654021', '', '', '伊宁县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3481, '654022', '', '', '察布查尔锡伯自治县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3482, '654023', '', '', '霍城县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3483, '654024', '', '', '巩留县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3484, '654025', '', '', '新源县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3485, '654026', '', '', '昭苏县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3486, '654027', '', '', '特克斯县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3487, '654028', '', '', '尼勒克县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3488, '654200', '', '', '塔城地区', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3489, '654201', '', '', '塔城市', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3490, '654202', '', '', '乌苏市', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3491, '654221', '', '', '额敏县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3492, '654223', '', '', '沙湾县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3493, '654224', '', '', '托里县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3494, '654225', '', '', '裕民县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3495, '654226', '', '', '和布克赛尔蒙古自治县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3496, '654300', '', '', '阿勒泰地区', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3497, '654301', '', '', '阿勒泰市', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3498, '654321', '', '', '布尔津县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3499, '654322', '', '', '富蕴县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3500, '654323', '', '', '福海县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3501, '654324', '', '', '哈巴河县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3502, '654325', '', '', '青河县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3503, '654326', '', '', '吉木乃县', 3476, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3504, '659000', '', '自治区直辖县级行政区划', '', 3391, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3505, '659001', '', '', '石河子市', 3504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3506, '659002', '', '', '阿拉尔市', 3504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3507, '659003', '', '', '图木舒克市', 3504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3508, '659004', '', '', '五家渠市', 3504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3509, '659005', '', '', '北屯市', 3504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3510, '659006', '', '', '铁门关市', 3504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3511, '659007', '', '', '双河市', 3504, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3512, '710000', '台湾省', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3513, '710100', '', '台北市', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3514, '710101', '', '', '松山区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3515, '710102', '', '', '信义区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3516, '710103', '', '', '大安区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3517, '710104', '', '', '中山区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3518, '710105', '', '', '中正区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3519, '710106', '', '', '大同区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3520, '710107', '', '', '万华区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3521, '710108', '', '', '文山区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3522, '710109', '', '', '南港区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3523, '710110', '', '', '内湖区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3524, '710111', '', '', '士林区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3525, '710112', '', '', '北投区', 3513, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3526, '710200', '', '高雄市', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3527, '710201', '', '', '盐埕区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3528, '710202', '', '', '鼓山区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3529, '710203', '', '', '左营区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3530, '710204', '', '', '楠梓区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3531, '710205', '', '', '三民区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3532, '710206', '', '', '新兴区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3533, '710207', '', '', '前金区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3534, '710208', '', '', '苓雅区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3535, '710209', '', '', '前镇区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3536, '710210', '', '', '旗津区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3537, '710211', '', '', '小港区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3538, '710212', '', '', '凤山区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3539, '710213', '', '', '林园区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3540, '710214', '', '', '大寮区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3541, '710215', '', '', '大树区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3542, '710216', '', '', '大社区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3543, '710217', '', '', '仁武区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3544, '710218', '', '', '鸟松区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3545, '710219', '', '', '冈山区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3546, '710220', '', '', '桥头区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3547, '710221', '', '', '燕巢区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3548, '710222', '', '', '田寮区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3549, '710223', '', '', '阿莲区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3550, '710224', '', '', '路竹区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3551, '710225', '', '', '湖内区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3552, '710226', '', '', '茄萣区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3553, '710227', '', '', '永安区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3554, '710228', '', '', '弥陀区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3555, '710229', '', '', '梓官区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3556, '710230', '', '', '旗山区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3557, '710231', '', '', '美浓区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3558, '710232', '', '', '六龟区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3559, '710233', '', '', '甲仙区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3560, '710234', '', '', '杉林区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3561, '710235', '', '', '内门区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3562, '710236', '', '', '茂林区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3563, '710237', '', '', '桃源区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3564, '710238', '', '', '那玛夏区', 3526, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3565, '710300', '', '基隆市', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3566, '710301', '', '', '中正区', 3565, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3567, '710302', '', '', '七堵区', 3565, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3568, '710303', '', '', '暖暖区', 3565, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3569, '710304', '', '', '仁爱区', 3565, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3570, '710305', '', '', '中山区', 3565, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3571, '710306', '', '', '安乐区', 3565, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3572, '710307', '', '', '信义区', 3565, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3573, '710400', '', '台中市', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3574, '710401', '', '', '中区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3575, '710402', '', '', '东区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3576, '710403', '', '', '南区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3577, '710404', '', '', '西区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3578, '710405', '', '', '北区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3579, '710406', '', '', '西屯区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3580, '710407', '', '', '南屯区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3581, '710408', '', '', '北屯区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3582, '710409', '', '', '丰原区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3583, '710410', '', '', '东势区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3584, '710411', '', '', '大甲区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3585, '710412', '', '', '清水区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3586, '710413', '', '', '沙鹿区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3587, '710414', '', '', '梧栖区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3588, '710415', '', '', '后里区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3589, '710416', '', '', '神冈区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3590, '710417', '', '', '潭子区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3591, '710418', '', '', '大雅区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3592, '710419', '', '', '新社区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3593, '710420', '', '', '石冈区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3594, '710421', '', '', '外埔区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3595, '710422', '', '', '大安区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3596, '710423', '', '', '乌日区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3597, '710424', '', '', '大肚区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3598, '710425', '', '', '龙井区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3599, '710426', '', '', '雾峰区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3600, '710427', '', '', '太平区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3601, '710428', '', '', '大里区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3602, '710429', '', '', '和平区', 3573, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3603, '710500', '', '台南市', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3604, '710501', '', '', '东区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3605, '710502', '', '', '南区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3606, '710504', '', '', '北区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3607, '710506', '', '', '安南区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3608, '710507', '', '', '安平区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3609, '710508', '', '', '中西区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3610, '710509', '', '', '新营区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3611, '710510', '', '', '盐水区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3612, '710511', '', '', '白河区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3613, '710512', '', '', '柳营区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3614, '710513', '', '', '后壁区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3615, '710514', '', '', '东山区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3616, '710515', '', '', '麻豆区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3617, '710516', '', '', '下营区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3618, '710517', '', '', '六甲区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3619, '710518', '', '', '官田区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3620, '710519', '', '', '大内区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3621, '710520', '', '', '佳里区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3622, '710521', '', '', '学甲区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3623, '710522', '', '', '西港区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3624, '710523', '', '', '七股区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3625, '710524', '', '', '将军区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3626, '710525', '', '', '北门区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3627, '710526', '', '', '新化区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3628, '710527', '', '', '善化区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3629, '710528', '', '', '新市区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3630, '710529', '', '', '安定区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3631, '710530', '', '', '山上区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3632, '710531', '', '', '玉井区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3633, '710532', '', '', '楠西区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3634, '710533', '', '', '南化区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3635, '710534', '', '', '左镇区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3636, '710535', '', '', '仁德区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3637, '710536', '', '', '归仁区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3638, '710537', '', '', '关庙区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3639, '710538', '', '', '龙崎区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3640, '710539', '', '', '永康区', 3603, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3641, '710600', '', '新竹市', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3642, '710601', '', '', '东区', 3641, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3643, '710602', '', '', '北区', 3641, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3644, '710603', '', '', '香山区', 3641, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3645, '710700', '', '嘉义市', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3646, '710701', '', '', '东区', 3645, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3647, '710702', '', '', '西区', 3645, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3648, '710800', '', '新北市', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3649, '710801', '', '', '板桥区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3650, '710802', '', '', '三重区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3651, '710803', '', '', '中和区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3652, '710804', '', '', '永和区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3653, '710805', '', '', '新庄区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3654, '710806', '', '', '新店区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3655, '710807', '', '', '树林区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3656, '710808', '', '', '莺歌区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3657, '710809', '', '', '三峡区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3658, '710810', '', '', '淡水区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3659, '710811', '', '', '汐止区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3660, '710812', '', '', '瑞芳区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3661, '710813', '', '', '土城区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3662, '710814', '', '', '芦洲区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3663, '710815', '', '', '五股区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3664, '710816', '', '', '泰山区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3665, '710817', '', '', '林口区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3666, '710818', '', '', '深坑区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3667, '710819', '', '', '石碇区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3668, '710820', '', '', '坪林区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3669, '710821', '', '', '三芝区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3670, '710822', '', '', '石门区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3671, '710823', '', '', '八里区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3672, '710824', '', '', '平溪区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3673, '710825', '', '', '双溪区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3674, '710826', '', '', '贡寮区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3675, '710827', '', '', '金山区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3676, '710828', '', '', '万里区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3677, '710829', '', '', '乌来区', 3648, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3678, '712200', '', '宜兰县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3679, '712201', '', '', '宜兰市', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3680, '712221', '', '', '罗东镇', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3681, '712222', '', '', '苏澳镇', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3682, '712223', '', '', '头城镇', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3683, '712224', '', '', '礁溪乡', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3684, '712225', '', '', '壮围乡', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3685, '712226', '', '', '员山乡', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3686, '712227', '', '', '冬山乡', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3687, '712228', '', '', '五结乡', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3688, '712229', '', '', '三星乡', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3689, '712230', '', '', '大同乡', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3690, '712231', '', '', '南澳乡', 3678, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3691, '712300', '', '桃园县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3692, '712301', '', '', '桃园市', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3693, '712302', '', '', '中坜市', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3694, '712303', '', '', '平镇市', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3695, '712304', '', '', '八德市', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3696, '712305', '', '', '杨梅市', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3697, '712321', '', '', '大溪镇', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3698, '712323', '', '', '芦竹乡', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3699, '712324', '', '', '大园乡', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3700, '712325', '', '', '龟山乡', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3701, '712327', '', '', '龙潭乡', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3702, '712329', '', '', '新屋乡', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3703, '712330', '', '', '观音乡', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3704, '712331', '', '', '复兴乡', 3691, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3705, '712400', '', '新竹县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3706, '712401', '', '', '竹北市', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3707, '712421', '', '', '竹东镇', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3708, '712422', '', '', '新埔镇', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3709, '712423', '', '', '关西镇', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3710, '712424', '', '', '湖口乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3711, '712425', '', '', '新丰乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3712, '712426', '', '', '芎林乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3713, '712427', '', '', '橫山乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3714, '712428', '', '', '北埔乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3715, '712429', '', '', '宝山乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3716, '712430', '', '', '峨眉乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3717, '712431', '', '', '尖石乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3718, '712432', '', '', '五峰乡', 3705, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3719, '712500', '', '苗栗县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3720, '712501', '', '', '苗栗市', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3721, '712521', '', '', '苑里镇', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3722, '712522', '', '', '通霄镇', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3723, '712523', '', '', '竹南镇', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3724, '712524', '', '', '头份镇', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3725, '712525', '', '', '后龙镇', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3726, '712526', '', '', '卓兰镇', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3727, '712527', '', '', '大湖乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3728, '712528', '', '', '公馆乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3729, '712529', '', '', '铜锣乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3730, '712530', '', '', '南庄乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3731, '712531', '', '', '头屋乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3732, '712532', '', '', '三义乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3733, '712533', '', '', '西湖乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3734, '712534', '', '', '造桥乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3735, '712535', '', '', '三湾乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3736, '712536', '', '', '狮潭乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3737, '712537', '', '', '泰安乡', 3719, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3738, '712700', '', '彰化县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3739, '712701', '', '', '彰化市', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3740, '712721', '', '', '鹿港镇', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3741, '712722', '', '', '和美镇', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3742, '712723', '', '', '线西乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3743, '712724', '', '', '伸港乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3744, '712725', '', '', '福兴乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3745, '712726', '', '', '秀水乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3746, '712727', '', '', '花坛乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3747, '712728', '', '', '芬园乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3748, '712729', '', '', '员林镇', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3749, '712730', '', '', '溪湖镇', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3750, '712731', '', '', '田中镇', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3751, '712732', '', '', '大村乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3752, '712733', '', '', '埔盐乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3753, '712734', '', '', '埔心乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3754, '712735', '', '', '永靖乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3755, '712736', '', '', '社头乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3756, '712737', '', '', '二水乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3757, '712738', '', '', '北斗镇', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3758, '712739', '', '', '二林镇', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3759, '712740', '', '', '田尾乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3760, '712741', '', '', '埤头乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3761, '712742', '', '', '芳苑乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3762, '712743', '', '', '大城乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3763, '712744', '', '', '竹塘乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3764, '712745', '', '', '溪州乡', 3738, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3765, '712800', '', '南投县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3766, '712801', '', '', '南投市', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3767, '712821', '', '', '埔里镇', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3768, '712822', '', '', '草屯镇', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3769, '712823', '', '', '竹山镇', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3770, '712824', '', '', '集集镇', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3771, '712825', '', '', '名间乡', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3772, '712826', '', '', '鹿谷乡', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3773, '712827', '', '', '中寮乡', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3774, '712828', '', '', '鱼池乡', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3775, '712829', '', '', '国姓乡', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3776, '712830', '', '', '水里乡', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3777, '712831', '', '', '信义乡', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3778, '712832', '', '', '仁爱乡', 3765, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3779, '712900', '', '云林县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3780, '712901', '', '', '斗六市', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3781, '712921', '', '', '斗南镇', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3782, '712922', '', '', '虎尾镇', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3783, '712923', '', '', '西螺镇', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3784, '712924', '', '', '土库镇', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3785, '712925', '', '', '北港镇', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3786, '712926', '', '', '古坑乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3787, '712927', '', '', '大埤乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3788, '712928', '', '', '莿桐乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3789, '712929', '', '', '林内乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3790, '712930', '', '', '二仑乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3791, '712931', '', '', '仑背乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3792, '712932', '', '', '麦寮乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3793, '712933', '', '', '东势乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3794, '712934', '', '', '褒忠乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3795, '712935', '', '', '台西乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3796, '712936', '', '', '元长乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3797, '712937', '', '', '四湖乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3798, '712938', '', '', '口湖乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3799, '712939', '', '', '水林乡', 3779, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3800, '713000', '', '嘉义县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3801, '713001', '', '', '太保市', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3802, '713002', '', '', '朴子市', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3803, '713023', '', '', '布袋镇', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3804, '713024', '', '', '大林镇', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3805, '713025', '', '', '民雄乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3806, '713026', '', '', '溪口乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3807, '713027', '', '', '新港乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3808, '713028', '', '', '六脚乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3809, '713029', '', '', '东石乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3810, '713030', '', '', '义竹乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3811, '713031', '', '', '鹿草乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3812, '713032', '', '', '水上乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3813, '713033', '', '', '中埔乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3814, '713034', '', '', '竹崎乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3815, '713035', '', '', '梅山乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3816, '713036', '', '', '番路乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3817, '713037', '', '', '大埔乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3818, '713038', '', '', '阿里山乡', 3800, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3819, '713300', '', '屏东县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3820, '713301', '', '', '屏东市', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3821, '713321', '', '', '潮州镇', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3822, '713322', '', '', '东港镇', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3823, '713323', '', '', '恒春镇', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3824, '713324', '', '', '万丹乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3825, '713325', '', '', '长治乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3826, '713326', '', '', '麟洛乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3827, '713327', '', '', '九如乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3828, '713328', '', '', '里港乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3829, '713329', '', '', '盐埔乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3830, '713330', '', '', '高树乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3831, '713331', '', '', '万峦乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3832, '713332', '', '', '内埔乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3833, '713333', '', '', '竹田乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3834, '713334', '', '', '新埤乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3835, '713335', '', '', '枋寮乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3836, '713336', '', '', '新园乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3837, '713337', '', '', '崁顶乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3838, '713338', '', '', '林边乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3839, '713339', '', '', '南州乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3840, '713340', '', '', '佳冬乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3841, '713341', '', '', '琉球乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3842, '713342', '', '', '车城乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3843, '713343', '', '', '满州乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3844, '713344', '', '', '枋山乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3845, '713345', '', '', '三地门乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3846, '713346', '', '', '雾台乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3847, '713347', '', '', '玛家乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3848, '713348', '', '', '泰武乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3849, '713349', '', '', '来义乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3850, '713350', '', '', '春日乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3851, '713351', '', '', '狮子乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3852, '713352', '', '', '牡丹乡', 3819, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3853, '713400', '', '台东县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3854, '713401', '', '', '台东市', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3855, '713421', '', '', '成功镇', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3856, '713422', '', '', '关山镇', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3857, '713423', '', '', '卑南乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3858, '713424', '', '', '鹿野乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3859, '713425', '', '', '池上乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3860, '713426', '', '', '东河乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3861, '713427', '', '', '长滨乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3862, '713428', '', '', '太麻里乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3863, '713429', '', '', '大武乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3864, '713430', '', '', '绿岛乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3865, '713431', '', '', '海端乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3866, '713432', '', '', '延平乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3867, '713433', '', '', '金峰乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3868, '713434', '', '', '达仁乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3869, '713435', '', '', '兰屿乡', 3853, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3870, '713500', '', '花莲县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3871, '713501', '', '', '花莲市', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3872, '713521', '', '', '凤林镇', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3873, '713522', '', '', '玉里镇', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3874, '713523', '', '', '新城乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3875, '713524', '', '', '吉安乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3876, '713525', '', '', '寿丰乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3877, '713526', '', '', '光复乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3878, '713527', '', '', '丰滨乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3879, '713528', '', '', '瑞穗乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3880, '713529', '', '', '富里乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3881, '713530', '', '', '秀林乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3882, '713531', '', '', '万荣乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3883, '713532', '', '', '卓溪乡', 3870, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3884, '713600', '', '澎湖县', '', 3512, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3885, '713601', '', '', '马公市', 3884, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3886, '713621', '', '', '湖西乡', 3884, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3887, '713622', '', '', '白沙乡', 3884, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3888, '713623', '', '', '西屿乡', 3884, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3889, '713624', '', '', '望安乡', 3884, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3890, '713625', '', '', '七美乡', 3884, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3891, '810000', '香港特别行政区', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3892, '810100', '', '香港岛', '', 3891, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3893, '810101', '', '', '中西区', 3892, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3894, '810102', '', '', '湾仔区', 3892, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3895, '810103', '', '', '东区', 3892, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3896, '810104', '', '', '南区', 3892, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3897, '810200', '', '九龙', '', 3891, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3898, '810201', '', '', '油尖旺区', 3897, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3899, '810202', '', '', '深水埗区', 3897, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3900, '810203', '', '', '九龙城区', 3897, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3901, '810204', '', '', '黄大仙区', 3897, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3902, '810205', '', '', '观塘区', 3897, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3903, '810300', '', '新界', '', 3891, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3904, '810301', '', '', '荃湾区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3905, '810302', '', '', '屯门区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3906, '810303', '', '', '元朗区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3907, '810304', '', '', '北区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3908, '810305', '', '', '大埔区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3909, '810306', '', '', '西贡区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3910, '810307', '', '', '沙田区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3911, '810308', '', '', '葵青区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3912, '810309', '', '', '离岛区', 3903, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3913, '820000', '澳门特别行政区', '', '', 1, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3914, '820100', '', '澳门半岛', '', 3913, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3915, '820101', '', '', '花地玛堂区', 3914, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3916, '820102', '', '', '圣安多尼堂区', 3914, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3917, '820103', '', '', '大堂区', 3914, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3918, '820104', '', '', '望德堂区', 3914, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3919, '820105', '', '', '风顺堂区', 3914, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3920, '820200', '', '氹仔岛', '', 3913, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3921, '820201', '', '', '嘉模堂区', 3920, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3922, '820300', '', '路环岛', '', 3913, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3923, '820301', '', '', '圣方济各堂区', 3922, '', 0, 0);
INSERT INTO `f_com_area` VALUES (3924, '110000', '', '北京市', '', 2, '', 0, 0);

-- ----------------------------
-- Table structure for f_com_area_zone
-- ----------------------------
DROP TABLE IF EXISTS `f_com_area_zone`;
CREATE TABLE `f_com_area_zone`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_code` char(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `dt_type` int(11) NOT NULL COMMENT 'dtree,街道,小区,村镇,..',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `code` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'area_code 基础上 多增加2位',
  `py` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '拼音 全拼',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '社区' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_auth_code
-- ----------------------------
DROP TABLE IF EXISTS `f_com_auth_code`;
CREATE TABLE `f_com_auth_code`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '验证码/其它',
  `accepter` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机或邮箱',
  `expire_time` int(10) UNSIGNED NOT NULL COMMENT '过期时间',
  `ip` bigint(20) NOT NULL,
  `client_id` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(2) UNSIGNED NOT NULL COMMENT '状态, 0=>默认,1=>验证成功,2=>验证失败',
  `type` tinyint(4) UNSIGNED NOT NULL COMMENT '类型, 1=>reg,2=>user edit,3=>login',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '验证码' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_banner
-- ----------------------------
DROP TABLE IF EXISTS `f_com_banner`;
CREATE TABLE `f_com_banner`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `add_uid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `store_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `url_type` int(10) UNSIGNED NOT NULL COMMENT 'datatree,链接类型,webview/帖子详情/商品详情',
  `start_time` int(10) UNSIGNED NOT NULL,
  `end_time` int(10) UNSIGNED NOT NULL,
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  `sort` int(10) UNSIGNED NOT NULL,
  `views` int(10) UNSIGNED NOT NULL COMMENT '点击率',
  `dt_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'banner' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_com_banner
-- ----------------------------
INSERT INTO `f_com_banner` VALUES (1, 'app_index_1', 'd', '6', 0, 0, 'http://www.51cc.win', 10, 0, 0, 0, 0, 0, 0, 14);
INSERT INTO `f_com_banner` VALUES (2, 'pc_top_1', 'url -- ddd', '8', 0, 0, '9', 11, 0, 0, 0, 0, 1, 0, 15);

-- ----------------------------
-- Table structure for f_com_country
-- ----------------------------
DROP TABLE IF EXISTS `f_com_country`;
CREATE TABLE `f_com_country`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `code` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tel_pre` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `py` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '拼音',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 241 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_com_country
-- ----------------------------
INSERT INTO `f_com_country` VALUES (1, '中国', 'ZH', '+86', 'ZG');
INSERT INTO `f_com_country` VALUES (2, '阿尔巴尼亚', 'ALB', '+355', 'AEBNY');
INSERT INTO `f_com_country` VALUES (3, '阿尔及利亚', 'DZA', '+213', 'AEJLY');
INSERT INTO `f_com_country` VALUES (4, '阿富汗', 'AFG', '+93', 'AFH');
INSERT INTO `f_com_country` VALUES (5, '阿根廷', 'ARG', '+54', 'AGT');
INSERT INTO `f_com_country` VALUES (6, '阿拉伯联合酋长国', 'ARE', '+971', 'ALBLHQCG');
INSERT INTO `f_com_country` VALUES (7, '阿鲁巴', 'ABW', '+297', 'ALB');
INSERT INTO `f_com_country` VALUES (8, '阿曼', 'OMN', '+968', 'AM');
INSERT INTO `f_com_country` VALUES (9, '阿塞拜疆', 'AZE', '+994', 'ASBJ');
INSERT INTO `f_com_country` VALUES (10, '阿森松岛', 'ASC', '+247', 'ASSD');
INSERT INTO `f_com_country` VALUES (11, '埃及', 'EGY', '+20', 'AJ');
INSERT INTO `f_com_country` VALUES (12, '埃塞俄比亚', 'ETH', '+251', 'ASEBY');
INSERT INTO `f_com_country` VALUES (13, '爱尔兰', 'IRL', '+353', 'AEL');
INSERT INTO `f_com_country` VALUES (14, '爱沙尼亚', 'EST', '+372', 'ASNY');
INSERT INTO `f_com_country` VALUES (15, '安道尔', 'AND', '+376', 'ADE');
INSERT INTO `f_com_country` VALUES (16, '安哥拉', 'AGO', '+244', 'AGL');
INSERT INTO `f_com_country` VALUES (17, '安圭拉', 'AIA', '+1264', 'AGL');
INSERT INTO `f_com_country` VALUES (18, '安提瓜岛和巴布达', 'ATG', '+1268', 'ATGDHBBD');
INSERT INTO `f_com_country` VALUES (19, '澳大利亚', 'AUS', '+61', 'ADLY');
INSERT INTO `f_com_country` VALUES (20, '奥地利', 'AUT', '+43', 'ADL');
INSERT INTO `f_com_country` VALUES (21, '奥兰群岛', 'ALA', '+358', 'ALQD');
INSERT INTO `f_com_country` VALUES (22, '巴巴多斯岛', 'BRB', '+1246', 'BBDSD');
INSERT INTO `f_com_country` VALUES (23, '巴布亚新几内亚', 'PNG', '+675', 'BBYXJNY');
INSERT INTO `f_com_country` VALUES (24, '巴哈马', 'BHS', '+1242', 'BHM');
INSERT INTO `f_com_country` VALUES (25, '巴基斯坦', 'PAK', '+92', 'BJST');
INSERT INTO `f_com_country` VALUES (26, '巴拉圭', 'PRY', '+595', 'BLG');
INSERT INTO `f_com_country` VALUES (27, '巴勒斯坦', 'PSE', '+970', 'BLST');
INSERT INTO `f_com_country` VALUES (28, '巴林', 'BHR', '+973', 'BL');
INSERT INTO `f_com_country` VALUES (29, '巴拿马', 'PAN', '+507', 'BNM');
INSERT INTO `f_com_country` VALUES (30, '巴西', 'BRA', '+55', 'BX');
INSERT INTO `f_com_country` VALUES (31, '白俄罗斯', 'BLR', '+375', 'BELS');
INSERT INTO `f_com_country` VALUES (32, '百慕大', 'BMU', '+1441', 'BMD');
INSERT INTO `f_com_country` VALUES (33, '保加利亚', 'BGR', '+359', 'BJLY');
INSERT INTO `f_com_country` VALUES (34, '北马里亚纳群岛', 'MNP', '+1670', 'BMLYNQD');
INSERT INTO `f_com_country` VALUES (35, '贝宁', 'BEN', '+229', 'BN');
INSERT INTO `f_com_country` VALUES (36, '比利时', 'BEL', '+32', 'BLS');
INSERT INTO `f_com_country` VALUES (37, '冰岛', 'ISL', '+354', 'BD');
INSERT INTO `f_com_country` VALUES (38, '波多黎各', 'PRI', '+1809', 'BDLG');
INSERT INTO `f_com_country` VALUES (39, '波兰', 'POL', '+48', 'BL');
INSERT INTO `f_com_country` VALUES (40, '玻利维亚', 'BOL', '+591', 'BLWY');
INSERT INTO `f_com_country` VALUES (41, '波斯尼亚和黑塞哥维那', 'BIH', '+387', 'BSNYHHSGWN');
INSERT INTO `f_com_country` VALUES (42, '博茨瓦纳', 'BWA', '+267', 'BCWN');
INSERT INTO `f_com_country` VALUES (43, '伯利兹', 'BLZ', '+501', 'BLZ');
INSERT INTO `f_com_country` VALUES (44, '不丹', 'BTN', '+975', 'BD');
INSERT INTO `f_com_country` VALUES (45, '布基纳法索', 'BFA', '+226', 'BJNFS');
INSERT INTO `f_com_country` VALUES (46, '布隆迪', 'BDI', '+257', 'BLD');
INSERT INTO `f_com_country` VALUES (47, '布韦岛', 'BVT', '+00', 'BWD');
INSERT INTO `f_com_country` VALUES (48, '朝鲜', 'PRK', '+850', 'CX');
INSERT INTO `f_com_country` VALUES (49, '丹麦', 'DNK', '+45', 'DM');
INSERT INTO `f_com_country` VALUES (50, '德国', 'DEU', '+49', 'DG');
INSERT INTO `f_com_country` VALUES (51, '东帝汶', 'TLS', '+670', 'DDZ');
INSERT INTO `f_com_country` VALUES (52, '多哥', 'TGO', '+228', 'DG');
INSERT INTO `f_com_country` VALUES (53, '多米尼加', 'DMA', '+', 'DMNJ');
INSERT INTO `f_com_country` VALUES (54, '多米尼加共和国', 'DOM', '+1809', 'DMNJGHG');
INSERT INTO `f_com_country` VALUES (55, '俄罗斯', 'RUS', '+7', 'ELS');
INSERT INTO `f_com_country` VALUES (56, '厄瓜多尔', 'ECU', '593', 'EGDE');
INSERT INTO `f_com_country` VALUES (57, '厄立特里亚', 'ERI', '+291', 'ELTLY');
INSERT INTO `f_com_country` VALUES (58, '法国', 'FRA', '+33', 'FG');
INSERT INTO `f_com_country` VALUES (59, '法罗群岛', 'FRO', '+298', 'FLQD');
INSERT INTO `f_com_country` VALUES (60, '法属波利尼西亚', 'PYF', '+689', 'FSBLNXY');
INSERT INTO `f_com_country` VALUES (61, '法属圭亚那', 'GUF', '+594', 'FSGYN');
INSERT INTO `f_com_country` VALUES (62, '法属南部领地', 'ATF', '+00', 'FSNBLD');
INSERT INTO `f_com_country` VALUES (63, '梵蒂冈', 'VAT', '+3906698', 'ZDG');
INSERT INTO `f_com_country` VALUES (64, '菲律宾', 'PHL', '+63', 'FLB');
INSERT INTO `f_com_country` VALUES (65, '斐济', 'FJI', '+679', 'ZJ');
INSERT INTO `f_com_country` VALUES (66, '芬兰', 'FIN', '+358', 'FL');
INSERT INTO `f_com_country` VALUES (67, '佛得角', 'CPV', '+238', 'FDJ');
INSERT INTO `f_com_country` VALUES (68, '弗兰克群岛', 'FLK', '+', 'FLKQD');
INSERT INTO `f_com_country` VALUES (69, '冈比亚', 'GMB', '+220', 'GBY');
INSERT INTO `f_com_country` VALUES (70, '刚果', 'COG', '+242', 'GG');
INSERT INTO `f_com_country` VALUES (71, '刚果民主共和国', 'COD', '+243', 'GGMZGHG');
INSERT INTO `f_com_country` VALUES (72, '哥伦比亚', 'COL', '+57', 'GLBY');
INSERT INTO `f_com_country` VALUES (73, '哥斯达黎加', 'CRI', '+506', 'GSDLJ');
INSERT INTO `f_com_country` VALUES (74, '格恩西岛', 'GGY', '+', 'GEXD');
INSERT INTO `f_com_country` VALUES (75, '格林纳达', 'GRD', '+1473', 'GLND');
INSERT INTO `f_com_country` VALUES (76, '格陵兰', 'GRL', '+299', 'GLL');
INSERT INTO `f_com_country` VALUES (77, '古巴', 'CUB', '+53', 'GB');
INSERT INTO `f_com_country` VALUES (78, '瓜德罗普', 'GLP', '+590', 'GDLP');
INSERT INTO `f_com_country` VALUES (79, '关岛', 'GUM', '+1671', 'GD');
INSERT INTO `f_com_country` VALUES (80, '圭亚那', 'GUY', '+592', 'GYN');
INSERT INTO `f_com_country` VALUES (81, '哈萨克斯坦', 'KAZ', '+7', 'HSKST');
INSERT INTO `f_com_country` VALUES (82, '海地', 'HTI', '+509', 'HD');
INSERT INTO `f_com_country` VALUES (83, '韩国', 'KOR', '+82', 'HG');
INSERT INTO `f_com_country` VALUES (84, '荷兰', 'NLD', '+31', 'HL');
INSERT INTO `f_com_country` VALUES (85, '荷属安地列斯', 'ANT', '+', 'HSADLS');
INSERT INTO `f_com_country` VALUES (86, '赫德和麦克唐纳群岛', 'HMD', '+', 'HDHMKTNQD');
INSERT INTO `f_com_country` VALUES (87, '洪都拉斯', 'HND', '+504', 'HDLS');
INSERT INTO `f_com_country` VALUES (88, '基里巴斯', 'KIR', '+686', 'JLBS');
INSERT INTO `f_com_country` VALUES (89, '吉布提', 'DJI', '+253', 'JBT');
INSERT INTO `f_com_country` VALUES (90, '吉尔吉斯斯坦', 'KGZ', '+996', 'JEJSST');
INSERT INTO `f_com_country` VALUES (91, '几内亚', 'GIN', '+224', 'JNY');
INSERT INTO `f_com_country` VALUES (92, '几内亚比绍', 'GNB', '+245', 'JNYBS');
INSERT INTO `f_com_country` VALUES (93, '加拿大', 'CAN', '+1', 'JND');
INSERT INTO `f_com_country` VALUES (94, '加纳', 'GHA', '+233', 'JN');
INSERT INTO `f_com_country` VALUES (95, '加蓬', 'GAB', '+241', 'JP');
INSERT INTO `f_com_country` VALUES (96, '柬埔寨', 'KHM', '+855', 'JPZ');
INSERT INTO `f_com_country` VALUES (97, '捷克共和国', 'CZE', '+420', 'JKGHG');
INSERT INTO `f_com_country` VALUES (98, '津巴布韦', 'ZWE', '+263', 'JBBW');
INSERT INTO `f_com_country` VALUES (99, '喀麦隆', 'CMR', '+237', 'KML');
INSERT INTO `f_com_country` VALUES (100, '卡塔尔', 'QAT', '+974', 'KTE');
INSERT INTO `f_com_country` VALUES (101, '开曼群岛', 'CYM', '+1345', 'KMQD');
INSERT INTO `f_com_country` VALUES (102, '科科斯群岛', 'CCK', '+61-891', 'KKSQD');
INSERT INTO `f_com_country` VALUES (103, '科摩罗', 'COM', '+269', 'KML');
INSERT INTO `f_com_country` VALUES (104, '科特迪瓦', 'CIV', '+225', 'KTDW');
INSERT INTO `f_com_country` VALUES (105, '科威特', 'KWT', '+965', 'KWT');
INSERT INTO `f_com_country` VALUES (106, '克罗地亚', 'HRV', '+385', 'KLDY');
INSERT INTO `f_com_country` VALUES (107, '肯尼亚', 'KEN', '+254', 'KNY');
INSERT INTO `f_com_country` VALUES (108, '库克群岛', 'COK', '+682', 'KKQD');
INSERT INTO `f_com_country` VALUES (109, '拉脱维亚', 'LVA', '+371', 'LTWY');
INSERT INTO `f_com_country` VALUES (110, '莱索托', 'LSO', '+266', 'LST');
INSERT INTO `f_com_country` VALUES (111, '老挝', 'LAO', '+856', 'LW');
INSERT INTO `f_com_country` VALUES (112, '黎巴嫩', 'LBN', '+961', 'LBN');
INSERT INTO `f_com_country` VALUES (113, '利比里亚', 'LBR', '+231', 'LBLY');
INSERT INTO `f_com_country` VALUES (114, '利比亚', 'LBY', '+218', 'LBY');
INSERT INTO `f_com_country` VALUES (115, '立陶宛', 'LTU', '+370', 'LTW');
INSERT INTO `f_com_country` VALUES (116, '列支敦士登', 'LIE', '+423', 'LZDSD');
INSERT INTO `f_com_country` VALUES (117, '留尼旺岛', 'REU', '+262', 'LNWD');
INSERT INTO `f_com_country` VALUES (118, '卢森堡', 'LUX', '+352', 'LSB');
INSERT INTO `f_com_country` VALUES (119, '卢旺达', 'RWA', '+250', 'LWD');
INSERT INTO `f_com_country` VALUES (120, '罗马尼亚', 'ROU', '+40', 'LMNY');
INSERT INTO `f_com_country` VALUES (121, '马达加斯加', 'MDG', '+261', 'MDJSJ');
INSERT INTO `f_com_country` VALUES (122, '马尔代夫', 'MDV', '+960', 'MEDF');
INSERT INTO `f_com_country` VALUES (123, '马耳他', 'MLT', '+356', 'MET');
INSERT INTO `f_com_country` VALUES (124, '马拉维', 'MWI', '+265', 'MLW');
INSERT INTO `f_com_country` VALUES (125, '马来西亚', 'MYS', '+60', 'MLXY');
INSERT INTO `f_com_country` VALUES (126, '马里', 'MLI', '+223', 'ML');
INSERT INTO `f_com_country` VALUES (127, '马其顿', 'MKD', '+389', 'MQD');
INSERT INTO `f_com_country` VALUES (128, '马绍尔群岛', 'MHL', '+692', 'MSEQD');
INSERT INTO `f_com_country` VALUES (129, '马提尼克', 'MTQ', '+596', 'MTNK');
INSERT INTO `f_com_country` VALUES (130, '马约特岛', 'MYT', '+269', 'MYTD');
INSERT INTO `f_com_country` VALUES (131, '曼岛', 'IMN', '+44-1624', 'MD');
INSERT INTO `f_com_country` VALUES (132, '毛里求斯', 'MUS', '+230', 'MLQS');
INSERT INTO `f_com_country` VALUES (133, '毛里塔尼亚', 'MRT', '+222', 'MLTNY');
INSERT INTO `f_com_country` VALUES (134, '美国', 'USA', '+1', 'MG');
INSERT INTO `f_com_country` VALUES (135, '美属萨摩亚', 'ASM', '+1684', 'MSSMY');
INSERT INTO `f_com_country` VALUES (136, '美属外岛', 'UMI', '+', 'MSWD');
INSERT INTO `f_com_country` VALUES (137, '蒙古', 'MNG', '+976', 'MG');
INSERT INTO `f_com_country` VALUES (138, '蒙特塞拉特', 'MSR', '+1664', 'MTSLT');
INSERT INTO `f_com_country` VALUES (139, '孟加拉', 'BGD', '+880', 'MJL');
INSERT INTO `f_com_country` VALUES (140, '密克罗尼西亚', 'FSM', '+00691', 'MKLNXY');
INSERT INTO `f_com_country` VALUES (141, '秘鲁', 'PER', '+51', 'ML');
INSERT INTO `f_com_country` VALUES (142, '缅甸', 'MMR', '+95', 'MD');
INSERT INTO `f_com_country` VALUES (143, '摩尔多瓦', 'MDA', '+373', 'MEDW');
INSERT INTO `f_com_country` VALUES (144, '摩洛哥', 'MAR', '+212', 'MLG');
INSERT INTO `f_com_country` VALUES (145, '摩纳哥', 'MCO', '+377', 'MNG');
INSERT INTO `f_com_country` VALUES (146, '莫桑比克', 'MOZ', '+258', 'MSBK');
INSERT INTO `f_com_country` VALUES (147, '墨西哥', 'MEX', '+52', 'MXG');
INSERT INTO `f_com_country` VALUES (148, '纳米比亚', 'NAM', '+264', 'NMBY');
INSERT INTO `f_com_country` VALUES (149, '南非', 'ZAF', '+27', 'NF');
INSERT INTO `f_com_country` VALUES (150, '南极洲', 'ATA', '+672', 'NJZ');
INSERT INTO `f_com_country` VALUES (151, '南乔治亚和南桑德威奇群岛', 'SGS', '+', 'NQZYHNSDWQQD');
INSERT INTO `f_com_country` VALUES (152, '瑙鲁', 'NRU', '+674', 'ZL');
INSERT INTO `f_com_country` VALUES (153, '尼泊尔', 'NPL', '+977', 'NBE');
INSERT INTO `f_com_country` VALUES (154, '尼加拉瓜', 'NIC', '+505', 'NJLG');
INSERT INTO `f_com_country` VALUES (155, '尼日尔', 'NER', '+227', 'NRE');
INSERT INTO `f_com_country` VALUES (156, '尼日利亚', 'NGA', '+234', 'NRLY');
INSERT INTO `f_com_country` VALUES (157, '纽埃', 'NIU', '+683', 'NA');
INSERT INTO `f_com_country` VALUES (158, '挪威', 'NOR', '+47', 'NW');
INSERT INTO `f_com_country` VALUES (159, '诺福克', 'NFK', '+672', 'NFK');
INSERT INTO `f_com_country` VALUES (160, '帕劳群岛', 'PLW', '+', 'PLQD');
INSERT INTO `f_com_country` VALUES (161, '皮特凯恩', 'PCN', '+', 'PTKE');
INSERT INTO `f_com_country` VALUES (162, '葡萄牙', 'PRT', '+351', 'PTY');
INSERT INTO `f_com_country` VALUES (163, '乔治亚', 'GEO', '+', 'QZY');
INSERT INTO `f_com_country` VALUES (164, '日本', 'JPN', '+81', 'RB');
INSERT INTO `f_com_country` VALUES (165, '瑞典', 'SWE', '+46', 'RD');
INSERT INTO `f_com_country` VALUES (166, '瑞士', 'CHE', '+41', 'RS');
INSERT INTO `f_com_country` VALUES (167, '萨尔瓦多', 'SLV', '+503', 'SEWD');
INSERT INTO `f_com_country` VALUES (168, '萨摩亚', 'WSM', '+684', 'SMY');
INSERT INTO `f_com_country` VALUES (169, '塞尔维亚,黑山', 'SCG', '+381', 'SEWY,HS');
INSERT INTO `f_com_country` VALUES (170, '塞拉利昂', 'SLE', '+232', 'SLLA');
INSERT INTO `f_com_country` VALUES (171, '塞内加尔', 'SEN', '+221', 'SNJE');
INSERT INTO `f_com_country` VALUES (172, '塞浦路斯', 'CYP', '+357', 'SPLS');
INSERT INTO `f_com_country` VALUES (173, '塞舌尔', 'SYC', '+248', 'SSE');
INSERT INTO `f_com_country` VALUES (174, '沙特阿拉伯', 'SAU', '+966', 'STALB');
INSERT INTO `f_com_country` VALUES (175, '圣诞岛', 'CXR', '+619164', 'SDD');
INSERT INTO `f_com_country` VALUES (176, '圣多美和普林西比', 'STP', '+239', 'SDMHPLXB');
INSERT INTO `f_com_country` VALUES (177, '圣赫勒拿', 'SHN', '+290', 'SHLN');
INSERT INTO `f_com_country` VALUES (178, '圣基茨和尼维斯', 'KNA', '+1869', 'SJCHNWS');
INSERT INTO `f_com_country` VALUES (179, '圣卢西亚', 'LCA', '+1758', 'SLXY');
INSERT INTO `f_com_country` VALUES (180, '圣马力诺', 'SMR', '+378', 'SMLN');
INSERT INTO `f_com_country` VALUES (181, '圣皮埃尔和米克隆群岛', 'SPM', '+', 'SPAEHMKLQD');
INSERT INTO `f_com_country` VALUES (182, '圣文森特和格林纳丁斯', 'VCT', '+1784', 'SWSTHGLNDS');
INSERT INTO `f_com_country` VALUES (183, '斯里兰卡', 'LKA', '+94', 'SLLK');
INSERT INTO `f_com_country` VALUES (184, '斯洛伐克', 'SVK', '+421', 'SLFK');
INSERT INTO `f_com_country` VALUES (185, '斯洛文尼亚', 'SVN', '+386', 'SLWNY');
INSERT INTO `f_com_country` VALUES (186, '斯瓦尔巴和扬马廷', 'SJM', '+', 'SWEBHYMT');
INSERT INTO `f_com_country` VALUES (187, '斯威士兰', 'SWZ', '+268', 'SWSL');
INSERT INTO `f_com_country` VALUES (188, '苏丹', 'SDN', '+249', 'SD');
INSERT INTO `f_com_country` VALUES (189, '苏里南', 'SUR', '+597', 'SLN');
INSERT INTO `f_com_country` VALUES (190, '所罗门群岛', 'SLB', '+677', 'SLMQD');
INSERT INTO `f_com_country` VALUES (191, '索马里', 'SOM', '+252', 'SML');
INSERT INTO `f_com_country` VALUES (192, '塔吉克斯坦', 'TJK', '+992', 'TJKST');
INSERT INTO `f_com_country` VALUES (193, '泰国', 'THA', '+66', 'TG');
INSERT INTO `f_com_country` VALUES (194, '坦桑尼亚', 'TZA', '+255', 'TSNY');
INSERT INTO `f_com_country` VALUES (195, '汤加', 'TON', '+676', 'TJ');
INSERT INTO `f_com_country` VALUES (196, '特克斯和凯克特斯群岛', 'TCA', '+', 'TKSHKKTSQD');
INSERT INTO `f_com_country` VALUES (197, '特里斯坦达昆哈', 'TAA', '+', 'TLSTDKH');
INSERT INTO `f_com_country` VALUES (198, '特立尼达和多巴哥', 'TTO', '+1868', 'TLNDHDBG');
INSERT INTO `f_com_country` VALUES (199, '突尼斯', 'TUN', '+216', 'TNS');
INSERT INTO `f_com_country` VALUES (200, '图瓦卢', 'TUV', '+688', 'TWL');
INSERT INTO `f_com_country` VALUES (201, '土耳其', 'TUR', '+90', 'TEQ');
INSERT INTO `f_com_country` VALUES (202, '土库曼斯坦', 'TKM', '+993', 'TKMST');
INSERT INTO `f_com_country` VALUES (203, '托克劳', 'TKL', '+690', 'TKL');
INSERT INTO `f_com_country` VALUES (204, '瓦利斯和福图纳', 'WLF', '+690', 'WLSHFTN');
INSERT INTO `f_com_country` VALUES (205, '瓦努阿图', 'VUT', '+678', 'WNAT');
INSERT INTO `f_com_country` VALUES (206, '危地马拉', 'GTM', '+502', 'WDML');
INSERT INTO `f_com_country` VALUES (207, '维尔京群岛美属', 'VIR', '+1-340', 'W');
INSERT INTO `f_com_country` VALUES (208, '维尔京群岛英属', 'VGB', '+1-284', 'W');
INSERT INTO `f_com_country` VALUES (209, '委内瑞拉', 'VEN', '+58', 'WNRL');
INSERT INTO `f_com_country` VALUES (210, '文莱', 'BRN', '+673', 'WL');
INSERT INTO `f_com_country` VALUES (211, '乌干达', 'UGA', '+256', 'WGD');
INSERT INTO `f_com_country` VALUES (212, '乌克兰', 'UKR', '+380', 'WKL');
INSERT INTO `f_com_country` VALUES (213, '乌拉圭', 'URY', '+598', 'WLG');
INSERT INTO `f_com_country` VALUES (214, '乌兹别克斯坦', 'UZB', '+998', 'WZBKST');
INSERT INTO `f_com_country` VALUES (215, '西班牙', 'ESP', '+34', 'XBY');
INSERT INTO `f_com_country` VALUES (216, '希腊', 'GRC', '+30', 'XL');
INSERT INTO `f_com_country` VALUES (217, '新加坡', 'SGP', '+65', 'XJP');
INSERT INTO `f_com_country` VALUES (218, '新喀里多尼亚', 'NCL', '+687', 'XKLDNY');
INSERT INTO `f_com_country` VALUES (219, '新西兰', 'NZL', '+64', 'XXL');
INSERT INTO `f_com_country` VALUES (220, '匈牙利', 'HUN', '+36', 'XYL');
INSERT INTO `f_com_country` VALUES (221, '叙利亚', 'SYR', '+963', 'XLY');
INSERT INTO `f_com_country` VALUES (222, '牙买加', 'JAM', '+1876', 'YMJ');
INSERT INTO `f_com_country` VALUES (223, '亚美尼亚', 'ARM', '+374', 'YMNY');
INSERT INTO `f_com_country` VALUES (224, '也门', 'YEM', '+967', 'YM');
INSERT INTO `f_com_country` VALUES (225, '伊拉克', 'IRQ', '+964', 'YLK');
INSERT INTO `f_com_country` VALUES (226, '伊朗', 'IRN', '+98', 'YL');
INSERT INTO `f_com_country` VALUES (227, '以色列', 'ISR', '+972', 'YSL');
INSERT INTO `f_com_country` VALUES (228, '意大利', 'ITA', '+39', 'YDL');
INSERT INTO `f_com_country` VALUES (229, '印度', 'IND', '+91', 'YD');
INSERT INTO `f_com_country` VALUES (230, '印度尼西亚', 'IDN', '+62', 'YDNXY');
INSERT INTO `f_com_country` VALUES (231, '英国', 'GBR', '+44', 'YG');
INSERT INTO `f_com_country` VALUES (232, '英属印度洋领地', 'IOT', '+246', 'YSYDYLD');
INSERT INTO `f_com_country` VALUES (233, '约旦', 'JOR', '+962', 'YD');
INSERT INTO `f_com_country` VALUES (234, '越南', 'VNM', '+84', 'YN');
INSERT INTO `f_com_country` VALUES (235, '赞比亚', 'ZMB', '+260', 'ZBY');
INSERT INTO `f_com_country` VALUES (236, '泽西岛', 'JEY', '+44', 'ZXD');
INSERT INTO `f_com_country` VALUES (237, '乍得', 'TCD', '+235', 'ZD');
INSERT INTO `f_com_country` VALUES (238, '直布罗陀', 'GIB', '+350', 'ZBLT');
INSERT INTO `f_com_country` VALUES (239, '智利', 'CHL', '+56', 'ZL');
INSERT INTO `f_com_country` VALUES (240, '中非共和国', 'CAF', '+236', 'ZFGHG');

-- ----------------------------
-- Table structure for f_com_keyword
-- ----------------------------
DROP TABLE IF EXISTS `f_com_keyword`;
CREATE TABLE `f_com_keyword`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kwords` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '关键词,空格分隔',
  `module_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '非系统模块id',
  `model_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '非系统模型id',
  `target_id` int(11) UNSIGNED NOT NULL COMMENT '主键值',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '分词表(或使用专业引擎或直接写到模型里) , 模块 , 模型 , 主键 , 多表的话查询当页在分开查' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_message
-- ----------------------------
DROP TABLE IF EXISTS `f_com_message`;
CREATE TABLE `f_com_message`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `send_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `from` int(10) UNSIGNED NOT NULL,
  `to` int(10) UNSIGNED NOT NULL,
  `summary` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `extra` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `from_status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: 默认,1:对from不显示',
  `to_status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '查询<>2 ,0: 默认(未读), 1:已读 , 2: 已被to假删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '消息' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_school
-- ----------------------------
DROP TABLE IF EXISTS `f_com_school`;
CREATE TABLE `f_com_school`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '校区名（max:32）',
  `area_code` char(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `lat` decimal(8, 6) NOT NULL COMMENT '纬度',
  `lng` decimal(9, 6) NOT NULL COMMENT '经度',
  `alias_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '学校别名 (max:64)',
  `address` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '学校地址（max:64）',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '市区下的学校' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_search_stat
-- ----------------------------
DROP TABLE IF EXISTS `f_com_search_stat`;
CREATE TABLE `f_com_search_stat`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `word` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `cnt` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '总次数',
  `module_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `model_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '搜索统计' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_session
-- ----------------------------
DROP TABLE IF EXISTS `f_com_session`;
CREATE TABLE `f_com_session`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `login_info` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `expire_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `device_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  `device_token` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `client_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登陆的客户端',
  `ip` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ip2long',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 50 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '登陆session' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_com_session
-- ----------------------------
INSERT INTO `f_com_session` VALUES (1, 'b65ea804bfa7042765b4ba759bd6c83a0', 1, '[]', 1512610153, '', 1512609853, 1512609853, '', 0, 0);
INSERT INTO `f_com_session` VALUES (2, 'ebd4f17334e9438ef92f296dd2a9097d0', 1, '[]', 1512610194, '', 1512609894, 1512609894, '', 0, 0);
INSERT INTO `f_com_session` VALUES (3, '50409c0a119c4bc0bb3c631033fd35180', 1, '[]', 1512610223, '', 1512609923, 1512609923, '', 0, 0);
INSERT INTO `f_com_session` VALUES (4, '691fdd13ca54b08190f2101e2e584f5e0', 1, '[]', 1513046719, '', 1513046419, 1513046419, '', 0, 0);
INSERT INTO `f_com_session` VALUES (5, '13d46ada43acb976f7a9ae67cf7619050', 1, '[]', 1516263544, '', 1516263244, 1516263244, '', 0, 0);
INSERT INTO `f_com_session` VALUES (6, '98a6f968b990f8008ebb98bdb41e2dc00', 1, '[]', 1516266307, '', 1516266007, 1516266007, '', 0, 0);
INSERT INTO `f_com_session` VALUES (7, 'e7532a6a99f97daac133c841255299e00', 1, '[]', 1516267629, '', 1516267329, 1516267329, '', 0, 0);
INSERT INTO `f_com_session` VALUES (8, 'e965c25156b3e2e1396f62728d5bc0f60', 1, '[]', 1516267634, '', 1516267334, 1516267334, '', 0, 0);
INSERT INTO `f_com_session` VALUES (9, '0858632d430946e33a0053a816887da60', 1, '[]', 1516327053, '', 1516326753, 1516326753, '', 0, 0);
INSERT INTO `f_com_session` VALUES (10, 'd43a9cfaf0ce7dcccb187608109c84cf0', 1, '[]', 1516692781, '', 1516692481, 1516692481, '', 0, 0);
INSERT INTO `f_com_session` VALUES (11, '2b95269155be4843ed73ed01f06fc5e80', 1, '[]', 1516697291, '', 1516696991, 1516696991, '', 0, 0);
INSERT INTO `f_com_session` VALUES (12, '6ab357bcbce9b1478e5672d8a1dc57350', 1, '[]', 1521618665, '', 1521618365, 1521618365, '', 0, 0);
INSERT INTO `f_com_session` VALUES (13, '3ec9ce37086c6a93b89738895f73c0b90', 1, '[]', 1523427275, '', 1523426975, 1523426975, '', 0, 0);
INSERT INTO `f_com_session` VALUES (14, '60e0fe8e9b3a48ef7722d3312389a4880', 1, '[]', 1526029285, '', 1526028985, 1526028985, '', 0, 0);
INSERT INTO `f_com_session` VALUES (15, 'c1f3e3503ed7a86a836cb8029ac216d51', 2, '[]', 1526461556, '', 1526461256, 1526461256, '', 0, 0);
INSERT INTO `f_com_session` VALUES (16, '31682d9cddfae57604b3e6f41926722e1', 2, '[]', 1526461621, '', 1526461321, 1526461321, '', 0, 0);
INSERT INTO `f_com_session` VALUES (17, '22cd844e0e7a01cd5d554fe8a77d18010', 1, '[]', 1526462253, '', 1526461953, 1526461953, '', 0, 0);
INSERT INTO `f_com_session` VALUES (18, '033bacc7b1e8d1730b9e01cd201844980', 1, '[]', 1527320525, '', 1527320225, 1527320225, '', 0, 0);
INSERT INTO `f_com_session` VALUES (19, '1034a0b3637876b16c4c86eb51b47a070', 1, '[]', 1527320581, '', 1527320281, 1527320281, '', 0, 0);
INSERT INTO `f_com_session` VALUES (20, 'bcf80743a6bf882301d54b8455953bae0', 1, '[]', 1527651583, '', 1527651283, 1527651283, '', 0, 0);
INSERT INTO `f_com_session` VALUES (21, 'ed778a05078c4b41a66f754e633f2d6f0', 1, '[]', 1530780912, '', 1530780612, 1530780612, '', 0, 0);
INSERT INTO `f_com_session` VALUES (22, '69db9c65ec486a7e8a7dd044803daa9c0', 1, '[]', 1533543586, '', 1533543286, 1533543286, '', 0, 0);
INSERT INTO `f_com_session` VALUES (23, '615c850e5b9b0f6d8bd983c7ffa94ddf0', 1, '[]', 1534232582, '', 1534232282, 1534232282, '', 0, 0);
INSERT INTO `f_com_session` VALUES (24, '487056c32ed99ceece0aab7e5c27228e0', 1, '[]', 1534233894, '', 1534233594, 1534233594, '', 0, 0);
INSERT INTO `f_com_session` VALUES (25, '8fdde97472b1c00e757153576fa4e69c0', 1, '[]', 1534234407, '', 1534234107, 1534234107, '', 0, 0);
INSERT INTO `f_com_session` VALUES (26, '9f3630c57bfa18137aff8a9d05debdf60', 1, '[]', 1534238501, '', 1534238201, 1534238201, '', 0, 0);
INSERT INTO `f_com_session` VALUES (27, '559a247b767b1e7ee43dc768227240f70', 1, '[]', 1534311543, '', 1534311243, 1534311243, '', 0, 0);
INSERT INTO `f_com_session` VALUES (28, '62d3cb9ef63ddd30338ed49f3a5bb3800', 1, '[]', 1534321137, '', 1534320837, 1534320837, '', 0, 0);
INSERT INTO `f_com_session` VALUES (29, 'fdb20302ff5ee25f05e035b3725337340', 1, '[]', 1534390969, '', 1534390669, 1534390669, '', 0, 0);
INSERT INTO `f_com_session` VALUES (30, 'f4d8ea2ac6e8712c9e914fef488ead1b0', 1, '[]', 1534484921, '', 1534484621, 1534484621, '', 0, 0);
INSERT INTO `f_com_session` VALUES (31, '57c99620ed98bedd1e590a67a1ba368b0', 1, '[]', 1534570950, '', 1534570650, 1534570650, '', 0, 0);
INSERT INTO `f_com_session` VALUES (32, '98c997237826e46f457f9dd82d02da3a0', 1, '[]', 1534735811, '', 1534735511, 1534735511, '', 0, 0);
INSERT INTO `f_com_session` VALUES (33, 'bf457866d3fb8fe25412aafb2c18894b0', 1, '[]', 1535348960, '', 1535348660, 1535348660, '', 0, 0);
INSERT INTO `f_com_session` VALUES (34, 'e2423c2233a9f03ae97ceedd01cff2fe0', 1, '[]', 1539682235, '', 1539681935, 1539681935, '', 0, 0);
INSERT INTO `f_com_session` VALUES (35, 'e1dde9eca65286778a973be72aaf7f0c0', 1, '[]', 1541576784, '', 1541576484, 1541576484, '', 0, 0);
INSERT INTO `f_com_session` VALUES (36, '56df5af17d4617f9456b2c0c3f7344700', 1, '[]', 1541643178, '', 1541642878, 1541642878, '', 0, 0);
INSERT INTO `f_com_session` VALUES (37, '50a18286be72c8a820c3aa14cdcd3d3d0', 1, '[]', 1542011250, '', 1542010950, 1542010950, '', 0, 0);
INSERT INTO `f_com_session` VALUES (38, 'e6909d3454b1fac05bc5a345e5e8815f0', 1, '[]', 1542015343, '', 1542015043, 1542015043, '', 0, 0);
INSERT INTO `f_com_session` VALUES (39, '4d2dbce562680f51f1965c38c6c2cf730', 1, '[]', 1542249228, 'web', 1542248928, 1542248928, '', 0, 0);
INSERT INTO `f_com_session` VALUES (40, 'dec2c0ff280440da03ab708b28341af90', 1, '[]', 1542590050, 'web', 1542589750, 1542589750, '', 0, 0);
INSERT INTO `f_com_session` VALUES (41, 'e0524198ce6447e52dea81c7b009564f0', 1, '[]', 1542592895, 'web', 1542592595, 1542592595, '', 0, 0);
INSERT INTO `f_com_session` VALUES (42, 'af4f35cc13eb8b4237671f84aeeff3bb0', 1, '[]', 1542593021, 'web', 1542592721, 1542592721, '', 0, 0);
INSERT INTO `f_com_session` VALUES (43, 'b25f93a6cdb7ba2427f74194bfd7f9dd0', 1, '[]', 1542605578, 'web', 1542605278, 1542605278, '', 0, 0);
INSERT INTO `f_com_session` VALUES (44, '1406054a8e8eef22cdefc8337bb15c3d0', 1, '[]', 1542623083, 'web', 1542622783, 1542622783, '', 0, 0);
INSERT INTO `f_com_session` VALUES (45, 'af98f9bbb9481588f3e19167fb1f9c2f0', 1, '[]', 1542679199, 'web', 1542678899, 1542678899, '', 0, 0);
INSERT INTO `f_com_session` VALUES (46, 'c688271be60029b51c50e440ecb3a2e70', 1, '[]', 1542762776, 'web', 1542762476, 1542762476, '', 0, 0);
INSERT INTO `f_com_session` VALUES (47, 'e8d6ebff24449004bbe74a2c80c099fa0', 1, '[]', 1542769030, 'web', 1542768730, 1542768730, '', 0, 0);
INSERT INTO `f_com_session` VALUES (48, '0bb2f51ba23e20451b62f3aefe651f630', 1, '[]', 1542778569, 'web', 1542778269, 1542778269, '', 0, 0);
INSERT INTO `f_com_session` VALUES (49, '16c725a35d53052380c58f6edc04b1170', 1, '[]', 1542848960, 'web', 1542848660, 1542848660, '', 0, 0);

-- ----------------------------
-- Table structure for f_com_signin
-- ----------------------------
DROP TABLE IF EXISTS `f_com_signin`;
CREATE TABLE `f_com_signin`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `sign_in_time` int(11) NOT NULL COMMENT '签到时间',
  `continues_signin` int(11) NOT NULL COMMENT '连续签到次数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '签到表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_com_tag
-- ----------------------------
DROP TABLE IF EXISTS `f_com_tag`;
CREATE TABLE `f_com_tag`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标签名',
  `md5_name` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'name字段的md5值，用来比较',
  `cnt` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '使用次数',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'dtree,类型,帖子,商品,...',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '标签定义表 ' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_device
-- ----------------------------
DROP TABLE IF EXISTS `f_device`;
CREATE TABLE `f_device`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `token` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备标识（用于友盟推送）',
  `type` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备类型（iPhone,android）',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `version` char(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备系统版本',
  `model` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机类型、型号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户绑定设备' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_device_unbind
-- ----------------------------
DROP TABLE IF EXISTS `f_device_unbind`;
CREATE TABLE `f_device_unbind`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `token` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备标识（用于友盟推送）',
  `type` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备类型（iPhone,android）',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `version` char(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备系统版本',
  `model` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机类型、型号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户解绑历史' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_file
-- ----------------------------
DROP TABLE IF EXISTS `f_file`;
CREATE TABLE `f_file`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '本地路径',
  `uid` int(10) UNSIGNED NOT NULL,
  `ori_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '原始文件名',
  `save_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '保存文件名',
  `size` int(11) DEFAULT 0 COMMENT '文件大小(B)',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件链接',
  `md5` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文件md5',
  `sha1` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态(1=>显示,0=>删除)',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `type` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '自定义类型',
  `ext` char(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '后缀',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户上传的其他文件' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_file_audio
-- ----------------------------
DROP TABLE IF EXISTS `f_file_audio`;
CREATE TABLE `f_file_audio`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ori_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `path` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `uid` int(11) NOT NULL COMMENT '上传用户uid',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `save_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `size` int(11) UNSIGNED NOT NULL COMMENT '文件大小',
  `duration` int(11) NOT NULL COMMENT '音频时长（单位：秒）',
  `md5` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `sha1` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` int(2) UNSIGNED NOT NULL COMMENT '状态(1=>显示,0=>删除)',
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '文件所属类型,查询用(other,question,shop)',
  `ext` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_file_audio
-- ----------------------------
INSERT INTO `f_file_audio` VALUES (2, 'lingshen.mp3', '/upload/userAudio/other/20170328/2e7bfdc4e1fe071eb21f577ae974477f.mp3', 1, 1491449819, 1491449819, '2e7bfdc4e1fe071eb21f577ae974477f.mp3', 34305, 2, 'b42497e41a2803a293b7576ccc76c6c2', '803a4f88118a0ad858dc1e9b94207847158fe70f', 1, 'other', 'mp3');
INSERT INTO `f_file_audio` VALUES (3, 'song1.mp3', '/upload/userAudio/other/20170406/17d3575c5e275643bca717595df58009.mp3', 1, 1491449819, 1491449819, '17d3575c5e275643bca717595df58009.mp3', 474500, 29, '354b39932fafc084ce2eef062be2951e', 'f3de1f851298b5157556dd5f514db9e416802301', 1, 'other', 'mp3');

-- ----------------------------
-- Table structure for f_file_picture
-- ----------------------------
DROP TABLE IF EXISTS `f_file_picture`;
CREATE TABLE `f_file_picture`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '本地路径',
  `uid` int(10) UNSIGNED NOT NULL,
  `ori_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '原始文件名',
  `save_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '保存文件名',
  `size` int(11) DEFAULT 0 COMMENT '文件大小(B)',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片链接',
  `imgurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `md5` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文件md5',
  `sha1` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态(1=>显示,0=>删除)',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `type` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片',
  `ext` char(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '后缀',
  `porn_prop` decimal(16, 2) NOT NULL DEFAULT -1.00 COMMENT '图片色情值-1为未知',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户上传的图片记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_file_picture
-- ----------------------------
INSERT INTO `f_file_picture` VALUES (1, '/upload/userPicture/other/20170216/fa072726519c37e18d071381c8598628.jpg', 1, 'gh_346e7dc226cc_344.jpg', 'fa072726519c37e18d071381c8598628.jpg', 10791, '', 'http://api.ewelisten.itboye.com/upload/userPicture/other/20170216/fa072726519c37e18d071381c8598628.jpg', 'c9e28fd64fbf7570e9606ed6b3c5e2f5', '6e1e1ccd8ea9e47ddfa4ab3d2f6953301d5cc21b', 0, 1487236701, 'other', 'jpg', -1.00);
INSERT INTO `f_file_picture` VALUES (5, '/upload/userPicture/avatar/20170317/bebcd8875ff001d5caa3656a4c97cd8c.png', 171, '易微听_09-05.png', 'bebcd8875ff001d5caa3656a4c97cd8c.png', 1989, '', 'http://ewt/upload/userPicture/avatar/20170317/bebcd8875ff001d5caa3656a4c97cd8c.png', 'b3929f6a071eada5620a1f2062d4cbc4', '66a3766c8b43ea79f54975b072cbbf01f54be6d1', 0, 1489717416, 'avatar', 'png', -1.00);
INSERT INTO `f_file_picture` VALUES (6, '/upload/userPicture/other/20171205/cb65bf57126ccb461eeaca3cde0e4b13.png', 1, '404.png', 'cb65bf57126ccb461eeaca3cde0e4b13.png', 6645, '', 'http://tp51/upload/userPicture/other/20171205/cb65bf57126ccb461eeaca3cde0e4b13.png', '714488364b677c5ec2431150c27080a0', '1dd162c98c5c686c2cd80d34b32eb1cfeea956f2', 1, 1512464465, 'other', 'png', -1.00);
INSERT INTO `f_file_picture` VALUES (7, '/upload/userPicture/other/20171208/2a18fd183fe71adba3479e06a0826407.png', 1, 'index.png', '2a18fd183fe71adba3479e06a0826407.png', 3887, '', 'http://tp51/upload/userPicture/other/20171208/2a18fd183fe71adba3479e06a0826407.png', '3b841d8721e2c09618565bf10e0cfd37', 'aeb7dfd6b7d546b7307346a1f68c176351a445fd', 1, 1512723726, 'other', 'png', -1.00);
INSERT INTO `f_file_picture` VALUES (8, '/upload/userPicture/imImage/20180320/db6e3680416f8ced664c950d9052f89a.png', 1, '111.png', 'db6e3680416f8ced664c950d9052f89a.png', 4303, '', 'http://tp51/upload/userPicture/imImage/20180320/db6e3680416f8ced664c950d9052f89a.png', 'df748fc5f6f7e742827a992a0550385c', 'eef08ccfe37813ced551a6e4bed6cfa8193997e1', 1, 1521527862, 'imImage', 'png', -1.00);
INSERT INTO `f_file_picture` VALUES (9, '/upload/ueditor/image/20180522/1526979365292616.png', 0, 'index.png', '', 3887, '', 'http://tp51/upload/ueditor/image/20180522/1526979365292616.png', '3b841d8721e2c09618565bf10e0cfd37', 'aeb7dfd6b7d546b7307346a1f68c176351a445fd', 0, 1526979365, 'ueditor', 'png', -1.00);
INSERT INTO `f_file_picture` VALUES (10, '/upload/ueditor/image/20180522/1526979702649779.png', 0, 'index.png', '', 3887, '', 'http://tp51/upload/ueditor/image/20180522/1526979702649779.png', '3b841d8721e2c09618565bf10e0cfd37', 'aeb7dfd6b7d546b7307346a1f68c176351a445fd', 0, 1526979702, 'ueditor', 'png', -1.00);
INSERT INTO `f_file_picture` VALUES (11, '/upload/userPicture/other/20180522/10768f523cd80d80296e33d944a79445.png', 1, 't_04.png', '10768f523cd80d80296e33d944a79445.png', 1738, '', 'http://tp51/upload/userPicture/other/20180522/10768f523cd80d80296e33d944a79445.png', '0702333734ca29d81726aa4d00b9dea5', '04c6bde72d6d55f9083a63761bc8eb5a527278b8', 0, 1526981300, 'other', 'png', -1.00);
INSERT INTO `f_file_picture` VALUES (12, '/upload/ueditor/image/20180522/1526981608796087.png', 0, 't_04.png', '', 1738, '', 'http://tp51/upload/ueditor/image/20180522/1526981608796087.png', '0702333734ca29d81726aa4d00b9dea5', '04c6bde72d6d55f9083a63761bc8eb5a527278b8', 0, 1526981608, 'ueditor', 'png', -1.00);
INSERT INTO `f_file_picture` VALUES (13, '/upload/userPicture/other/20180523/1f2396295817e921ea9f8ad8487e9921.jpg', 1, '下载_03.jpg', '1f2396295817e921ea9f8ad8487e9921.jpg', 1945, '', 'http://tp51/upload/userPicture/other/20180523/1f2396295817e921ea9f8ad8487e9921.jpg', '887f91d0c38f6e63200b1d2aacf71083', '37443df7e824e38ce179d5dffe104c94fd195d3e', 1, 1527064138, 'other', 'jpg', -1.00);

-- ----------------------------
-- Table structure for f_fy_account
-- ----------------------------
DROP TABLE IF EXISTS `f_fy_account`;
CREATE TABLE `f_fy_account`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '添加用户',
  `pid` bigint(32) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '推广位名字',
  `pid_src` varchar(127) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '原始pid',
  `invite_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '推广链接',
  `invite_url_sm` varchar(127) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '短连接,300d',
  `token` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '淘口令,30d',
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  `to_uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分配给谁了,已废弃',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `pid`(`pid`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 98 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '推广位信息' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_fy_account
-- ----------------------------
INSERT INTO `f_fy_account` VALUES (78, 1, 16661700406, '张德重测试33', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&amp;pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1534919983, 1535028876, 0);
INSERT INTO `f_fy_account` VALUES (79, 1, 16661700402, '张德重测试', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1534939960, 1534939960, 0);
INSERT INTO `f_fy_account` VALUES (90, 1, 16661700404, '张德重测试', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1535079863, 1535079863, 0);
INSERT INTO `f_fy_account` VALUES (91, 1, 16661700401, '张德重测试1', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1535079863, 1535079863, 0);
INSERT INTO `f_fy_account` VALUES (92, 1, 16661700405, '张德重测试2', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1535079863, 1535079863, 0);
INSERT INTO `f_fy_account` VALUES (93, 1, 16661700400, '张德重测试3', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1535079863, 1535079863, 0);
INSERT INTO `f_fy_account` VALUES (94, 1, 16661700407, '张德重测试', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1535079869, 1535079869, 0);
INSERT INTO `f_fy_account` VALUES (96, 1, 16661700409, '张德重测试2', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1535079870, 1535079870, 0);
INSERT INTO `f_fy_account` VALUES (97, 1, 16661700410, '张德重测试2', 'mm_119852206_76700059_16661700406', 'https://mos.m.taobao.com/activity_newer?from=pub&pid=mm_119852206_76700059_16661700406', 'https://s.click.taobao.com/3gnQWNw', '€lUk7bXHz0K8€', 1535079870, 1535079870, 0);

-- ----------------------------
-- Table structure for f_fy_account_user
-- ----------------------------
DROP TABLE IF EXISTS `f_fy_account_user`;
CREATE TABLE `f_fy_account_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '添加用户',
  `pid` bigint(20) UNSIGNED NOT NULL COMMENT 'account 主键',
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  `parent_uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分配给谁了,已废弃',
  `level` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '层级',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uid`(`uid`, `pid`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 102 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '推广位用户关系表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_fy_account_user
-- ----------------------------
INSERT INTO `f_fy_account_user` VALUES (98, 8, 97, 1535424981, 1535424981, 1, 0);
INSERT INTO `f_fy_account_user` VALUES (99, 10, 96, 1540619062, 1540619062, 1, 0);
INSERT INTO `f_fy_account_user` VALUES (100, 13, 96, 1540625052, 1540625052, 10, 1);
INSERT INTO `f_fy_account_user` VALUES (101, 8, 94, 1541491883, 1541491883, 1, 0);

-- ----------------------------
-- Table structure for f_fy_invite
-- ----------------------------
DROP TABLE IF EXISTS `f_fy_invite`;
CREATE TABLE `f_fy_invite`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  `reg_time` int(10) UNSIGNED NOT NULL COMMENT '注册时间',
  `login_time` int(10) UNSIGNED NOT NULL COMMENT '支付宝登陆时间',
  `active_time` int(11) UNSIGNED NOT NULL COMMENT '激活时间',
  `first_buy_time` int(10) UNSIGNED NOT NULL COMMENT '首购时间',
  `receive_time` int(10) UNSIGNED NOT NULL COMMENT '确认收货时间',
  `user_mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '新人手机号',
  `user_status` tinyint(3) UNSIGNED NOT NULL COMMENT '新人状态;1已激活,2以首购',
  `order_type` tinyint(3) NOT NULL COMMENT '订单类型;1未完成首购,2淘客订单,3非淘客订单',
  `media_id` bigint(20) NOT NULL COMMENT '来源媒体id',
  `site_id` bigint(20) NOT NULL COMMENT '网站id',
  `pid` bigint(32) UNSIGNED NOT NULL COMMENT '推广位id',
  `pid_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '推广位名字',
  `act_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '活动名字',
  `order_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单编号',
  `add_uid` int(10) UNSIGNED NOT NULL COMMENT '添加人',
  `money` int(255) NOT NULL COMMENT '佣金',
  `invite_uid` int(10) UNSIGNED NOT NULL COMMENT '邀请人',
  `real_time` int(10) UNSIGNED NOT NULL COMMENT '实名时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 102 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '拉新数据表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_fy_invite
-- ----------------------------
INSERT INTO `f_fy_invite` VALUES (32, 1539004000, 1539314000, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 76700059, '张德重测试', '手淘拉新7月活动', '203483682593591175', 1, 0, 0, 0);
INSERT INTO `f_fy_invite` VALUES (33, 1539314109, 1539314109, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 76700059, '张德重测试2', '手淘拉新7月活动', '203483682593591175', 1, 0, 0, 0);
INSERT INTO `f_fy_invite` VALUES (34, 1539315062, 1539315062, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 16661700410, '张德重测试2', '手淘拉新8月活动', '203483682593591175', 1, 0, 8, 0);
INSERT INTO `f_fy_invite` VALUES (35, 1539315086, 1539315086, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 16661700410, '张德重测试2', '手淘拉新8月活动', '203483682593591175', 1, 0, 8, 0);
INSERT INTO `f_fy_invite` VALUES (36, 1539315130, 1539315130, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 16661700410, '张德重测试2', '手淘拉新8月活动', '203483682593591175', 1, 0, 8, 0);
INSERT INTO `f_fy_invite` VALUES (37, 1539315182, 1539315182, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 16661700410, '张德重测试2', '手淘拉新8月活动', '203483682593591175', 1, 0, 8, 0);
INSERT INTO `f_fy_invite` VALUES (38, 1539315213, 1539315213, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 16661700410, '张德重测试2', '手淘拉新8月活动', '203483682593591175', 1, 0, 8, 0);
INSERT INTO `f_fy_invite` VALUES (39, 1539329960, 1539329960, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 16661700410, '张德重测试2', '手淘拉新8月活动', '203483682593591175', 1, 0, 8, 0);
INSERT INTO `f_fy_invite` VALUES (40, 1539330039, 1539330039, 1534681849, 0, 1534681850, 0, 0, '188****0316', 1, 1, 3, 119852206, 16661700410, '张德重测试2', '手淘拉新8月活动', '203483682593591175', 1, 0, 8, 0);
INSERT INTO `f_fy_invite` VALUES (41, 1539331486, 1539331486, 1534681849, 1, 1534681850, 1534681850, 2, '188****0316', 2, 0, 3, 119852206, 16661700410, '张德重测试2', '手淘拉新8月活动', '203483682593591175', 1, 5000, 8, 0);
INSERT INTO `f_fy_invite` VALUES (70, 1540793327, 1540793327, 1534674576, 0, 1534674577, 1534674716, 0, '183****0539', 2, 0, 0, 119852206, 16661700410, '推广位测试10', '手淘拉新8月活动', '203542284487580051', 1, 3000, 8, 0);
INSERT INTO `f_fy_invite` VALUES (73, 1540796307, 1540796307, 1534674576, 0, 1534674577, 1534674716, 0, '183****0539', 2, 0, 0, 119852206, 16661700410, '推广位测试10', '手淘拉新8月活动', '203542284487580051', 1, 3000, 0, 0);
INSERT INTO `f_fy_invite` VALUES (74, 1540796411, 1540796411, 1534674576, 0, 1534674577, 1534674716, 0, '183****0539', 2, 0, 0, 119852206, 16661700410, '推广位测试10', '手淘拉新8月活动', '203542284487580051', 1, 3000, 8, 0);
INSERT INTO `f_fy_invite` VALUES (75, 1540796518, 1540796518, 1534674576, 0, 1534674577, 1534674716, 0, '183****0539', 2, 0, 0, 119852206, 16661700410, '推广位测试10', '手淘拉新8月活动', '203542284487580051', 1, 3000, 8, 0);
INSERT INTO `f_fy_invite` VALUES (97, 1540798896, 1540798896, 1534674576, 0, 1534674577, 1534674716, 0, '183****0539', 2, 0, 0, 119852206, 16661700409, '推广位测试10', '手淘拉新8月活动', '203542284487580051', 1, 3000, 10, 0);
INSERT INTO `f_fy_invite` VALUES (99, 1540799579, 1540799579, 1534674576, 0, 1534674577, 1534674716, 0, '183****0539', 2, 0, 0, 119852206, 16661700409, '推广位测试10', '手淘拉新8月活动', '203542284487580051', 1, 3000, 10, 0);
INSERT INTO `f_fy_invite` VALUES (100, 1540800189, 1540800189, 1534674576, 0, 1534674577, 1534674716, 0, '183****0539', 2, 0, 0, 119852206, 16661700409, '推广位测试10', '手淘拉新8月活动', '203542284487580051', 1, 3000, 0, 0);
INSERT INTO `f_fy_invite` VALUES (101, 1540800324, 1540800324, 1534674576, 0, 1534674577, 1534674716, 0, '183****0539', 2, 0, 0, 119852206, 16661700409, '推广位测试10', '手淘拉新8月活动', '203542284487580051', 1, 3000, 0, 0);

-- ----------------------------
-- Table structure for f_im_msg
-- ----------------------------
DROP TABLE IF EXISTS `f_im_msg`;
CREATE TABLE `f_im_msg`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(11) UNSIGNED NOT NULL,
  `update_time` int(11) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL COMMENT '0未读,1已读',
  `uid` int(11) UNSIGNED NOT NULL,
  `to_uid` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for f_im_user
-- ----------------------------
DROP TABLE IF EXISTS `f_im_user`;
CREATE TABLE `f_im_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL,
  `client` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 96 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for f_log_action
-- ----------------------------
DROP TABLE IF EXISTS `f_log_action`;
CREATE TABLE `f_log_action`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '行为id',
  `user_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '执行者编号',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '触发行为的数据id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '执行行为的时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '行为日志表' ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for f_log_api
-- ----------------------------
DROP TABLE IF EXISTS `f_log_api`;
CREATE TABLE `f_log_api`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for f_log_cron
-- ----------------------------
DROP TABLE IF EXISTS `f_log_cron`;
CREATE TABLE `f_log_cron`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for f_log_err
-- ----------------------------
DROP TABLE IF EXISTS `f_log_err`;
CREATE TABLE `f_log_err`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for f_log_test
-- ----------------------------
DROP TABLE IF EXISTS `f_log_test`;
CREATE TABLE `f_log_test`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `get` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `post` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ext` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '后端临时调试表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for f_oauth_client
-- ----------------------------
DROP TABLE IF EXISTS `f_oauth_client`;
CREATE TABLE `f_oauth_client`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Unique client identifier',
  `client_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `client_secret` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Client secret',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `redirect_uri` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'Redirect URI used for Authorization Grant',
  `grant_types` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'Dot-delimited list of grant types permitted, null = all',
  `scope` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'Space-delimited list of approved scopes',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to oauth_users.user_id',
  `public_key` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'Public key for encryption',
  `api_alg` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'md5',
  `api_auth` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'api权限,api验证1/2',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_oauth_client
-- ----------------------------
INSERT INTO `f_oauth_client` VALUES (1, 'by565fa4e56a9241', 'ios', 'c37725a62af42ea0569d79b1942935be', 'd', '#', 'client_credentials', 'base', 1, '', 'md5', '2');
INSERT INTO `f_oauth_client` VALUES (2, 'by565fa4facdb191', 'android', 'b6b27d3182d589b92424cac0f2876fcd', '', '#', 'client_credentials', 'base', 1, '', 'md5', '1,2');
INSERT INTO `f_oauth_client` VALUES (3, 'by571846d03009e1', 'admin', '964561983083ac622f03389051f112e5', '', '#', 'client_credentials', 'base', 1, '', 'md5', '');
INSERT INTO `f_oauth_client` VALUES (4, 'by58018f50cfcae1', 'test', 'cb0bfaf5b9b2f53a216bf518e18fef18', '', '#', 'password,client_credentials,refresh_token', 'base', 1, '', 'md5', '1,2');

-- ----------------------------
-- Table structure for f_order
-- ----------------------------
DROP TABLE IF EXISTS `f_order`;
CREATE TABLE `f_order`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '下单用户id',
  `order_code` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '精确到秒，毫秒',
  `price` int(10) UNSIGNED NOT NULL COMMENT '总价,包含运费,不算优惠',
  `post_price` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '该订单运费',
  `note` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
  `status` tinyint(2) NOT NULL COMMENT '数据状态,-1 已删除 0：已禁用 1：正常 2：待审核',
  `pay_status` tinyint(2) NOT NULL COMMENT '支付状态（0:未付款 1：已付款  2：已退款）',
  `order_status` tinyint(2) NOT NULL COMMENT '订单状态（2待确认，3待发货，4已发货，5已收货，6已退货，7已完成，8取消或交易关闭，12订单退回）',
  `cs_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '售后状态(0: 初始状态 2 待处理 3 已处理)',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `comment_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0：待评价 1：手动评价 2：自动评价',
  `from` int(11) NOT NULL DEFAULT 1 COMMENT '来源:1、PC,2、Android,3、IOS',
  `discount_money` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '优惠金额(该订单包含商品的总计),视业务',
  `store_id` int(11) NOT NULL DEFAULT 0 COMMENT '店铺ID',
  `goods_amount` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '货款,视业务',
  `pay_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '支付类型(1: 支付宝)',
  `pay_code` char(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '交易号',
  `pay_balance` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付的余额,1:1',
  `invite_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '介绍人uid',
  `dt_type` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'dtree订单类型, 商品,余额充值,生活,维修,..',
  `bill_type` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=>个人,2=>企业,视业务',
  `bill_title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '发票抬头,视业务',
  `bill_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '企业发票税号,视业务',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单主表,分店铺生成' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_order_comment
-- ----------------------------
DROP TABLE IF EXISTS `f_order_comment`;
CREATE TABLE `f_order_comment`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '商品id',
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `score` decimal(11, 2) NOT NULL DEFAULT 0.00 COMMENT '打分(0-5分)',
  `comment` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '评论文字',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `order_code` char(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `logistics_service` decimal(11, 2) NOT NULL DEFAULT 5.00 COMMENT '物流服务',
  `delivery_speed` decimal(11, 2) NOT NULL DEFAULT 5.00 COMMENT '发货速度',
  `service_attitude` decimal(11, 2) NOT NULL DEFAULT 5.00 COMMENT '服务态度',
  `group_id` int(11) UNSIGNED DEFAULT 0 COMMENT '分组ID',
  `package_id` int(11) UNSIGNED DEFAULT 0 COMMENT '套餐ID',
  `psku_id` int(11) UNSIGNED DEFAULT 0 COMMENT '规格ID',
  `item_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单项id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单和订单项评论,多了再拆分' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_order_comment_attach
-- ----------------------------
DROP TABLE IF EXISTS `f_order_comment_attach`;
CREATE TABLE `f_order_comment_attach`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `img_id` int(11) UNSIGNED NOT NULL COMMENT '图片ID',
  `comment_id` int(64) UNSIGNED NOT NULL COMMENT '订单评论id',
  `create_time` int(11) UNSIGNED NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `pic_com`(`img_id`, `comment_id`) USING BTREE COMMENT '每个评价单位图片不同'
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '评论附件（图片）' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_order_paycode
-- ----------------------------
DROP TABLE IF EXISTS `f_order_paycode`;
CREATE TABLE `f_order_paycode`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单内容',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `pay_type` int(11) NOT NULL DEFAULT 1 COMMENT '支付类型 1支付宝',
  `pay_money` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '应支付金额 ( 单位：分 )',
  `true_pay_money` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实付金额（单位：分）',
  `pay_code` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '支付单号',
  `trade_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `pay_balance` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付的余额数',
  `pay_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '用户是否支付成功',
  `dt_currency` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'rmb' COMMENT '支付的货币单位',
  `b_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '  业务状态(2: 订单金额不一致 3:订单货币单位不一致)',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单支付信息表 (若干订单获取的支付信息)' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_order_status_his
-- ----------------------------
DROP TABLE IF EXISTS `f_order_status_his`;
CREATE TABLE `f_order_status_his`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reason` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '状态变更原因',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '变更时间',
  `is_auto` tinyint(2) NOT NULL COMMENT '是否是自动变更',
  `op_uid` int(10) UNSIGNED NOT NULL COMMENT '操作人ID',
  `order_code` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cur_status` tinyint(2) NOT NULL,
  `next_status` tinyint(2) NOT NULL,
  `status_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '类型（PAY: 支付状态 ORDER:订单状态 STATUS: 数据状态）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单状态变更记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_alipay_notify
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_alipay_notify`;
CREATE TABLE `f_shop_alipay_notify`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `discount` decimal(16, 2) NOT NULL DEFAULT 0.00,
  `payment_type` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `subject` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `trade_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `buyer_email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `gmt_create` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `notify_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `quantity` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `out_trade_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `seller_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `notify_time` int(11) NOT NULL DEFAULT 0,
  `body` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `trade_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `is_total_fee_adjust` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `total_fee` decimal(16, 2) NOT NULL DEFAULT 0.00,
  `gmt_payment` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `seller_email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `price` decimal(16, 2) NOT NULL DEFAULT 0.00,
  `buyer_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `notify_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `use_coupon` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `sign_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `sign` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '记录时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 65 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '支付宝回调历史' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for f_shop_coupon
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_coupon`;
CREATE TABLE `f_shop_coupon`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '拥有者的ID',
  `create_time` int(11) NOT NULL COMMENT '获得时间',
  `tpl_id` int(11) NOT NULL DEFAULT 0 COMMENT '红包模版ID',
  `use_status` tinyint(2) NOT NULL COMMENT '使用状态（0: 未使用，1:已使用）',
  `notes` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `money` decimal(16, 2) NOT NULL,
  `expire_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '红包表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_coupon_tpl
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_coupon_tpl`;
CREATE TABLE `f_shop_coupon_tpl`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dt_type` int(11) NOT NULL COMMENT '红包类别',
  `status` tinyint(4) NOT NULL COMMENT '是否开启',
  `condition` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '使用条件',
  `expire_time` int(11) NOT NULL COMMENT '过期时间',
  `money` decimal(16, 2) NOT NULL COMMENT '可抵扣金额',
  `create_time` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `notes` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '优惠券模版' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_freight
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_freight`;
CREATE TABLE `f_shop_freight`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '模板名称',
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '按（件数1，重量2，体积3）',
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配送公司',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户的运费模板' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_shop_freight
-- ----------------------------
INSERT INTO `f_shop_freight` VALUES (4, '我的运费模版1', 1, '6026', 2, 0, 0);
INSERT INTO `f_shop_freight` VALUES (5, '我的运费模版2', 1, '6026', 2, 0, 0);
INSERT INTO `f_shop_freight` VALUES (6, '标准运费', 2, '6027', 2, 0, 0);
INSERT INTO `f_shop_freight` VALUES (7, '全国包邮', 2, '6027', 2, 0, 0);
INSERT INTO `f_shop_freight` VALUES (8, '江浙沪皖包邮', 2, '6027', 2, 0, 0);
INSERT INTO `f_shop_freight` VALUES (9, '14省包邮', 2, '6027', 2, 0, 0);
INSERT INTO `f_shop_freight` VALUES (10, '25省包邮', 2, '6027', 2, 0, 0);
INSERT INTO `f_shop_freight` VALUES (11, '申通快递', 1, '6027', 1, 0, 0);
INSERT INTO `f_shop_freight` VALUES (13, '模板3', 1, '6027', 2, 0, 0);
INSERT INTO `f_shop_freight` VALUES (14, '中通快递', 1, '6027', 1, 0, 0);
INSERT INTO `f_shop_freight` VALUES (15, '中通快递', 1, '6027', 61, 0, 0);
INSERT INTO `f_shop_freight` VALUES (16, '海外快递', 1, '6027', 61, 0, 0);
INSERT INTO `f_shop_freight` VALUES (36, '运费模版测试', 1, '6026', 1, 0, 0);

-- ----------------------------
-- Table structure for f_shop_freight_detail
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_freight_detail`;
CREATE TABLE `f_shop_freight_detail`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `area_ids` varchar(6000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '地区Id集合',
  `areas` varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '地址集合',
  `first_piece` int(10) UNSIGNED NOT NULL COMMENT '首件/每单位，单位由模版确定',
  `first_money` int(10) UNSIGNED NOT NULL COMMENT '首费/每单位，单位由模版确定',
  `next_piece` int(10) UNSIGNED NOT NULL COMMENT '续件',
  `next_money` int(10) UNSIGNED NOT NULL COMMENT '续费',
  `template_id` int(10) UNSIGNED NOT NULL COMMENT '模板ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户模板详情' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_shop_freight_detail
-- ----------------------------
INSERT INTO `f_shop_freight_detail` VALUES (1, '0', '全国 [默认运费]', 9, 9, 9, 9, 6);
INSERT INTO `f_shop_freight_detail` VALUES (2, '370100,370200,370300,370400,370500,370600,370700,370800,370900,371000,371100,371200,371300,371400,371500,371600,371700,120000,410100,410200,410300,410400,410500,410600,410700,410800,410900,411000,411100,411200,411300,411400,411500,411600,411700,350100,350200,350300,350400,350500,350600,350700,350800,350900,130100,130300,130200,130400,130500,130600,130700,130800,130900,131000,131100,110000,360100,360200,360300,360400,360500,360600,360700,360800,360900,361000,361100,440100,440200,440300,440400,440500,440600,440700,440800,440900,441200,441300,441400,441500,441600,441700,441800,441900,442000,445100,445200,445300,430100,430200,430300,430400,430500,430600,430700,430800,430900,431000,431100,431200,431300,433100,420100,420200,420300,420500,420600,420700,420800,420900,421000,421100,421200,421300,429000,422800', '济南市,青岛市,淄博市,枣庄市,东营市,烟台市,潍坊市,济宁市,泰安市,威海市,日照市,莱芜市,临沂市,德州市,聊城市,滨州市,荷泽市,天津市,郑州市,开封市,洛阳市,平顶山市,安阳市,鹤壁市,新乡市,焦作市,濮阳市,许昌市,漯河市,三门峡市,南阳市,商丘市,信阳市,周口市,驻马店市,福州市,厦门市,莆田市,三明市,泉州市,漳州市,南平市,龙岩市,宁德市,石家庄市,秦皇岛市,唐山市,邯郸市,邢台市,保定市,张家口市,承德市,沧州市,廊坊市,衡水市,北京市,南昌市,景德镇市,萍乡市,九江市,新余市,鹰潭市,赣州市,吉安市,宜春市,抚州市,上饶市,广州市,韶关市,深圳市,珠海市,汕头市,佛山市,江门市,湛江市,茂名市,肇庆市,惠州市,梅州市,汕尾市,河源市,阳江市,清远市,东莞市,中山市,潮州市,揭阳市,云浮市,长沙市,株洲市,湘潭市,衡阳市,邵阳市,岳阳市,常德市,张家界市,益阳市,郴州市,永州市,怀化市,娄底市,湘西土家族苗族自治州,武汉市,黄石市,十堰市,宜昌市,襄樊市,鄂州市,荆门市,孝感市,荆州市,黄冈市,咸宁市,随州市,省直辖行政单位,恩施土家族苗族自治州', 1, 6, 1, 3, 6);
INSERT INTO `f_shop_freight_detail` VALUES (3, '330100,330200,330300,330400,330500,330600,330700,330800,330900,331000,331100,310000,320100,320200,320300,320400,320500,320600,320700,320800,320900,321000,321100,321200,321300,340100,340200,340300,340400,340500,340600,340700,340800,341000,341100,341200,341300,341400,341500,341600,341700,341800', '杭州市,宁波市,温州市,嘉兴市,湖州市,绍兴市,金华市,衢州市,舟山市,台州市,丽水市,上海市,南京市,无锡市,徐州市,常州市,苏州市,南通市,连云港市,淮安市,盐城市,扬州市,镇江市,泰州市,宿迁市,合肥市,芜湖市,蚌埠市,淮南市,马鞍山市,淮北市,铜陵市,安庆市,黄山市,滁州市,阜阳市,宿州市,巢湖市,六安市,亳州市,池州市,宣城市', 3, 6, 1, 1, 6);
INSERT INTO `f_shop_freight_detail` VALUES (4, '510100,510300,510400,510500,510600,510700,510800,510900,511000,511100,511300,511400,511500,511600,511700,511800,511900,512000,513200,513300,513400,610100,610200,610300,610400,610500,610600,610700,610800,610900,611000,230100,230200,230300,230400,230500,230600,230700,230800,230900,231000,231100,231200,232700,140100,140200,140300,140400,140500,140600,140700,140800,140900,141000,141100,530100,530300,530400,530500,530600,530700,530800,530900,532300,532500,532600,532800,532900,533100,533300,533400,520100,520200,520300,520400,522200,522300,522400,522600,522700,220100,220200,220300,220400,220500,220600,220700,220800,222400,110000,210100,210200,210300,210400,210500,210600,210700,210800,210900,211000,211100,211200,211300,211400,460100,460200,469000,450100,450200,450300,450400,450500,450600,450700,450800,450900,451000,451100,451200,451300,451400,500300', '成都市,自贡市,攀枝花市,泸州市,德阳市,绵阳市,广元市,遂宁市,内江市,乐山市,南充市,眉山市,宜宾市,广安市,达州市,雅安市,巴中市,资阳市,阿坝藏族羌族自治州,甘孜藏族自治州,凉山彝族自治州,西安市,铜川市,宝鸡市,咸阳市,渭南市,延安市,汉中市,榆林市,安康市,商洛市,哈尔滨市,齐齐哈尔市,鸡西市,鹤岗市,双鸭山市,大庆市,伊春市,佳木斯市,七台河市,牡丹江市,黑河市,绥化市,大兴安岭地区,太原市,大同市,阳泉市,长治市,晋城市,朔州市,晋中市,运城市,忻州市,临汾市,吕梁市,昆明市,曲靖市,玉溪市,保山市,昭通市,丽江市,思茅市,临沧市,楚雄彝族自治州,红河哈尼族彝族自治州,文山壮族苗族自治州,西双版纳傣族自治州,大理白族自治州,德宏傣族景颇族自治州,怒江傈僳族自治州,迪庆藏族自治州,贵阳市,六盘水市,遵义市,安顺市,铜仁地区,黔西南布依族苗族自治州,毕节地区,黔东南苗族侗族自治州,黔南布依族苗族自治州,长春市,吉林市,四平市,辽源市,通化市,白山市,松原市,白城市,延边朝鲜族自治州,北京市,沈阳市,大连市,鞍山市,抚顺市,本溪市,丹东市,锦州市,营口市,阜新市,辽阳市,盘锦市,铁岭市,朝阳市,葫芦岛市,海口市,三亚市,省直辖县级行政单位,南宁市,柳州市,桂林市,梧州市,北海市,防城港市,钦州市,贵港市,玉林市,百色市,贺州市,河池市,来宾市,崇左市,市', 1, 11, 1, 8, 6);
INSERT INTO `f_shop_freight_detail` VALUES (5, '630100,632100,632200,632300,632500,632600,632700,632800,620100,620200,620300,620400,620500,620600,620700,620800,620900,621000,621100,621200,622900,623000,150100,150200,150300,150400,150500,150600,150700,150800,150900,152200,152500,152900,640100,640200,640300,640400,640500', '西宁市,海东地区,海北藏族自治州,黄南藏族自治州,海南藏族自治州,果洛藏族自治州,玉树藏族自治州,海西蒙古族藏族自治州,兰州市,嘉峪关市,金昌市,白银市,天水市,武威市,张掖市,平凉市,酒泉市,庆阳市,定西市,陇南市,临夏回族自治州,甘南藏族自治州,呼和浩特市,包头市,乌海市,赤峰市,通辽市,鄂尔多斯市,呼伦贝尔市,巴彦淖尔市,乌兰察布市,兴安盟,锡林郭勒盟,阿拉善盟,银川市,石嘴山市,吴忠市,固原市,中卫市', 1, 11, 1, 8, 6);
INSERT INTO `f_shop_freight_detail` VALUES (6, '650100,650200,652100,652200,652300,652700,652800,652900,653000,653100,653200,654000,654200,654300,429000,540100,542100,542200,542300,542400,542500,542600', '乌鲁木齐市,克拉玛依市,吐鲁番地区,哈密地区,昌吉回族自治州,博尔塔拉蒙古自治州,巴音郭楞蒙古自治州,阿克苏地区,克孜勒苏柯尔克孜自治州,喀什地区,和田地区,伊犁哈萨克自治州,塔城地区,阿勒泰地区,省直辖行政单位,拉萨市,昌都地区,山南地区,日喀则地区,那曲地区,阿里地区,林芝地区', 1, 15, 1, 13, 6);
INSERT INTO `f_shop_freight_detail` VALUES (7, '810000,820000,710000', '香港特别行政区,澳门特别行政区,台湾省', 1, 80, 1, 40, 6);

-- ----------------------------
-- Table structure for f_shop_order_contact
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_order_contact`;
CREATE TABLE `f_shop_order_contact`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '联系人姓名',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `country` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '国家',
  `province` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '省份',
  `area` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '区域',
  `detail` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '详细地址',
  `mobile` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '联系电话',
  `notes` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
  `wx_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '微信号',
  `order_code` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单编号',
  `city` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '城市',
  `id_card` char(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '身份证号',
  `postal_code` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '邮编',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单联系人 - 非商城废弃' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_order_express
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_order_express`;
CREATE TABLE `f_shop_order_express`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dt_ep_code` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'dtree,发货快递公司编码',
  `dt_ep_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '发货快递公司名称',
  `ep_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '发货快递单号',
  `note` varchar(126) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
  `order_code` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '冗余用户ID，方便查询用户的快递信息。',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单发货信息' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_order_item
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_order_item`;
CREATE TABLE `f_shop_order_item`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品名称',
  `img` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '图片地址',
  `price` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品价格(分）',
  `ori_price` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品原价',
  `sku_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品SKUID',
  `psku_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品规格主键',
  `sku_desc` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'sku描述',
  `count` int(11) NOT NULL COMMENT '数量',
  `order_code` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `create_time` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `dt_goods_unit` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '001' COMMENT '计量单位',
  `dt_origin_country` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '142' COMMENT '产销国',
  `weight` decimal(14, 2) NOT NULL DEFAULT 0.00 COMMENT '商品毛重',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '订单包含的单个商品信息' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_order_refund
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_order_refund`;
CREATE TABLE `f_shop_order_refund`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL,
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '退货申请时间',
  `reason` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '退货理由',
  `valid_status` tinyint(2) NOT NULL COMMENT '验证状态(0: 待审核 1: 同意售后 2:驳回售后 )',
  `replay_uid` int(10) UNSIGNED NOT NULL,
  `reply_msg` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '客服回复信息,每次覆盖',
  `order_code` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单编号',
  `replay_time` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '售后申请表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_order_refund_his
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_order_refund_his`;
CREATE TABLE `f_shop_order_refund_his`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单号',
  `reason` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '退款理由',
  `create_time` int(11) NOT NULL COMMENT '退款时间',
  `result` tinyint(2) NOT NULL COMMENT '退款结果（1:成功,0:失败）',
  `result_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '退款结果描述',
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '退款内容、参数',
  `money` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退款金额,分',
  `dt_currency` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '币制',
  `refund_channel` tinyint(4) NOT NULL COMMENT '退款渠道,与支付类型一致',
  `batch_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '退款批次号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '退款记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_product
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_product`;
CREATE TABLE `f_shop_product`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品名称',
  `product_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品编号',
  `slogan` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '二级标题',
  `template_id` int(11) NOT NULL COMMENT '运费模板ID(0: 免邮 其它模板ID)',
  `loc_country` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '所属国家',
  `loc_province` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '所属省份',
  `loc_city` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '所属城市',
  `loc_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '所属区域',
  `cate_id` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品分类ID',
  `create_time` int(11) UNSIGNED NOT NULL,
  `update_time` int(11) UNSIGNED NOT NULL,
  `onshelf` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否上架(0:否,1:是)',
  `status` tinyint(2) NOT NULL COMMENT '商品状态',
  `store_id` int(11) UNSIGNED NOT NULL COMMENT '店铺ID',
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '详情',
  `weight` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '单件商品计重（克）',
  `synopsis` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品简介',
  `dt_origin_country` int(11) NOT NULL DEFAULT 142 COMMENT ' 产销国(datatree中国别)',
  `dt_goods_unit` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '001' COMMENT '成交计量单位',
  `lang` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'zh-cn' COMMENT '国家区分',
  `place_origin` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '产地',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品基础表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_product_extra
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_product_extra`;
CREATE TABLE `f_shop_product_extra`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `has_receipt` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否有发票',
  `under_guaranty` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否支持保修',
  `support_replace` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否支持退换货',
  `total_sales` int(11) NOT NULL DEFAULT 0 COMMENT '总销量',
  `buy_limit` int(11) NOT NULL DEFAULT 0 COMMENT '限购件数',
  `view_cnt` int(11) NOT NULL DEFAULT 0,
  `pid` int(11) NOT NULL,
  `consignment_time` tinyint(4) NOT NULL DEFAULT 0 COMMENT '发货时间（单位：天）',
  `contact_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品联系人姓名',
  `contact_way` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '联系方式',
  `expire_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品下架时间（时间戳）',
  `favorite_cnt` int(11) NOT NULL DEFAULT 0 COMMENT '收藏数量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品额外属性、服务' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_product_faq
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_product_faq`;
CREATE TABLE `f_shop_product_faq`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '商品id',
  `ask_content` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '咨询内容',
  `reply_content` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '回复内容',
  `ask_uid` int(11) UNSIGNED NOT NULL COMMENT '咨询人ID',
  `reply_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复人uid',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '咨询时间',
  `reply_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户商品咨询表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_product_group
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_product_group`;
CREATE TABLE `f_shop_product_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dt_group` int(11) UNSIGNED NOT NULL COMMENT 'dtree,分组id',
  `pid` int(11) UNSIGNED NOT NULL,
  `sku_id` int(11) UNSIGNED DEFAULT 0 COMMENT '规格id',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '到期时间',
  `price` int(11) UNSIGNED DEFAULT 0 COMMENT '限时价,分',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品分组 (热门,首页,..)' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_product_img
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_product_img`;
CREATE TABLE `f_shop_product_img`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '商品ID',
  `img_id` int(11) NOT NULL COMMENT '图片ID',
  `type` smallint(4) NOT NULL COMMENT '所用场所(1,商品轮播图片 2: 商品APP海报背景 3: 商品APP海报 ) )',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品的图片(主图/列表图,轮播,..)' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_product_prop
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_product_prop`;
CREATE TABLE `f_shop_product_prop`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL,
  `prop_id` int(11) UNSIGNED NOT NULL,
  `value_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品属性表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_product_sku
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_product_sku`;
CREATE TABLE `f_shop_product_sku`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sku_id` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'sku信息, 参照上述sku_table的定义; \n格式 : \"id1:vid1;id2:vid2\"\n规则 : id_info的组合个数必须与sku_table个数一致(若商品无sku信息, 即商品为统一规格，则此处赋值为空字符串即可)',
  `sku_desc` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'sku描述',
  `ori_price` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'sku原价(单位 : 分)起批价1',
  `price` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '价格',
  `quantity` int(11) UNSIGNED NOT NULL COMMENT 'SKU库存 - 废弃',
  `product_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商家商品编码',
  `create_time` int(11) UNSIGNED NOT NULL,
  `pid` int(11) UNSIGNED NOT NULL,
  `icon_url` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格图片id',
  `min_cnt` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最小起订量 - 废弃',
  `price2` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区间2价格(单位:分) - 废弃',
  `cnt2` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区间2最小起批量 - 废弃',
  `update_time` int(11) UNSIGNED NOT NULL,
  `group_price` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '推荐1组价格',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_quantity_his
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_quantity_his`;
CREATE TABLE `f_shop_quantity_his`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `change` int(11) NOT NULL COMMENT '变更（可正可负） 不能为0',
  `pid` int(11) NOT NULL COMMENT '商品ID',
  `create_time` int(11) NOT NULL COMMENT '数据创建时间',
  `change_time` int(11) NOT NULL COMMENT '库存变动时间（让用户选择）',
  `operator_uid` int(11) NOT NULL COMMENT '操作人UID',
  `operator_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '操作人帐号名',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注（填写变动的原因，需要手动调整、缺货等让用户填）',
  `sku_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `dtree_type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_red_envelope
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_red_envelope`;
CREATE TABLE `f_shop_red_envelope`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '拥有者的ID',
  `create_time` int(11) NOT NULL COMMENT '获得时间',
  `tpl_id` int(11) NOT NULL DEFAULT 0 COMMENT '红包模版ID',
  `use_status` tinyint(2) NOT NULL COMMENT '使用状态（0: 未使用，1:已使用）',
  `notes` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `money` decimal(16, 2) NOT NULL,
  `expire_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '红包表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_red_envelope_tpl
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_red_envelope_tpl`;
CREATE TABLE `f_shop_red_envelope_tpl`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dt_type` int(11) NOT NULL COMMENT '红包类别',
  `status` tinyint(4) NOT NULL COMMENT '是否开启',
  `condition` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '使用条件',
  `expire_time` int(11) NOT NULL COMMENT '过期时间',
  `money` decimal(16, 2) NOT NULL COMMENT '可抵扣金额',
  `create_time` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `notes` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '红包模版' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_service
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_service`;
CREATE TABLE `f_shop_service`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `p_id` int(11) NOT NULL,
  `dt_service_id` int(11) NOT NULL COMMENT '商品服务id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_shopping_cart
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_shopping_cart`;
CREATE TABLE `f_shop_shopping_cart`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL,
  `create_time` int(11) UNSIGNED NOT NULL,
  `update_time` int(11) UNSIGNED NOT NULL COMMENT '更新时间',
  `store_id` int(11) NOT NULL COMMENT '店铺ID',
  `pid` int(11) NOT NULL COMMENT '商品ID',
  `sku_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品SKU_ID',
  `sku_desc` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'sku 文字描述',
  `icon_url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品图片地址',
  `count` int(11) NOT NULL COMMENT '商品数量',
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商品名称',
  `express` int(10) NOT NULL DEFAULT 0 COMMENT '商品总运费',
  `template_id` int(11) NOT NULL DEFAULT 0 COMMENT '运费模板ID(应该用不到)',
  `price` int(10) NOT NULL COMMENT '商品购买时的价格',
  `ori_price` int(10) NOT NULL COMMENT '商品原价',
  `psku_id` int(11) NOT NULL DEFAULT 0 COMMENT '商品规格ID,0表示没有规格',
  `weight` int(11) NOT NULL DEFAULT 0 COMMENT '重量',
  `tax_rate` float NOT NULL DEFAULT 0.1 COMMENT '税率',
  `group_id` int(11) NOT NULL DEFAULT 0 COMMENT '分组ID',
  `package_id` int(11) NOT NULL DEFAULT 0 COMMENT '套餐id',
  `item_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1：正常 2: 已过期 3：已下架 4:无库存',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '购物车表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_sku
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_sku`;
CREATE TABLE `f_shop_sku`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'SKU???',
  `cate_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_sku_value
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_sku_value`;
CREATE TABLE `f_shop_sku_value`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sku_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_store
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_store`;
CREATE TABLE `f_shop_store`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_phone` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `weixin_number` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `wx_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺微信号',
  `uid` int(11) NOT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '店铺名称',
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '店铺描述',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `cate_id` int(11) UNSIGNED DEFAULT 0 COMMENT '店铺分类ID',
  `is_open` tinyint(2) DEFAULT 0 COMMENT '店铺是否开张',
  `exp` int(11) DEFAULT 0,
  `banner` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '店铺Banner',
  `logo` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '店铺logo',
  `notes` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `lng` decimal(8, 5) NOT NULL DEFAULT 0.00000 COMMENT '经度',
  `lat` decimal(8, 5) NOT NULL DEFAULT 0.00000 COMMENT '纬度',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_store_discount
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_store_discount`;
CREATE TABLE `f_shop_store_discount`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` int(11) UNSIGNED NOT NULL,
  `condition` int(11) UNSIGNED NOT NULL COMMENT ' 满多少',
  `discount_money` int(11) UNSIGNED NOT NULL COMMENT '减多少',
  `free_shipping` tinyint(1) UNSIGNED NOT NULL COMMENT '是否免邮',
  `start_time` int(11) UNSIGNED NOT NULL COMMENT '开始时间',
  `end_time` int(11) UNSIGNED NOT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_shop_wxpay_notify
-- ----------------------------
DROP TABLE IF EXISTS `f_shop_wxpay_notify`;
CREATE TABLE `f_shop_wxpay_notify`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sign` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '签名',
  `openid` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户在商户appid下的唯一标识',
  `trade_type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '交易类型[app,,]',
  `bank_type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '银行类型，采用字符串类型的银行标识',
  `total_fee` int(11) NOT NULL DEFAULT 0 COMMENT '订单总金额，单位为分',
  `cash_fee` int(11) NOT NULL DEFAULT 0 COMMENT '现金支付金额订单现金支付金额',
  `transaction_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信支付订单号',
  `out_trade_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商户订单号',
  `time_end` varchar(14) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付完成时间,格式为yyyyMMddHHmmss',
  `extra` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '自定义额外信息',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '记录时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信支付回掉记录,微信退款记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_sys_action
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_action`;
CREATE TABLE `f_sys_action`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '行为规则',
  `log` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '日志规则',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '状态',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统行为表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for f_sys_auth
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_auth`;
CREATE TABLE `f_sys_auth`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '系统命名',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '显示名字',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `icon` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标库class',
  `model_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模块id',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `module_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模块id',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '模型操作注册表=节点表=权限注册表 , 扩展:分模型分客户端' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_sys_auth
-- ----------------------------
INSERT INTO `f_sys_auth` VALUES (1, 'user_login', '用户登陆', '控制用户自己是否能登陆', '', 2, 0, 3);
INSERT INTO `f_sys_auth` VALUES (2, 'user_index', '用户查看', '控制用户是否能查看自己外的用户,自己必可以', '', 2, 0, 3);
INSERT INTO `f_sys_auth` VALUES (3, 'user_set', '用户增改', '控制用户是否能添加和修改自己外的用户,自己必可以', '', 2, 0, 3);
INSERT INTO `f_sys_auth` VALUES (4, 'user_del', '用户删除', '控制用户是否能删除某个可删除的用户', '', 2, 0, 3);
INSERT INTO `f_sys_auth` VALUES (5, 'menu_index', '菜单查询', '', '', 1, 0, 1);
INSERT INTO `f_sys_auth` VALUES (6, 'menu_set', '菜单编辑', '', '', 1, 0, 1);
INSERT INTO `f_sys_auth` VALUES (7, 'menu_del', '菜单添加', '', '', 1, 0, 1);
INSERT INTO `f_sys_auth` VALUES (8, 'fyaccount_set', '推广位添加', '', '', 8, 0, 9);
INSERT INTO `f_sys_auth` VALUES (9, 'fyaccount_index', '推广位查看', '', '', 8, 0, 9);
INSERT INTO `f_sys_auth` VALUES (10, 'fyaccount_del', '推广位编辑', '', '', 8, 0, 9);
INSERT INTO `f_sys_auth` VALUES (11, 'role_set', '角色添加', '', '', 3, 0, 1);
INSERT INTO `f_sys_auth` VALUES (12, 'role_index', '角色查询', '', '', 3, 0, 1);
INSERT INTO `f_sys_auth` VALUES (13, 'role_del', '角色编辑', '', '', 3, 0, 1);
INSERT INTO `f_sys_auth` VALUES (14, 'client_index', '客户端查询', '', '', 7, 0, 1);
INSERT INTO `f_sys_auth` VALUES (15, 'client_set', '客户端添加', '', '', 7, 0, 1);
INSERT INTO `f_sys_auth` VALUES (16, 'client_del', '客户端编辑', '', '', 7, 0, 1);
INSERT INTO `f_sys_auth` VALUES (17, 'datatree_index', '字典查看', '', '', 6, 0, 1);
INSERT INTO `f_sys_auth` VALUES (18, 'datatree_set', '字典编辑', '', '', 6, 0, 1);
INSERT INTO `f_sys_auth` VALUES (19, 'datatree_del', '字典添加', '', '', 6, 0, 1);
INSERT INTO `f_sys_auth` VALUES (20, 'auth_index', '节点查看', '', '', 9, 0, 1);
INSERT INTO `f_sys_auth` VALUES (21, 'auth_set', '节点编辑', '', '', 9, 0, 1);
INSERT INTO `f_sys_auth` VALUES (22, 'auth_del', '节点添加', '', '', 9, 0, 1);
INSERT INTO `f_sys_auth` VALUES (23, 'role_auth', '角色权限管理', '', '', 3, 0, 1);
INSERT INTO `f_sys_auth` VALUES (24, 'wallethis_index', '余额变更[他人]', '', '', 10, 0, 3);
INSERT INTO `f_sys_auth` VALUES (25, 'fyinvite_index', '拉新查看[他人]', '', '', 11, 0, 3);
INSERT INTO `f_sys_auth` VALUES (26, 'fyaccount_allot', '推广位分配', '', '', 8, 0, 9);

-- ----------------------------
-- Table structure for f_sys_config
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_config`;
CREATE TABLE `f_sys_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置系统名,模板调用{:config(name)}',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置显示',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '配置类型,0字符(过滤特殊)1数字2文本(不过滤)3数组4枚举5图片',
  `group` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '查询某些才有效,0=>不分组默认,1=>api',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置值',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置说明',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_sys_config
-- ----------------------------
INSERT INTO `f_sys_config` VALUES (1, 'site_title', '网站标题', 0, 1, 'rainbowPHP', '网站标题', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (2, 'site_slogan', '网站标语', 0, 1, ' rainbowPHP', '网站标题', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (3, 'site_desc', '网站描述', 0, 1, ' rainbowPHP', '网站标题', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (4, 'site_keyword', '网站关键词', 0, 1, ' rainbowPHP', '网站标题', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (5, 'site_icp', 'ICP', 0, 1, ' rainbowPHP', '网站标题', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (6, 'site_copyright', 'copyright', 0, 1, '', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (7, 'site_analysis', '分析html结构', 0, 1, '', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (8, 'site_logo', 'logo', 0, 1, '/sys/img/logo.png', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (9, 'config_type_list', '配置类型', 3, 1, '0:字符(过滤),1:数字,2:文本(不滤),3:数组,4:图片', '配置类型定义 , 不允许修改', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (10, 'config_group_list', '配置分组', 3, 1, '0:默认,1:系统,2:邮件,3:API,4:分佣', '配置分组定义 , 不允许修改', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (11, 'admin_page_size', '分页大小', 1, 1, '10', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (12, 'link_phone', '客服电话', 0, 1, 'x-x', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (13, 'admin_allow_ips', '运行后台的IP列表', 0, 1, '', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (14, 'session_expire', 'session超时时间', 1, 1, '1296000', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (15, 'ios_ver', 'ios版本', 0, 3, '1.0.0', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (16, 'android_ver', 'android版本', 0, 3, '1.0.0', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (17, 'link_qq', '客服qq', 0, 1, 'xx', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (18, 'fliter_words', '过滤词池', 0, 0, 'admin', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (19, 'smtp_host', '邮件服务', 0, 2, ' ', '邮件服务器地址', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (20, 'smtp_port', '邮件服务', 1, 2, '', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (21, 'smtp_from_name', '邮件服务', 0, 2, '', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (22, 'smtp_pass', '邮件服务', 0, 2, '', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (23, 'smtp_user', '邮件服务', 0, 2, '', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (24, 'device_df_limit', '设备默认最大绑定数', 0, 0, '0', '设备默认最大绑定数,最终保存到用户', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (25, 'device_unbind_cycle', '设备解绑周期(天)', 0, 0, '1', '配合设备周期解绑次数使用', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (26, 'device_cycle_limit', '设备解绑周期限制', 0, 0, '2', '设备周期内允许的最大解绑次数,配合设备周期使用', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (27, 'table_df_pre', '表前缀', 0, 1, 'f_', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (28, 'admin_theme', '后台主题', 0, 1, 'df', 'app_debug下无效,主题 : blue,df,cyan,gray,green,orange,red', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (29, 'admin_ban_ips', '运行后台的IP列表', 0, 1, '', '', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (31, 'fy_money', '佣金设置', 3, 4, 'login:2000,real:1000,buy:3000', '佣金设置,分', 0, 0, 0);
INSERT INTO `f_sys_config` VALUES (32, 'admin_login_auth_type', '后台登陆验证', 0, 1, 'auth_slide', 'auth_slide,,auth_code', 0, 0, 0);

-- ----------------------------
-- Table structure for f_sys_datatree
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_datatree`;
CREATE TABLE `f_sys_datatree`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '系统名 , dt(name)',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名字',
  `sort` int(10) UNSIGNED NOT NULL,
  `parent` int(10) UNSIGNED NOT NULL,
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_sys` tinyint(2) UNSIGNED NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '数据字典' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_sys_datatree
-- ----------------------------
INSERT INTO `f_sys_datatree` VALUES (1, 'pro_group', '商品分组', 0, 0, '', 0, 'fa fa-shopping-bag');
INSERT INTO `f_sys_datatree` VALUES (2, 'pro_group_item', '热门商品', 0, 1, '', 0, '123');
INSERT INTO `f_sys_datatree` VALUES (3, 'pro_group_item', '首页商品', 0, 1, '', 0, 'fa fa-home');
INSERT INTO `f_sys_datatree` VALUES (4, 'msg_type', '消息类型', 0, 0, '', 0, 'fa fa-bell');
INSERT INTO `f_sys_datatree` VALUES (5, 'url_type', 'url跳转类型', 0, 0, '', 1, '');
INSERT INTO `f_sys_datatree` VALUES (10, 'url_type_item', '超链接', 0, 5, '超链接', 1, '');
INSERT INTO `f_sys_datatree` VALUES (11, 'url_type_item', '商品id', 0, 5, '商品id', 0, '');
INSERT INTO `f_sys_datatree` VALUES (12, 'url_type_item', '帖子id', 0, 5, '帖子id', 0, '');
INSERT INTO `f_sys_datatree` VALUES (13, 'banner', 'banner汇总', 1, 0, 'banner 列表汇总', 1, '');
INSERT INTO `f_sys_datatree` VALUES (14, 'banner_item', 'APP首页_600x400', 0, 13, '', 0, '');
INSERT INTO `f_sys_datatree` VALUES (15, 'banner_item', 'PC首页顶部_1600x400', 0, 13, '', 0, '');
INSERT INTO `f_sys_datatree` VALUES (16, 'wallet_change', '余额变动类型', 0, 0, '余额变动类型', 1, 'fa fa-lock');
INSERT INTO `f_sys_datatree` VALUES (17, 'wallet_change_item', '拉新佣金获得', 0, 16, '拉新佣金获得', 0, '');
INSERT INTO `f_sys_datatree` VALUES (18, 'wallet_change_item', '佣金失去', 0, 16, '佣金失去', 0, '');

-- ----------------------------
-- Table structure for f_sys_hooks
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_hooks`;
CREATE TABLE `f_sys_hooks`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '钩子名称',
  `desc` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '描述',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `addons` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '钩子挂载的插件 \'，\'分割',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for f_sys_menu
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_menu`;
CREATE TABLE `f_sys_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `parent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'admin/user/edit or http',
  `show` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=>显示,0=>隐藏, 对超管无效',
  `icon` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'id/ icon class',
  `module_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模块id',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `params` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '参数',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `level` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 55 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '菜单' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_sys_menu
-- ----------------------------
INSERT INTO `f_sys_menu` VALUES (1, '系统', 0, '#', 1, '', 0, 0, 1, '', '', 1512357255, 1535333148, 1);
INSERT INTO `f_sys_menu` VALUES (2, '系统管理', 1, '#', 1, 'fa fa-gears', 0, 0, 1, '', '', 1512357255, 1535333133, 2);
INSERT INTO `f_sys_menu` VALUES (3, '菜单管理', 2, 'menu/index', 1, '', 0, 1, 1, '', '', 1512357255, 1534759192, 3);
INSERT INTO `f_sys_menu` VALUES (4, '节点管理', 34, 'auth/index', 1, '', 0, 4, 1, '', '', 1512357255, 1534759220, 3);
INSERT INTO `f_sys_menu` VALUES (5, '配置管理', 2, 'config/index', 1, '', 0, 2, 1, '', '', 1512357255, 1534759192, 3);
INSERT INTO `f_sys_menu` VALUES (6, '数据字典', 2, 'datatree/index', 1, '', 0, 3, 1, '', '数据字典', 1512357255, 1534759194, 3);
INSERT INTO `f_sys_menu` VALUES (15, '商城', 0, '#', 0, '', 0, 4, 1, '', '', 1512441238, 1535333164, 1);
INSERT INTO `f_sys_menu` VALUES (16, '用户', 0, '#', 1, '', 0, 2, 1, '', '', 1512441247, 1535333165, 1);
INSERT INTO `f_sys_menu` VALUES (17, '其他', 0, '#', 1, '', 0, 99, 1, '23', 'dd', 1512441254, 1535333150, 1);
INSERT INTO `f_sys_menu` VALUES (18, '图片素材', 1, '#', 0, 'layui-icon layui-icon-picture-fine', 0, 2, 1, '', '图片音频等', 1512441342, 1542769421, 2);
INSERT INTO `f_sys_menu` VALUES (19, 't用户图片', 18, 'picture/user', 0, '', 0, 0, 1, '', '', 1512441494, 1542784215, 3);
INSERT INTO `f_sys_menu` VALUES (20, 't编辑器图片', 18, 'picture/editor', 0, '', 0, 1, 1, '', '', 1512441505, 1542784216, 3);
INSERT INTO `f_sys_menu` VALUES (21, 't默认头像', 18, 'picture/avatar', 0, '', 0, 4, 1, '', '', 1512441528, 1542784220, 3);
INSERT INTO `f_sys_menu` VALUES (22, 't表情管理', 18, 'picture/face', 0, '', 0, 3, 1, '', '', 1512441540, 1542784219, 3);
INSERT INTO `f_sys_menu` VALUES (23, 't图标配置', 18, 'picture/icon', 0, '', 0, 2, 1, '', '', 1512441568, 1542784218, 3);
INSERT INTO `f_sys_menu` VALUES (24, 't数据库管理', 2, 'database/index', 0, '', 0, 99, 1, '', '', 1512454688, 1542784197, 3);
INSERT INTO `f_sys_menu` VALUES (25, '上传与编辑器t', 2, 'picture/test', 0, '', 0, 98, 1, '', '', 1512455200, 1542784201, 3);
INSERT INTO `f_sys_menu` VALUES (29, '用户管理', 16, '', 1, 'layui-icon layui-icon-user', 0, 0, 1, '', '', 1512529414, 1542768745, 2);
INSERT INTO `f_sys_menu` VALUES (30, '用户管理', 29, 'user/index', 1, '', 0, 0, 1, '', '', 1512529441, 1526461091, 3);
INSERT INTO `f_sys_menu` VALUES (31, '角色管理', 34, 'role/index', 1, '', 0, 6, 1, '', '', 1512608609, 1534759222, 3);
INSERT INTO `f_sys_menu` VALUES (32, '客户端管理', 34, 'client/index', 0, '', 0, 5, 1, '', '', 1512608627, 1513327420, 3);
INSERT INTO `f_sys_menu` VALUES (34, '角色权限', 1, '', 1, 'layui-icon layui-icon-auz', 0, 1, 1, '', '', 1513327385, 1542768886, 2);
INSERT INTO `f_sys_menu` VALUES (35, '商品管理', 15, '', 1, 'fa fa-shopping-bag', 0, 0, 1, '', '', 1513740537, 1526461040, 2);
INSERT INTO `f_sys_menu` VALUES (36, '店铺管理', 35, 'store/index', 1, '', 0, 0, 1, '', '', 1513740569, 1526461042, 3);
INSERT INTO `f_sys_menu` VALUES (37, '商品管理', 35, 'product/index', 1, '', 0, 0, 1, '', '', 1513740591, 1526461044, 3);
INSERT INTO `f_sys_menu` VALUES (38, '类目管理', 35, 'productCate/index', 1, '', 0, 0, 1, '', '商品类目 , 属性 , 规格 管理', 1513740613, 1526461046, 3);
INSERT INTO `f_sys_menu` VALUES (39, 'CMS', 0, '', 0, '', 0, 3, 1, '', 'dsdsd', 1513740676, 1535333087, 1);
INSERT INTO `f_sys_menu` VALUES (40, 'CMS管理', 39, '', 1, 'fa fa-book', 0, 0, 1, '', '', 1513740731, 1526461054, 2);
INSERT INTO `f_sys_menu` VALUES (41, '类目管理', 40, 'cmsCate/index', 1, '', 0, 0, 1, '', '', 1513740902, 1526461055, 3);
INSERT INTO `f_sys_menu` VALUES (42, '文章管理', 40, 'cmsPost/index', 1, '', 0, 0, 1, '', '', 1513740924, 1526461056, 3);
INSERT INTO `f_sys_menu` VALUES (43, '未分类', 17, '', 1, 'fa fa-terminal', 0, 0, 1, '', '', 1513741022, 1526461029, 2);
INSERT INTO `f_sys_menu` VALUES (44, '轮播推广', 18, 'banner/index', 0, '', 0, 0, 1, '', '轮播,广告', 1513741201, 1513741201, 3);
INSERT INTO `f_sys_menu` VALUES (46, '省市区管理', 2, 'area/index', 0, '', 0, 4, 1, '', '', 1519804276, 1519804289, 3);
INSERT INTO `f_sys_menu` VALUES (47, 'BBS', 0, '', 0, '', 0, 5, 1, '', '', 1526029145, 1535333160, 1);
INSERT INTO `f_sys_menu` VALUES (48, '分销管理', 16, '', 1, 'layui-icon layui-icon-tree', 0, 1, 1, '', '', 1534759369, 1542769468, 2);
INSERT INTO `f_sys_menu` VALUES (49, '推广位管理', 48, 'fyAccount/index', 1, '', 0, 0, 1, '', '', 1534759617, 1535420246, 3);
INSERT INTO `f_sys_menu` VALUES (50, '我的分销商', 48, 'user/mine', 1, '', 0, 0, 1, '', '下级用户管理', 1535333486, 1535364428, 1);
INSERT INTO `f_sys_menu` VALUES (51, '我的推广位', 48, 'fyAccount/mine', 1, '', 0, 0, 1, '', '', 1535364368, 1535420243, 3);
INSERT INTO `f_sys_menu` VALUES (52, '消息管理', 16, '', 1, 'layui-icon layui-icon-notice', 0, 0, 1, '', '', 1542706262, 1542707364, 2);
INSERT INTO `f_sys_menu` VALUES (53, 't系统消息', 52, 'message/index', 1, 'fa fa-bell', 0, 0, 1, '', '', 1542707080, 1542784245, 3);
INSERT INTO `f_sys_menu` VALUES (54, 't系统日志', 2, 'log/index', 1, 'layui-icon layui-icon-form', 0, 0, 1, '', '', 1542707334, 1542784191, 3);

-- ----------------------------
-- Table structure for f_sys_model
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_model`;
CREATE TABLE `f_sys_model`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模块系统名',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '显示标题',
  `desc` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模型描述',
  `icon` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL COMMENT '模块id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_sys` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否前端不公开',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '模型注册表 独立于模块 安装模块_模块名.表名' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_sys_model
-- ----------------------------
INSERT INTO `f_sys_model` VALUES (1, 'menu', '菜单', '菜单', 'fa fa-', 2, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (2, 'user', '用户', '用户', 'fa fa-', 3, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (3, 'role', '角色', '角色', 'fa fa-', 1, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (4, 'img', '图片', '用户上传的图片', 'fa fa-', 0, 0, 0, 0);
INSERT INTO `f_sys_model` VALUES (5, 'config', '配置', '系统必须的配置', 'fa fa-', 1, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (6, 'datatree', '字典', '数据字典,模块引用,大了拆出来做表', 'fa fa-', 1, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (7, 'client', '客户端', ' ', 'fa fa-', 1, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (8, 'account', '推广位', ' ', 'fa fa-', 9, 0, 0, 0);
INSERT INTO `f_sys_model` VALUES (9, 'auth', '权限', ' ', 'fa fa-', 1, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (10, 'area', '省市区', ' ', 'fa fa-', 1, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (11, 'message', '消息', ' ', 'fa fa-', 1, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (12, 'frame', '后台框架', ' ', 'fa fa-', 2, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (13, 'login', '后台登陆', ' ', 'fa fa-', 2, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (14, 'banner', '轮播图', ' ', 'fa fa-', 2, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (15, 'cate', '新闻分类', ' ', 'fa fa-', 6, 0, 0, 0);
INSERT INTO `f_sys_model` VALUES (16, 'post', '新闻', ' ', 'fa fa-', 6, 0, 0, 0);
INSERT INTO `f_sys_model` VALUES (17, 'upload', '后台上传', ' ', 'fa fa-', 2, 0, 0, 1);
INSERT INTO `f_sys_model` VALUES (18, 'user', '分销商', ' ', 'fa fa-', 9, 0, 0, 0);
INSERT INTO `f_sys_model` VALUES (19, 'invite', '拉新记录', ' ', 'fa fa-', 9, 0, 0, 0);
INSERT INTO `f_sys_model` VALUES (20, 'img', '后台图片', ' ', 'fa fa-', 2, 0, 0, 0);
INSERT INTO `f_sys_model` VALUES (21, 'test', '后台测试', ' ', 'fa fa-', 2, 0, 0, 0);

-- ----------------------------
-- Table structure for f_sys_model_field
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_model_field`;
CREATE TABLE `f_sys_model_field`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '模型字段 , 考虑外部文件' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_sys_module
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_module`;
CREATE TABLE `f_sys_module`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模块名(标志)',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `desc` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `icon` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'ENUM:图标库class( ? 图片id/系统path )',
  `author` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '作者',
  `author_url` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '联系方式/网站',
  `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置信息',
  `access` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '权限信息',
  `version` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1.0.0' COMMENT '版本号',
  `flag` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '唯一标志 , user.rainbowphp.module',
  `is_sys` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为系统.前端不公开',
  `sort` int(10) UNSIGNED DEFAULT 0 COMMENT '排序,越大越靠前',
  `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT 'ENUM : -1假删除,0=>默认,1=>正常,2=>禁用,3=>待审',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `flag`(`flag`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '模块注册表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_sys_module
-- ----------------------------
INSERT INTO `f_sys_module` VALUES (1, 'sys', '系统', '系统模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'sys.rainbow.module', 1, 0, 1, 0, 0);
INSERT INTO `f_sys_module` VALUES (2, 'admin', '后台', '后台模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'admin.rainbow.module', 1, 0, 1, 0, 0);
INSERT INTO `f_sys_module` VALUES (3, 'user', '用户', '用户模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'user.rainbow.module', 0, 0, 1, 0, 0);
INSERT INTO `f_sys_module` VALUES (4, 'shop', '商城', '商城模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'shop.rainbow.module', 0, 0, 1, 0, 0);
INSERT INTO `f_sys_module` VALUES (5, 'bbs', '论坛', '论坛模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'bbs.rainbow.module', 0, 0, 1, 0, 0);
INSERT INTO `f_sys_module` VALUES (6, 'cms', '门户', '门户模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'cms.rainbow.module', 0, 0, 1, 0, 0);
INSERT INTO `f_sys_module` VALUES (7, 'api', '接口', '接口模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'api.rainbow.module', 0, 0, 1, 0, 0);
INSERT INTO `f_sys_module` VALUES (8, 'weixin', '微信', '微信模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'weixin.rainbow.module', 0, 0, 1, 0, 0);
INSERT INTO `f_sys_module` VALUES (9, 'fy', '分佣', '分佣模块', 'fa fa-tw fa-gears', 'rainbow', '51cc.win', ' ', ' ', '1.0.0', 'fy.rainbow.module', 0, 0, 1, 0, 0);

-- ----------------------------
-- Table structure for f_sys_role
-- ----------------------------
DROP TABLE IF EXISTS `f_sys_role`;
CREATE TABLE `f_sys_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(2) UNSIGNED NOT NULL,
  `menu_auth` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单显示权限,全查,由客户端处理状态等',
  `api_auth` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作权限,全查,由客户端处理状态等',
  `client_auth` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户端权限,全查,由客户端处理状态等',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '角色注册表,不包括创始人(uid=1),创始人自带全部权限,加角色不影响' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_sys_role
-- ----------------------------
INSERT INTO `f_sys_role` VALUES (1, '管理员', '', 1, '1,2,3,5,6,46,25,24,34,4,32,31,18,19,44,20,23,22,21', '1,2', '');
INSERT INTO `f_sys_role` VALUES (2, '默认用户', '普通用户,注册后添加的默认角色', 1, '', '{\"1\":[\"1\"],\"2\":[\"2\"],\"3\":[\"1\",\"2\"],\"4\":[\"1\",\"2\"]}', '');

-- ----------------------------
-- Table structure for f_user
-- ----------------------------
DROP TABLE IF EXISTS `f_user`;
CREATE TABLE `f_user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '系统用户名,df:u时间戳?',
  `nick` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '昵称,显示,df:n时间戳 ?',
  `pass` varchar(96) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码,salt加密',
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '邮箱',
  `email_auth` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否认证',
  `phone` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机号',
  `phone_auth` tinyint(1) UNSIGNED NOT NULL COMMENT '是否认证',
  `idcard` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '省份证号码',
  `idcard_auth` tinyint(1) UNSIGNED NOT NULL COMMENT '是否认证',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '头像, picId / 微信http',
  `reg_ip` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ip地址,ip2long转换',
  `reg_from` int(10) UNSIGNED NOT NULL COMMENT '第三方注册方式ENUM:0=>默认,1=>self,2=>qq,3=>weixin,..',
  `reg_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册的时间',
  `status` tinyint(2) NOT NULL DEFAULT 0,
  `sex` tinyint(2) NOT NULL DEFAULT 0 COMMENT 'ENUM:0=>默认,1=>男,2=>女',
  `qq` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'qq号',
  `invite_uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '邀请人uid',
  `theme` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '主题',
  `rate` float(4, 3) NOT NULL DEFAULT 0.000 COMMENT '分佣比例',
  `level` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '代理商层级,1+',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户主表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_user
-- ----------------------------
INSERT INTO `f_user` VALUES (1, 'rainbow', 'rainbowPhp2323', '8b26f368a4971b567df2c003e6b1817a', '977746075@qq.com', 1, '17681876087', 1, ' ', 0, '6', 0, 0, 0, 1, 0, '', 0, 'cyan', 0.000, 0);
INSERT INTO `f_user` VALUES (2, 'test2', 'test', '8b26f368a4971b567df2c003e6b1817a', '', 0, '', 0, '', 0, '8', 0, 0, 0, 1, 0, '', 0, '', 0.000, 0);
INSERT INTO `f_user` VALUES (8, 'test3', 'test3', '8b26f368a4971b567df2c003e6b1817a', '', 0, '', 0, '', 0, '', 0, 0, 0, 1, 0, '', 0, '', 0.000, 0);

-- ----------------------------
-- Table structure for f_user_extra
-- ----------------------------
DROP TABLE IF EXISTS `f_user_extra`;
CREATE TABLE `f_user_extra`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'uid',
  `money` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '余额,分,看情况分表',
  `score` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分,看情况分表',
  `exp` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '经验,看情况分表',
  `last_login_ip` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上次登陆ip ,long',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上次登陆时间',
  `login_cnt` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '总登陆次数',
  `lng` decimal(8, 5) NOT NULL DEFAULT 0.00000 COMMENT '经度',
  `lat` decimal(8, 5) NOT NULL DEFAULT 0.00000 COMMENT '纬度',
  `geohash` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '经纬度geohash',
  `sign` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '签名',
  `bg_img` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '背景图片id',
  `birthday_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '生日',
  `login_device_limit` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登陆设备限制',
  `df_address` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '默认地址id',
  `pay_secret` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付密码',
  `im_status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0离线,1在线,2隐身',
  `id_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邀请码,uid相关',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id_code`(`id_code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户附表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_user_extra
-- ----------------------------
INSERT INTO `f_user_extra` VALUES (1, 1, 0, 0, 0, 0, 0, 0, 0.00000, 0.00000, '', '', 0, 0, 0, 0, '', 0, '1000001');
INSERT INTO `f_user_extra` VALUES (2, 2, 0, 0, 0, 0, 0, 0, 0.00000, 0.00000, '', '', 0, 0, 0, 0, '', 0, '1000002');
INSERT INTO `f_user_extra` VALUES (5, 8, 0, 0, 0, 0, 0, 0, 0.00000, 0.00000, '', '', 0, 0, 0, 0, '', 0, '1000008');

-- ----------------------------
-- Table structure for f_user_fav
-- ----------------------------
DROP TABLE IF EXISTS `f_user_fav`;
CREATE TABLE `f_user_fav`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `target_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `module_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `model_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `extra` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '收藏夹' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_user_role
-- ----------------------------
DROP TABLE IF EXISTS `f_user_role`;
CREATE TABLE `f_user_role`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `start_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间,暂不管',
  `end_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束时间,暂不管',
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uid`(`uid`, `role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户角色注册表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of f_user_role
-- ----------------------------
INSERT INTO `f_user_role` VALUES (1, 1, 1, 0, 0, 0, 1542709454);
INSERT INTO `f_user_role` VALUES (2, 8, 2, 0, 0, 1521448383, 1521450081);
INSERT INTO `f_user_role` VALUES (5, 2, 2, 0, 0, 1521450154, 1526461298);

-- ----------------------------
-- Table structure for f_user_score_his
-- ----------------------------
DROP TABLE IF EXISTS `f_user_score_his`;
CREATE TABLE `f_user_score_his`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `before_score` int(11) NOT NULL DEFAULT 0 COMMENT '操作前积分，因为涉及到系统全体发放逻辑，改为字符串',
  `plus` int(11) NOT NULL COMMENT '+多少佣金',
  `minus` int(11) NOT NULL COMMENT '减多少佣金',
  `after_score` int(11) NOT NULL DEFAULT 0 COMMENT '操作后积分',
  `create_time` int(11) NOT NULL COMMENT '变动时间',
  `type` int(11) NOT NULL COMMENT '类型,int,见scoreHisLogicV2',
  `reason` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '变动原因',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户积分变动历史' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_user_wallet
-- ----------------------------
DROP TABLE IF EXISTS `f_user_wallet`;
CREATE TABLE `f_user_wallet`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  `wallet_type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0=>正常,1=>账户冻结',
  `frozen_funds` int(11) NOT NULL DEFAULT 0 COMMENT '冻结资金',
  `account_balance` int(11) NOT NULL DEFAULT 0 COMMENT '账户余额',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '上次更改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_user_wallet_apply
-- ----------------------------
DROP TABLE IF EXISTS `f_user_wallet_apply`;
CREATE TABLE `f_user_wallet_apply`  (
  `id` int(11) UNSIGNED NOT NULL,
  `create_time` int(11) UNSIGNED NOT NULL COMMENT '申请时间',
  `reason` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '理由',
  `valid_status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '验证状态(0: 待审核 1: 同意提现 2:驳回提现 )',
  `reply_msg` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '客服回复信息,每次覆盖',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请者 uid',
  `op_uid` int(11) UNSIGNED DEFAULT 0 COMMENT '回复者 uid',
  `money` decimal(6, 2) UNSIGNED DEFAULT 0.00 COMMENT '提现金额',
  `account_id` int(11) NOT NULL DEFAULT 0 COMMENT '提现到的帐号ID'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '提现申请表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_user_wallet_his
-- ----------------------------
DROP TABLE IF EXISTS `f_user_wallet_his`;
CREATE TABLE `f_user_wallet_his`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `before_money` int(11) NOT NULL COMMENT '当前资金',
  `plus` int(11) NOT NULL COMMENT '+多少佣金',
  `minus` int(11) NOT NULL COMMENT '减多少佣金',
  `after_money` int(11) NOT NULL COMMENT '变化后资金',
  `create_time` int(11) NOT NULL COMMENT '变动时间',
  `dt_type` int(11) UNSIGNED NOT NULL COMMENT '余额变动类型',
  `reason` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '变动原因',
  `wallet_type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0: 可用资金 1: 冻结资金',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '余额变动历史纪录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_wx_content
-- ----------------------------
DROP TABLE IF EXISTS `f_wx_content`;
CREATE TABLE `f_wx_content`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tid` tinyint(1) UNSIGNED NOT NULL COMMENT '素材类型',
  `cid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关联内容id',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `thumb` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '图片',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '描述',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '更多阅读地址',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '详细内容',
  `orther` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '其他数据信息',
  `inputtime` int(10) UNSIGNED NOT NULL COMMENT '输入时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `tid`(`tid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '素材内容表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_wx_menu
-- ----------------------------
DROP TABLE IF EXISTS `f_wx_menu`;
CREATE TABLE `f_wx_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL,
  `type` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `key` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `displayorder` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE,
  INDEX `displayorder`(`displayorder`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信菜单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_wx_reply
-- ----------------------------
DROP TABLE IF EXISTS `f_wx_reply`;
CREATE TABLE `f_wx_reply`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复类型',
  `keyword` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '关键字',
  `app` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '应用目录',
  `cid` int(10) NOT NULL DEFAULT 0 COMMENT '素材id',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '文本信息',
  `count` int(10) NOT NULL DEFAULT 0 COMMENT '总计回复次数',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `count`(`count`) USING BTREE,
  INDEX `keyword`(`keyword`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信回复规则表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_wx_session
-- ----------------------------
DROP TABLE IF EXISTS `f_wx_session`;
CREATE TABLE `f_wx_session`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `openid` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '小程序用户ID',
  `session_key` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '会话密钥',
  `by_session_key` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '该系统生成的',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `by_session_key`(`by_session_key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信小程序session' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_wx_user
-- ----------------------------
DROP TABLE IF EXISTS `f_wx_user`;
CREATE TABLE `f_wx_user`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED DEFAULT NULL COMMENT '会员id',
  `openid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '唯一id',
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信昵称',
  `sex` tinyint(1) UNSIGNED DEFAULT NULL COMMENT '性别',
  `city` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '城市',
  `country` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '国家',
  `province` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '省',
  `language` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '语言',
  `headimgurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '头像地址',
  `subscribe_time` int(10) UNSIGNED NOT NULL COMMENT '关注时间',
  `location_x` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '坐标',
  `location_y` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '坐标',
  `location_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '坐标详情',
  `msg_today` int(10) NOT NULL DEFAULT 0 COMMENT '每日消息的发送时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `msg_today`(`msg_today`) USING BTREE,
  INDEX `openid`(`openid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信会员表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_bbs
-- ----------------------------
DROP TABLE IF EXISTS `zf_bbs`;
CREATE TABLE `zf_bbs`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '论坛ID',
  `parent` int(11) NOT NULL COMMENT '上级论坛ID',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '论坛名称',
  `sort` smallint(6) NOT NULL DEFAULT 0 COMMENT '显示顺序',
  `auth` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否审核(1: 是 0: 否)',
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '论坛概述',
  `icon` int(11) NOT NULL COMMENT '论坛小3图标',
  `status` tinyint(1) NOT NULL COMMENT '0正常,1锁定,-1关闭',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '版块表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_bbs_attach
-- ----------------------------
DROP TABLE IF EXISTS `zf_bbs_attach`;
CREATE TABLE `zf_bbs_attach`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '贴子ID',
  `img` int(11) NOT NULL COMMENT '附件ID(暂放图片ID)',
  `rid` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '附件索引表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_bbs_ban
-- ----------------------------
DROP TABLE IF EXISTS `zf_bbs_ban`;
CREATE TABLE `zf_bbs_ban`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL COMMENT '0则永久禁言',
  `rule` int(11) NOT NULL COMMENT '二进制后倒数:发改回帖,发改帖,0有1无',
  `reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '原因',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_bbs_post
-- ----------------------------
DROP TABLE IF EXISTS `zf_bbs_post`;
CREATE TABLE `zf_bbs_post`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '作者ID',
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `create_time` int(11) NOT NULL COMMENT '发表时间',
  `update_time` int(11) NOT NULL COMMENT '发表时间',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '消息',
  `ip` int(11) NOT NULL COMMENT '发帖者IP',
  `status` int(11) NOT NULL COMMENT '帖子状态，0待审核，1正常，-1隐藏',
  `tid` int(11) NOT NULL COMMENT '所属板块',
  `special` int(2) NOT NULL DEFAULT 0 COMMENT '0=默认,1=精华',
  `top` int(2) NOT NULL DEFAULT 0 COMMENT '0=默认,1=置顶',
  `app` varchar(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'admin/ios/android/...',
  `reply_limit` int(11) NOT NULL DEFAULT 0 COMMENT '是否限制别人回复,1=>限制',
  `views` int(11) NOT NULL DEFAULT 0,
  `repeat_id` int(11) NOT NULL DEFAULT 0 COMMENT '转发前id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_bbs_reply
-- ----------------------------
DROP TABLE IF EXISTS `zf_bbs_reply`;
CREATE TABLE `zf_bbs_reply`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '板块ID',
  `pid` int(11) NOT NULL COMMENT '帖子ID',
  `rid` int(11) NOT NULL COMMENT '回复ID',
  `to_uid` int(11) NOT NULL DEFAULT 0 COMMENT '回复用户ID',
  `uid` int(11) NOT NULL COMMENT '作者uid',
  `create_time` int(11) NOT NULL COMMENT '发表时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `content` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容(控制512个字以内)',
  `ip` int(11) NOT NULL COMMENT '发帖者IP',
  `app` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'admin/ios/android/...',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '帖子回复' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_bbs_report
-- ----------------------------
DROP TABLE IF EXISTS `zf_bbs_report`;
CREATE TABLE `zf_bbs_report`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urlkey` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '防止多次举报,',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '举报地址、举报的帖子地址',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '举报理由',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `op_uid` int(11) NOT NULL COMMENT '举报人ID',
  `op_time` int(11) NOT NULL COMMENT '处理时间',
  `op_result` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '处理结果备注',
  `create_time` int(11) DEFAULT NULL COMMENT '举报时间',
  `type` tinyint(4) NOT NULL DEFAULT -1 COMMENT '类型',
  `report_id` int(11) NOT NULL COMMENT '举报指向ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户举报表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_bbs_tag_post
-- ----------------------------
DROP TABLE IF EXISTS `zf_bbs_tag_post`;
CREATE TABLE `zf_bbs_tag_post`  (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tid` int(11) UNSIGNED NOT NULL,
  `fid` int(11) UNSIGNED NOT NULL COMMENT '论坛ID',
  `tag_id` int(11) NOT NULL COMMENT '标签ID'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '论坛帖子标签关系表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_answer
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_answer`;
CREATE TABLE `zf_edu_answer`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qid` int(11) NOT NULL COMMENT '题目id',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '答案',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `add_uid` int(11) NOT NULL COMMENT '添加人',
  `sort` smallint(6) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `is_real` tinyint(2) NOT NULL,
  `real_sort` smallint(6) NOT NULL,
  `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图片,字母,字符,布尔,..',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '答案' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_question
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_question`;
CREATE TABLE `zf_edu_question`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '题目概述 - 已废弃',
  `note` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '标识',
  `question` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '题问',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `added_uid` int(11) NOT NULL COMMENT '添加人',
  `dt_type` int(11) NOT NULL COMMENT '题目类型（数据字典）',
  `audio_id` int(11) NOT NULL COMMENT '音频文件id',
  `origin_article` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '原文题目解析',
  `do_cnt` int(11) NOT NULL DEFAULT 0 COMMENT '做题统计',
  `come_from` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '题目来源',
  `knowledge` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '考察知识点',
  `parent_id` int(11) NOT NULL COMMENT '父级题目id，一个大问题下面有很多小问题',
  `status` int(11) DEFAULT 0 COMMENT '0=>起草,1=>发布,-1删除',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '题面',
  `sort` int(11) DEFAULT 0 COMMENT '排序,小题有用',
  `relax` int(11) NOT NULL DEFAULT 0 COMMENT '休息时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '题目库' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_report
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_report`;
CREATE TABLE `zf_edu_report`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '成绩单所属用户',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `testpaper_id` int(11) NOT NULL COMMENT '试卷id',
  `correct_cnt` int(11) NOT NULL COMMENT '正确题',
  `error_cnt` int(11) NOT NULL COMMENT '错题',
  `missing_cnt` int(11) NOT NULL COMMENT '漏题',
  `bookunit_id` int(11) NOT NULL COMMENT '单元id',
  `use_time` int(255) NOT NULL COMMENT '答题时间,多少秒完成',
  `snap` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '当时问题的快照',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '学生成绩单' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_testpaper
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_testpaper`;
CREATE TABLE `zf_edu_testpaper`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '试卷标题',
  `creator` int(11) NOT NULL COMMENT '创建人',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `summary` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '试卷概述',
  `dt_class` int(11) DEFAULT NULL COMMENT 'dtree,课程类型',
  `time_limit` int(11) DEFAULT 0 COMMENT '答题时间(分钟,0+),0为无限制',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_testpaper_question
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_testpaper_question`;
CREATE TABLE `zf_edu_testpaper_question`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `sort` int(11) NOT NULL COMMENT '序号',
  `update_time` int(11) NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT 0 COMMENT '多少分,10为1分',
  `dt_part` int(11) DEFAULT 0 COMMENT 'dtree,所属部分',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_unit
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_unit`;
CREATE TABLE `zf_edu_unit`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL COMMENT '书籍商品ID',
  `unit_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '单元名称',
  `parent_unit` int(11) NOT NULL COMMENT '父级单元',
  `is_free` tinyint(4) NOT NULL COMMENT '是否免费体验',
  `price` int(11) NOT NULL COMMENT '销售价格',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `level` int(11) NOT NULL COMMENT '单元层,1-3',
  `has_answer` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否需要提交答案',
  `time_limit` int(11) NOT NULL DEFAULT 0 COMMENT '答题时间(分钟,0+),0为无限制',
  `is_tip` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否播报题号',
  `is_rand` int(11) NOT NULL DEFAULT 0 COMMENT '是否打乱',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `audio_id` int(10) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '书籍单元表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_unit_question
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_unit_question`;
CREATE TABLE `zf_edu_unit_question`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '序号',
  `update_time` int(11) NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_user_book
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_user_book`;
CREATE TABLE `zf_edu_user_book`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `book_id` int(11) NOT NULL COMMENT '书类商品id',
  `unit` int(11) NOT NULL COMMENT '单元,暂无用',
  `expire_time` int(11) NOT NULL COMMENT '过期时间',
  `buy_time` int(11) NOT NULL COMMENT '购买时间',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_uid_book`(`uid`, `book_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '已购买书籍-单元表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_edu_user_question
-- ----------------------------
DROP TABLE IF EXISTS `zf_edu_user_question`;
CREATE TABLE `zf_edu_user_question`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL COMMENT '问题id',
  `user_answer` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户回答的答案',
  `answer_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=>错误,1=>正确,2=>漏题',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `testpaper_id` int(11) NOT NULL COMMENT '0的时候表示单题目，大于0 表示与试卷关联',
  `bookunit_id` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) DEFAULT NULL COMMENT 'UID',
  `report_id` int(11) NOT NULL COMMENT '提交id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house
-- ----------------------------
DROP TABLE IF EXISTS `zf_house`;
CREATE TABLE `zf_house`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房源标题信息',
  `selling_point` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '卖点',
  `house_no` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '房源编号（）',
  `province` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所在省份编码',
  `city` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所在城市编码',
  `area` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所在区域编码',
  `area_zone` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '社区CODE',
  `community` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '小区CODE',
  `address_detail` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '详细地址',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `publish_time` int(11) NOT NULL DEFAULT 0 COMMENT '发布时间',
  `owner_uid` int(11) NOT NULL COMMENT '房源发布者ID',
  `house_area` int(11) NOT NULL COMMENT '房源面积（单位：平米）',
  `abstract` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '户型介绍\n',
  `notice` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '入住须知',
  `transportation` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交通出行\n',
  `education` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '教育配套',
  `status` tinyint(4) NOT NULL COMMENT '房源状态（1：正常 2：已下架 3：已被申请成功 -1：已删除）',
  `come_from` int(11) NOT NULL DEFAULT 0 COMMENT '0app,1admin,房源来源（来源跟品牌有不同含义）',
  `house_brand` int(11) NOT NULL DEFAULT 0 COMMENT '房源品牌（数据字典： 例子：住家）',
  `house_dir` int(11) NOT NULL COMMENT '房源朝向，dtree:house_orientation',
  `rent_type` int(11) NOT NULL COMMENT '出租方式（1：整租 2：合租-主卧，3：合租-次卧）',
  `lng` double(12, 7) NOT NULL COMMENT '经度',
  `lat` double(12, 7) NOT NULL COMMENT '纬度',
  `house_room` int(11) NOT NULL DEFAULT 0 COMMENT '几室',
  `house_hall` int(10) NOT NULL DEFAULT 0 COMMENT '几厅',
  `toilet` smallint(6) NOT NULL DEFAULT 0 COMMENT '几卫',
  `is_entrust` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否委托第三方',
  `entrust_money` int(11) NOT NULL DEFAULT 0 COMMENT '委托成交的佣金（单位:分)',
  `has_front_money` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否需要定金',
  `front_money` int(11) NOT NULL DEFAULT 0 COMMENT '定金金额（单位:分）',
  `contact_phone` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `contact_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `house_decoration` int(11) NOT NULL COMMENT '房源装修情况,dtree:house_decoration',
  `subway` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '最近的一个地铁站CODE',
  `school_zone` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '校区CODE',
  `service_charge` int(11) NOT NULL DEFAULT 0 COMMENT '服务费 (分/每月）',
  `rent` int(11) NOT NULL DEFAULT 0 COMMENT '租金（分/每月），0为面谈',
  `floor_at` int(11) NOT NULL DEFAULT 0 COMMENT '楼层',
  `floor_all` int(11) NOT NULL DEFAULT 0 COMMENT '总楼层',
  `house_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '房源类型(1:个人房源 2:经纪人)',
  `house_property` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `view_count` int(11) DEFAULT 0 COMMENT '查看次数',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `house_no`(`house_no`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源基础信息表(后缀跟上城市编号)' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_agent
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_agent`;
CREATE TABLE `zf_house_agent`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `house_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房源编号',
  `house_brokerager` int(11) NOT NULL COMMENT '经纪人id',
  `view_cnt` int(11) NOT NULL COMMENT '看房次数',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源经济人' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_apply
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_apply`;
CREATE TABLE `zf_house_apply`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '申请人用户id',
  `house_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房源编号',
  `apply_time` int(11) NOT NULL COMMENT '申请时间',
  `apply_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '申请状态（1：申请成功 0: 等候审核）',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源签约申请表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_img
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_img`;
CREATE TABLE `zf_house_img`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `house_no` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房源编号',
  `img_id` int(11) NOT NULL COMMENT '图片ID',
  `img_type` int(11) NOT NULL COMMENT '图片类型（参考数据字典表）',
  `img_title` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '图片标题',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源相关图片表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_look_apply
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_look_apply`;
CREATE TABLE `zf_house_look_apply`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `house_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `apply_time` int(11) NOT NULL,
  `apply_status` tinyint(4) NOT NULL,
  `mark` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `starttime` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '开始时间',
  `endtime` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源看房申请表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_pay_method
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_pay_method`;
CREATE TABLE `zf_house_pay_method`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `deposit` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '押金（分/每月）',
  `type` int(11) UNSIGNED NOT NULL COMMENT '数据字典：house_pay',
  `house_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房源编号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '付款方式 ' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_prop
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_prop`;
CREATE TABLE `zf_house_prop`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) NOT NULL COMMENT '属性ID',
  `prop_value_id` int(11) NOT NULL COMMENT '属性值ID',
  `prop_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '属性名称',
  `prop_value` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '属性值名称',
  `prop_type` int(11) NOT NULL COMMENT '属性类型（基本属性、交易属性、其它属性、房内配套设施、公共配套设施）',
  `house_no` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源属性' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_tag
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_tag`;
CREATE TABLE `zf_house_tag`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `house_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tag_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '冗余标签名，便于查询',
  `tag_type` int(11) NOT NULL COMMENT '分类【1:标签(认证,付费选项) ,2:特色(用户可选项)】',
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源标签关联,比如： 是否精装修，是否随时看房，是否供暖' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_trade
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_trade`;
CREATE TABLE `zf_house_trade`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `house_no` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房源编号',
  `rent` int(11) NOT NULL COMMENT '成交租金',
  `house_room` int(3) NOT NULL COMMENT '房间数',
  `province` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所在省份编码',
  `city` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所在城市编码',
  `area` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所在区域编码',
  `area_zone` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '社区CODE',
  `community` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '小区CODE',
  `detail` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房源信息快照(房源表json数据)',
  `create_time` int(11) NOT NULL COMMENT '成交时间',
  `trade_year` int(4) UNSIGNED NOT NULL COMMENT '交易完成年',
  `trade_month` int(2) UNSIGNED NOT NULL COMMENT '交易完成月',
  `trade_day` int(2) UNSIGNED NOT NULL COMMENT '交易完成日',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源交易记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_house_wanted
-- ----------------------------
DROP TABLE IF EXISTS `zf_house_wanted`;
CREATE TABLE `zf_house_wanted`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `city` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '城市编码',
  `city_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '城市名称',
  `area` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所在区域编码',
  `area_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '区域名称',
  `area_zone` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '社区CODE',
  `area_zone_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '社区名称',
  `rent` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '租金',
  `house_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '户型',
  `latest_check` int(11) NOT NULL COMMENT '最晚入住时间',
  `contact` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '联系人',
  `mobile` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机号码',
  `is_open` tinyint(1) NOT NULL COMMENT '是否公开：0不公开 1公开',
  `is_push` tinyint(1) NOT NULL COMMENT '是否信息推送房源 0否 1是',
  `is_entrust` tinyint(4) NOT NULL COMMENT '是否委托经纪人',
  `desc` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房源描述',
  `create_time` int(11) NOT NULL COMMENT '创建日期',
  `update_time` int(11) NOT NULL COMMENT '修改日期',
  `status` int(1) NOT NULL COMMENT '状态 0未审核 1已审核 -1已删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房源求租表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_lock
-- ----------------------------
DROP TABLE IF EXISTS `zf_lock`;
CREATE TABLE `zf_lock`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lock_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '锁编号（全局唯一）',
  `lock_type` int(11) NOT NULL COMMENT '锁类型（参考数据字典），用于以后更换锁类型的情况',
  `lock_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '锁名',
  `owner_uid` int(11) NOT NULL COMMENT '拥有者UID',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `lock_version` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '锁版本信息(json格式)',
  `lock_mac` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'lock mac address',
  `lock_key` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '锁key',
  `house_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '房源编号',
  `lock_flag_pos` int(11) NOT NULL DEFAULT 0 COMMENT '重置密码次数,重置管理员时=0,重置用户钥匙时+1',
  `aes_key_str` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'key aes加密 字符串',
  `lock_alias` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '别名',
  `keyboard_pwd_version` tinyint(4) NOT NULL DEFAULT 0 COMMENT '键盘密码版本，0-未知、1-老版900个键盘密码、2-新版300个键盘密码，最长180天、3-新版300个键盘密码，支持月份和永久',
  `push` tinyint(2) NOT NULL DEFAULT 1 COMMENT '开锁推送开关',
  `power` int(3) NOT NULL DEFAULT 0 COMMENT '电量',
  `model_num` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `hardware_revision` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `firmware_revision` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `lock_id`(`lock_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '锁信息表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_lock_his
-- ----------------------------
DROP TABLE IF EXISTS `zf_lock_his`;
CREATE TABLE `zf_lock_his`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `to_uid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `operate` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `result` tinyint(4) NOT NULL,
  `extra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lock_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `key_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '智能锁操作历史 - 开锁除外' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_lock_key
-- ----------------------------
DROP TABLE IF EXISTS `zf_lock_key`;
CREATE TABLE `zf_lock_key`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lock_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `key_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `status` int(6) NOT NULL COMMENT '钥匙状态',
  `uid` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT -1 COMMENT '钥匙类型,0=>管理员，1=>用户,2=>租户,3=>租户用户',
  `lockFlagPos` int(11) NOT NULL DEFAULT 0 COMMENT '锁开门标志位,仅保存,调试用',
  `adminPwd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员钥匙会有，锁的管理员密码，锁管理相关操作需要携带，校验管理员权限',
  `noKeyPwd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员钥匙会有，管理员键盘密码，管理员用该密码开门',
  `deletePwd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '二代锁的管理员钥匙会有，清空码，用于清空锁上的密码',
  `aesKeyStr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'Aes加解密Key,仅保存,调试用',
  `timezoneRawOffset` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '锁所在时区和UTC时区时间的差数，单位milliseconds',
  `mark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '别名',
  `electricQuantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `from_uid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `key_id`(`key_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '智能锁钥匙' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_lock_keyboard
-- ----------------------------
DROP TABLE IF EXISTS `zf_lock_keyboard`;
CREATE TABLE `zf_lock_keyboard`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lock_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keyboard_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `keyboard_pwd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `type` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '密码类型,1,2,3,4',
  `send_time` int(11) NOT NULL,
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `to_uid` int(11) DEFAULT NULL,
  `app_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `alias` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '别名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_lock_manager
-- ----------------------------
DROP TABLE IF EXISTS `zf_lock_manager`;
CREATE TABLE `zf_lock_manager`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lock_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `uid` int(11) NOT NULL,
  `start` int(10) UNSIGNED NOT NULL,
  `end` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0=>管理者，1=>授权用户',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '锁管理员和授权用户 - 已废弃' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_lock_record
-- ----------------------------
DROP TABLE IF EXISTS `zf_lock_record`;
CREATE TABLE `zf_lock_record`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lock_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `result` tinyint(4) DEFAULT NULL COMMENT '开锁结果',
  `type` tinyint(4) DEFAULT NULL COMMENT '开锁类型',
  `create_time` int(11) DEFAULT NULL,
  `op_time` int(11) DEFAULT NULL,
  `server_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for zf_lottery
-- ----------------------------
DROP TABLE IF EXISTS `zf_lottery`;
CREATE TABLE `zf_lottery`  (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `views` int(11) NOT NULL COMMENT '查看次数',
  `playtimes` int(11) NOT NULL COMMENT '参与游戏人次',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '游戏定义' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_lottery_gain
-- ----------------------------
DROP TABLE IF EXISTS `zf_lottery_gain`;
CREATE TABLE `zf_lottery_gain`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prize_id` int(11) NOT NULL DEFAULT 0,
  `game_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `get_time` int(11) NOT NULL COMMENT '获得时间',
  `prize_type` smallint(6) DEFAULT 0,
  `itboye_getprize_hiscol` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '参与人记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_lottery_prize
-- ----------------------------
DROP TABLE IF EXISTS `zf_lottery_prize`;
CREATE TABLE `zf_lottery_prize`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `prize_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '奖品名称',
  `prize_cnt` int(11) NOT NULL COMMENT '奖品数量',
  `probability` decimal(4, 2) NOT NULL COMMENT '概率（0-1之间,保留2位小数点）',
  `prize_type` smallint(6) NOT NULL DEFAULT 0 COMMENT '礼品类别(1: 积分[到账];2:购物券[到账],3: 商品[0.1购物车] 4: 实物[联系])',
  `prize_icon` int(11) NOT NULL COMMENT '奖品图标',
  `prize_num` int(11) NOT NULL DEFAULT 0 COMMENT '(单件积分/购物券数量或商品Id,实物为0)',
  `sort` int(4) NOT NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '奖品' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_oauth_access_token
-- ----------------------------
DROP TABLE IF EXISTS `zf_oauth_access_token`;
CREATE TABLE `zf_oauth_access_token`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `access_token` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_oauth_code
-- ----------------------------
DROP TABLE IF EXISTS `zf_oauth_code`;
CREATE TABLE `zf_oauth_code`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `authorization_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `redirect_uri` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_token` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_oauth_jwf
-- ----------------------------
DROP TABLE IF EXISTS `zf_oauth_jwf`;
CREATE TABLE `zf_oauth_jwf`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `public_key` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_oauth_public_key
-- ----------------------------
DROP TABLE IF EXISTS `zf_oauth_public_key`;
CREATE TABLE `zf_oauth_public_key`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `public_key` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `private_key` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `encryption_algorithm` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'RS256',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_oauth_refresh_token
-- ----------------------------
DROP TABLE IF EXISTS `zf_oauth_refresh_token`;
CREATE TABLE `zf_oauth_refresh_token`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `client_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uid` int(10) UNSIGNED DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_oauth_scope
-- ----------------------------
DROP TABLE IF EXISTS `zf_oauth_scope`;
CREATE TABLE `zf_oauth_scope`  (
  `id` int(11) NOT NULL,
  `scope` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_df` tinyint(1) UNSIGNED NOT NULL COMMENT '是否默认',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2c
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2c`;
CREATE TABLE `zf_p2c`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `expected_annualize` int(11) NOT NULL COMMENT '年化收益',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` int(11) DEFAULT 0 COMMENT '状态(1=>正常,0=>挂起/禁用,-1删除)',
  `last_day` date DEFAULT NULL COMMENT '上次回息时间',
  `start_time` int(11) DEFAULT 0 COMMENT '开放时间',
  `end_time` int(11) DEFAULT 0 COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2c_invest
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2c_invest`;
CREATE TABLE `zf_p2c_invest`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id\n',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `ft_id` int(11) NOT NULL COMMENT '理财宝id',
  `money` int(11) NOT NULL COMMENT '存入金额(单位：分)',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `last_day` date DEFAULT NULL COMMENT '上次回息时间,格式 YYYY-MM-DD,利息属于前一天的',
  `interest` int(11) DEFAULT 0 COMMENT '总利息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '投资表,历史写到wallet_his' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2c_payment
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2c_payment`;
CREATE TABLE `zf_p2c_payment`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ft_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT 0,
  `money` int(11) DEFAULT 0 COMMENT '当时的投资额,回款前',
  `rate` int(11) DEFAULT 0 COMMENT '计算时的万倍年化率',
  `interest` int(11) DEFAULT 0 COMMENT '利息(去掉手续费)',
  `fee` int(11) DEFAULT 0 COMMENT '利息手续费',
  `date` date DEFAULT NULL COMMENT '利息计算于,属于前一天',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '回款记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2c_plan
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2c_plan`;
CREATE TABLE `zf_p2c_plan`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ft_id` int(11) NOT NULL COMMENT '项目id',
  `create_time` int(11) NOT NULL COMMENT '0',
  `date` date NOT NULL COMMENT '计息时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '任务计划日志' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2p
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2p`;
CREATE TABLE `zf_p2p`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '项目标题',
  `dt_type` int(11) NOT NULL COMMENT '项目类型',
  `dt_repayment_type` int(11) NOT NULL COMMENT '还款方式',
  `total_money` bigint(25) NOT NULL COMMENT '项目总金额（单位：分）',
  `dt_ttsci` int(11) NOT NULL COMMENT '非数据字典int,开始计算利息的时间,都是在项目募集完成后多少天',
  `project_duration_month` int(11) NOT NULL COMMENT '项目工期(单位：月)',
  `project_duration_day` int(11) NOT NULL COMMENT '项目工期(单位：天)',
  `expected_annualize` int(11) NOT NULL COMMENT '预期年化收益(数据库存1120=》0.1120=》11.20% )',
  `create_time` int(11) NOT NULL,
  `detail` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '项目介绍详情',
  `update_time` int(11) NOT NULL,
  `min_invest_amount` int(11) NOT NULL DEFAULT 0 COMMENT '最小投资金额单位（分）',
  `max_invest_amount` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最大投资金额单位（分）',
  `n_multiple` int(11) NOT NULL COMMENT '而且必须是多少的整数倍才能投资 ',
  `dt_come_from` int(11) NOT NULL COMMENT '项目来源',
  `b_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '项目状态（0=>起草中.1=>募集中.2=>募集完成.3=>计息中.4=>项目完成.原+10=>项目挂起）',
  `dt_agreement_type` int(11) NOT NULL COMMENT '合同范本模板',
  `dt_tag` int(11) NOT NULL COMMENT '项目标签（单个）',
  `interest_start_time` date DEFAULT NULL COMMENT '计息时间(后台募集完自动计算)',
  `interest_end_time` date DEFAULT NULL COMMENT '结束时间,后台募集完根据计息时间自动计算',
  `next_interest_time` date DEFAULT NULL COMMENT '下期还款时间,后台募集完和还款时自动计算',
  `original_id` int(11) DEFAULT 0 COMMENT '原来的id,转让类型有效',
  `original_interest` int(11) DEFAULT 0 COMMENT '转让利息',
  `original_owner` int(11) DEFAULT 0 COMMENT '原持有人,转让有效',
  `fee` int(11) DEFAULT 0 COMMENT '万倍转让手续费率(本金)',
  `safety` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '安全审核',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '项目' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2p_attr
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2p_attr`;
CREATE TABLE `zf_p2p_attr`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `remaining_amount` int(11) NOT NULL COMMENT '剩余可投资金额（分）',
  `project_item_json` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '抵押类项目-抵押物品信息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '项目属性' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2p_borrower
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2p_borrower`;
CREATE TABLE `zf_p2p_borrower`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '姓名',
  `idcard` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '身份证',
  `sex` tinyint(2) NOT NULL DEFAULT 0,
  `age` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_married` tinyint(2) NOT NULL DEFAULT 0,
  `usage_of_loan` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '借款用途',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '项目借款人信息 - 非必需,暂未使用' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2p_lender
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2p_lender`;
CREATE TABLE `zf_p2p_lender`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `investment` int(11) NOT NULL COMMENT '投资金额（单位：分）',
  `create_time` int(11) NOT NULL COMMENT '创建时间 / 投资时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'p2p 用户投资记录 - 每投资一次 记录一次' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2p_loan
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2p_loan`;
CREATE TABLE `zf_p2p_loan`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '项目标题',
  `dt_type` int(11) NOT NULL DEFAULT 0 COMMENT '项目类型 - 暂无用',
  `dt_repayment_type` int(11) NOT NULL COMMENT '还款方式',
  `total_money` bigint(25) NOT NULL COMMENT '贷款金额（单位：分）',
  `month` int(11) NOT NULL COMMENT '项目工期(单位：月),几月几期',
  `day` int(11) NOT NULL COMMENT '项目工期(单位：天),1期',
  `expected_annualize` int(11) NOT NULL COMMENT '预期年化收益(数据库存1120=》0.1120=》11.20% )',
  `create_time` int(11) NOT NULL COMMENT '贷款时间',
  `update_time` int(11) NOT NULL,
  `detail` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '贷款详情 , editor',
  `p2p_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联的定期项目id',
  `b_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '项目状态（0=>起草中/申请中.1=>已发放还款中.2=>还款完成或提前还完.3=>项目挂起/异常了）',
  `dt_agreement_type` int(11) NOT NULL COMMENT '合同范本模板',
  `dt_tag` int(11) NOT NULL COMMENT '项目标签（单个） - 暂无用',
  `pay_start_time` date NOT NULL COMMENT '计息时间(后台贷款发放完自动计算/或选择时间)',
  `pay_end_time` date NOT NULL COMMENT '结束时间,后台募集完根据计息时间自动计算',
  `uid` int(11) NOT NULL COMMENT '贷款用户',
  `purpose` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '贷款用途',
  `loan_time` int(11) NOT NULL DEFAULT 0 COMMENT '放贷时间',
  `note` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `next_pay_time` date NOT NULL,
  `address_list` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_sms_date` date DEFAULT NULL COMMENT '上次发送短信的日期(定时任务中短信控制)',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '项目' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2p_loan_payment
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2p_loan_payment`;
CREATE TABLE `zf_p2p_loan_payment`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `pay_time` int(11) NOT NULL COMMENT '回款时间',
  `principal` int(11) NOT NULL COMMENT '支付本金( 单位：分）',
  `accrual` int(11) NOT NULL COMMENT '支付利息（单位：分）',
  `rank` int(11) NOT NULL DEFAULT 0 COMMENT '第几期',
  `has_pay` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否支付',
  `can_pay_start` int(11) NOT NULL COMMENT '开始支付时间',
  `can_pay_end` int(11) NOT NULL COMMENT '封闭支付时间',
  `update_time` int(11) NOT NULL,
  `pay_money` int(11) NOT NULL DEFAULT 0 COMMENT '实际支付',
  `pay_wallet` int(11) DEFAULT 0 COMMENT '支付的余额',
  `pay_type` tinyint(4) DEFAULT 0 COMMENT '支付类型',
  `pay_code` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '第三方支付码',
  `overdue_money` int(11) DEFAULT 0 COMMENT '滞纳金',
  `overdue_rate` int(11) DEFAULT 5 COMMENT '逾期,日利息,万分',
  `last_sms_time` int(11) NOT NULL DEFAULT 0 COMMENT '上次发送短信时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '项目回款计划和记录,回一次当期改变为已支付' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2p_payment
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2p_payment`;
CREATE TABLE `zf_p2p_payment`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `date` date NOT NULL COMMENT '应回款时间',
  `principal` int(11) NOT NULL COMMENT '回款本金( 单位：分）',
  `accrual` int(11) NOT NULL COMMENT '回款利息（单位：分)',
  `pay_time` int(11) NOT NULL COMMENT '回款时间',
  `rank` int(11) NOT NULL COMMENT '第几期',
  `pay_total` int(11) NOT NULL DEFAULT 0 COMMENT '实际回款(分),去手续费',
  `is_transfer` int(11) NOT NULL DEFAULT 0 COMMENT '0=>未转让,1=>已转让',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '项目回款分期计划,回一次改一次,转让标志' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_p2p_plan
-- ----------------------------
DROP TABLE IF EXISTS `zf_p2p_plan`;
CREATE TABLE `zf_p2p_plan`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL COMMENT '项目id',
  `create_time` int(11) NOT NULL COMMENT '0',
  `date` date NOT NULL COMMENT '计息时间',
  `msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '任务计划日志' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_repair
-- ----------------------------
DROP TABLE IF EXISTS `zf_repair`;
CREATE TABLE `zf_repair`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '申请人id',
  `mobile` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `price` int(11) NOT NULL DEFAULT 0 COMMENT '维修价格',
  `repair_type` int(11) NOT NULL DEFAULT 0 COMMENT '维修类型,dtree',
  `vehicle_type` int(11) NOT NULL DEFAULT 0 COMMENT '车辆类型,dtree',
  `detail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '情况说明',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `images` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '现场图片',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态',
  `repair_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '维修状态',
  `lng` decimal(9, 6) NOT NULL DEFAULT 0.000000 COMMENT '经度',
  `lat` decimal(9, 6) NOT NULL DEFAULT 0.000000 COMMENT '纬度',
  `geohash` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `worker_uid` int(8) NOT NULL DEFAULT 0 COMMENT '维修师傅id',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `evaluate` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评价',
  `evaluate_time` int(11) NOT NULL DEFAULT 0 COMMENT '评价时间',
  `city` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `area` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `repair_price` int(11) NOT NULL,
  `stuff_price` int(11) NOT NULL,
  `note` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `fee` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '维修表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_repair_apply
-- ----------------------------
DROP TABLE IF EXISTS `zf_repair_apply`;
CREATE TABLE `zf_repair_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '申请人id',
  `uname` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '申请人姓名',
  `mobile` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '联系电话',
  `repair_id` int(8) NOT NULL COMMENT '维修项目id',
  `repair_status` int(11) NOT NULL,
  `money` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '维修金额(单位:分)',
  `detail` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '情况说明',
  `status` int(4) DEFAULT NULL COMMENT '维修状态',
  `worker_id` int(8) DEFAULT NULL COMMENT '维修师傅id',
  `address` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '详细地址',
  `community` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '区域code',
  `evaluate` int(11) DEFAULT 0 COMMENT '评价',
  `evatags` char(40) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0' COMMENT '标签',
  `image` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_repair_project
-- ----------------------------
DROP TABLE IF EXISTS `zf_repair_project`;
CREATE TABLE `zf_repair_project`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `repair_type` int(8) NOT NULL COMMENT '维修类型',
  `name` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '维修名称',
  `repair_ill` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '维修介绍',
  `repair_check` int(11) NOT NULL COMMENT '检测报价',
  `repair_repay` int(11) NOT NULL COMMENT '检修报价',
  `repair_little` int(11) NOT NULL COMMENT '小修报价',
  `repair_big` int(11) NOT NULL COMMENT '大修报价',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '维修项目表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_repair_worker
-- ----------------------------
DROP TABLE IF EXISTS `zf_repair_worker`;
CREATE TABLE `zf_repair_worker`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '师傅id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '师傅姓名',
  `mobile` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '联系电话',
  `projectid` int(11) NOT NULL COMMENT '服务种类',
  `area` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '服务行政区',
  `rate` int(11) DEFAULT NULL COMMENT '评级',
  `areazone` char(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '小区1',
  `head` char(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '头像',
  `count` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '维修师傅表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_sgoods
-- ----------------------------
DROP TABLE IF EXISTS `zf_sgoods`;
CREATE TABLE `zf_sgoods`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '商品名称',
  `count` int(11) NOT NULL DEFAULT 0 COMMENT '数量,-1为无限',
  `cate_id` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '商品分类ID',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `onshelf` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否上架(0:否,1:是)',
  `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '商品状态(-1删除，0正常，1审核)',
  `store_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺ID',
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '详情',
  `synopsis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '商品简介',
  `price` decimal(16, 2) NOT NULL DEFAULT 0.00 COMMENT '价格（积分）',
  `score` int(11) NOT NULL DEFAULT 0 COMMENT '积分',
  `main_img` int(11) NOT NULL DEFAULT 0 COMMENT '商品主图',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=>其他,1=>实物,2=>红包,3=>优惠券',
  `tpl_id` int(10) UNSIGNED DEFAULT 0 COMMENT '整数，对应type的模板ID，实物时为邮费',
  `sort` int(10) UNSIGNED DEFAULT 0 COMMENT '排序，越大越靠前',
  `brand` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '品牌',
  `buy_limit` int(10) UNSIGNED DEFAULT 0 COMMENT '限购，0=不限',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '积分商品表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_sgoods_exchange
-- ----------------------------
DROP TABLE IF EXISTS `zf_sgoods_exchange`;
CREATE TABLE `zf_sgoods_exchange`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `pname` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '商品名称',
  `price` decimal(16, 2) NOT NULL DEFAULT 0.00 COMMENT '兑换时商品金额+邮费',
  `score` decimal(16, 2) NOT NULL COMMENT '支付积分',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `exchange_time` int(11) NOT NULL COMMENT '兑换时间',
  `express_no` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '快递单号',
  `express_company` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '快递公司名称',
  `express_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '快递公司编码',
  `exchange_status` tinyint(3) NOT NULL DEFAULT -1 COMMENT '-1:代付款,0: 待发货 1: 已发货 ',
  `address_id` int(11) NOT NULL DEFAULT 0,
  `address_province` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '省',
  `address_city` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '市',
  `address_area` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '区',
  `address_detail` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '地址详细',
  `pay_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否已支付(抵扣部分金额的商品)',
  `pay_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '交易号(S_ 开头)',
  `pay_balance` decimal(16, 2) UNSIGNED DEFAULT 0.00 COMMENT '余额支付',
  `type` tinyint(3) UNSIGNED DEFAULT 0,
  `tpl_id` int(11) UNSIGNED DEFAULT 0 COMMENT '邮费/模板ID',
  `tpl_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '模板名称',
  `main_img` int(10) UNSIGNED DEFAULT 0,
  `brand` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '积分商品换购记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_view_page
-- ----------------------------
DROP TABLE IF EXISTS `zf_view_page`;
CREATE TABLE `zf_view_page`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `notes` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定义webview视图页面' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_view_page_item
-- ----------------------------
DROP TABLE IF EXISTS `zf_view_page_item`;
CREATE TABLE `zf_view_page_item`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `item_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dt_view_type` int(11) NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '视图页面数据' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_welware_apply
-- ----------------------------
DROP TABLE IF EXISTS `zf_welware_apply`;
CREATE TABLE `zf_welware_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `reason` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '申请理由',
  `apply_time` int(11) NOT NULL COMMENT '申请时间',
  `goods_id` int(11) NOT NULL COMMENT '申请获赠物品id',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '地址',
  `mobile` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `linkman` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_welware_gain
-- ----------------------------
DROP TABLE IF EXISTS `zf_welware_gain`;
CREATE TABLE `zf_welware_gain`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '获得者用户ID',
  `win_time` int(11) NOT NULL COMMENT '获得时间',
  `apply_id` int(11) NOT NULL COMMENT '申请纪录id',
  `goods_id` int(11) NOT NULL COMMENT '获得物品ID',
  `score1` decimal(4, 2) NOT NULL DEFAULT 0.00 COMMENT '评分1',
  `score2` decimal(4, 2) NOT NULL DEFAULT 0.00 COMMENT '评分2',
  `score3` decimal(4, 2) NOT NULL DEFAULT 0.00 COMMENT '评分3',
  `evaluate_time` int(11) NOT NULL DEFAULT 0 COMMENT '评价时间',
  `evaluate_content` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '评价内容',
  `ep_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '物流单号',
  `dt_ep_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '物流公司编码',
  `dt_ep_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '物流公司名称',
  `express_time` int(11) NOT NULL DEFAULT 0 COMMENT '发货时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '获得者信息' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for zf_welware_image
-- ----------------------------
DROP TABLE IF EXISTS `zf_welware_image`;
CREATE TABLE `zf_welware_image`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `img_id` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
