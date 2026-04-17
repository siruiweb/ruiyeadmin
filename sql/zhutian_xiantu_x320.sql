/*
 诸天仙途 - X320 复活次数记录数据库更新
 Date: 2026-04-17
 功能：
 - 实现玩家复活次数记录功能
 - 死亡时自动增加复活次数
 - 限制最大复活次数（如3次）
 - 复活次数用尽后无法复活
*/

SET FOREIGN_KEY_CHECKS = 0;

-- ========================================
-- X320: fa_player表添加复活次数限制字段
-- ========================================
ALTER TABLE `fa_player` 
  ADD COLUMN `revive_count` tinyint(3) unsigned DEFAULT '0' COMMENT '当前复活次数' AFTER `max_sp`,
  ADD COLUMN `max_revive_count` tinyint(3) unsigned DEFAULT '3' COMMENT '最大复活次数限制' AFTER `revive_count`,
  ADD COLUMN `last_revive_reset` int(11) unsigned DEFAULT '0' COMMENT '上次复活次数重置时间' AFTER `max_revive_count`;

-- ========================================
-- X320: 复活日志表
-- ========================================
CREATE TABLE IF NOT EXISTS `fa_xiuxian_revival_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `player_id` int(11) NOT NULL COMMENT '玩家ID',
  `death_time` int(11) NOT NULL COMMENT '死亡时间戳',
  `revival_time` int(11) DEFAULT '0' COMMENT '复活时间戳',
  `realm` varchar(50) DEFAULT '练体期' COMMENT '死亡时境界',
  `revival_count` tinyint(3) unsigned DEFAULT '0' COMMENT '当前复活次数（第几次复活）',
  `death_location` varchar(128) DEFAULT '' COMMENT '死亡地点',
  `death_cause` varchar(128) DEFAULT '' COMMENT '死亡原因',
  `killer_name` varchar(64) DEFAULT '' COMMENT '击杀者名称',
  `revive_type` varchar(32) DEFAULT '' COMMENT '复活方式',
  `cost_type` varchar(32) DEFAULT '' COMMENT '消耗类型：exp/lingshi/item',
  `cost_amount` int(11) DEFAULT '0' COMMENT '消耗数量',
  `createtime` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `death_time` (`death_time`),
  KEY `revival_count` (`revival_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='复活日志表';

-- ========================================
-- X320: 初始化现有玩家的复活次数
-- ========================================
UPDATE `fa_player` SET `revive_count` = 0, `max_revive_count` = 3, `last_revive_reset` = UNIX_TIMESTAMP() WHERE `revive_count` IS NULL OR `revive_count` = 0;

SET FOREIGN_KEY_CHECKS = 1;
