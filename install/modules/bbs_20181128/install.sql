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

 Date: 28/11/2018 10:51:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for f_bbs
-- ----------------------------
DROP TABLE IF EXISTS `f_bbs`;
CREATE TABLE `f_bbs`  (
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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '版块表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_bbs_attach
-- ----------------------------
DROP TABLE IF EXISTS `f_bbs_attach`;
CREATE TABLE `f_bbs_attach`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL COMMENT '贴子ID',
  `ids` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '附件ID,拼接',
  `rid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复ID,区分附件归属帖子/回复',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=>img',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '附件索引表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_bbs_ban
-- ----------------------------
DROP TABLE IF EXISTS `f_bbs_ban`;
CREATE TABLE `f_bbs_ban`  (
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
-- Table structure for f_bbs_post
-- ----------------------------
DROP TABLE IF EXISTS `f_bbs_post`;
CREATE TABLE `f_bbs_post`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '作者ID',
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `create_time` int(11) NOT NULL COMMENT '发表时间',
  `update_time` int(11) NOT NULL COMMENT '发表时间',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '帖子内容',
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
-- Table structure for f_bbs_post_tag
-- ----------------------------
DROP TABLE IF EXISTS `f_bbs_post_tag`;
CREATE TABLE `f_bbs_post_tag`  (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tid` int(11) UNSIGNED NOT NULL,
  `fid` int(11) UNSIGNED NOT NULL COMMENT '论坛ID',
  `tag_id` int(11) NOT NULL COMMENT '标签ID'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '论坛帖子标签关系表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for f_bbs_reply
-- ----------------------------
DROP TABLE IF EXISTS `f_bbs_reply`;
CREATE TABLE `f_bbs_reply`  (
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
-- Table structure for f_bbs_report
-- ----------------------------
DROP TABLE IF EXISTS `f_bbs_report`;
CREATE TABLE `f_bbs_report`  (
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

SET FOREIGN_KEY_CHECKS = 1;
