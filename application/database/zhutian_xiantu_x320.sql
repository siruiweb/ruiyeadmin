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
-- X320: 初始化现有玩家的复活次数限制
-- ========================================
UPDATE `fa_player` SET `revive_count` = 0, `max_revive_count` = 3, `last_revive_reset` = UNIX_TIMESTAMP() WHERE `revive_count` IS NULL OR `revive_count` = 0;

SET FOREIGN_KEY_CHECKS = 1;
