-- =========================================
-- 诸天仙途 - 复活记录表
-- 数据库: zhutianxiantu
-- 表前缀: fa_xt_
-- =========================================

-- 1. 复活记录表（追踪每次复活）
DROP TABLE IF EXISTS `fa_xt_revive_record`;
CREATE TABLE `fa_xt_revive_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL COMMENT '玩家ID',
  `player_name` varchar(64) DEFAULT '' COMMENT '玩家名称',
  `revive_type` varchar(32) NOT NULL COMMENT '复活类型：normal/lingshi/item/hospital',
  `revive_location` varchar(128) DEFAULT '' COMMENT '复活地点',
  `death_cause` varchar(128) DEFAULT '' COMMENT '死亡原因',
  `killer_name` varchar(64) DEFAULT '' COMMENT '击杀者名称',
  `killer_type` varchar(32) DEFAULT '' COMMENT '击杀者类型：monster/player/other',
  `death_location` varchar(128) DEFAULT '' COMMENT '死亡地点',
  `death_time` int(11) DEFAULT 0 COMMENT '死亡时间',
  `revive_time` int(11) DEFAULT 0 COMMENT '复活时间',
  `time_diff` int(11) DEFAULT 0 COMMENT '死亡到复活间隔(秒)',
  `cost_type` varchar(32) DEFAULT '' COMMENT '消耗类型：exp/lingshi/item/none',
  `cost_amount` int(11) DEFAULT 0 COMMENT '消耗数量',
  `hp_before` int(11) DEFAULT 0 COMMENT '死亡前气血',
  `hp_after` int(11) DEFAULT 0 COMMENT '复活后气血',
  `realm_level` int(11) DEFAULT 1 COMMENT '境界等级',
  `realm_name` varchar(64) DEFAULT '' COMMENT '境界名称',
  `is_reversal` tinyint(1) DEFAULT 0 COMMENT '是否绝境翻盘',
  `reversal_result` varchar(32) DEFAULT '' COMMENT '翻盘结果：success/fail',
  `create_time` int(11) DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `death_time` (`death_time`),
  KEY `revive_time` (`revive_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='复活记录表';

-- 2. 玩家复活统计表（汇总数据）
DROP TABLE IF EXISTS `fa_xt_revive_stats`;
CREATE TABLE `fa_xt_revive_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL COMMENT '玩家ID',
  `player_name` varchar(64) DEFAULT '' COMMENT '玩家名称',
  `total_deaths` int(11) DEFAULT 0 COMMENT '总死亡次数',
  `total_revives` int(11) DEFAULT 0 COMMENT '总复活次数',
  `today_revives` int(11) DEFAULT 0 COMMENT '今日复活次数',
  `today_date` varchar(10) DEFAULT '' COMMENT '今日日期',
  `week_revives` int(11) DEFAULT 0 COMMENT '本周复活次数',
  `week_start` int(11) DEFAULT 0 COMMENT '本周开始时间戳',
  `total_exp_lost` int(11) DEFAULT 0 COMMENT '累计损失经验',
  `total_lingshi_spent` int(11) DEFAULT 0 COMMENT '累计消耗灵石',
  `total_daojin_spent` int(11) DEFAULT 0 COMMENT '累计消耗道金',
  `revive_item_used` int(11) DEFAULT 0 COMMENT '使用复活丹次数',
  `revive_lingshi_used` int(11) DEFAULT 0 COMMENT '灵石复活次数',
  `revive_hospital_used` int(11) DEFAULT 0 COMMENT '回城复活次数',
  `last_death_time` int(11) DEFAULT 0 COMMENT '上次死亡时间',
  `last_revive_time` int(11) DEFAULT 0 COMMENT '上次复活时间',
  `longest_live_time` int(11) DEFAULT 0 COMMENT '最长存活时间(秒)',
  `shortest_live_time` int(11) DEFAULT 0 COMMENT '最短存活时间(秒)',
  `current_live_time` int(11) DEFAULT 0 COMMENT '当前存活时间(秒)',
  `live_start_time` int(11) DEFAULT 0 COMMENT '本次存活开始时间',
  `total_reversals` int(11) DEFAULT 0 COMMENT '总绝境翻盘次数',
  `reversal_successes` int(11) DEFAULT 0 COMMENT '翻盘成功次数',
  `max_consecutive_deaths` int(11) DEFAULT 0 COMMENT '最大连续死亡次数',
  `current_consecutive_deaths` int(11) DEFAULT 0 COMMENT '当前连续死亡次数',
  `update_time` int(11) DEFAULT 0 COMMENT '更新时间',
  `create_time` int(11) DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_id` (`player_id`),
  KEY `total_deaths` (`total_deaths`),
  KEY `last_revive_time` (`last_revive_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='玩家复活统计表';

-- 3. 更新fa_xt_player表添加复活相关字段
ALTER TABLE `fa_xt_player` 
ADD COLUMN `total_deaths` int(11) DEFAULT 0 COMMENT '总死亡次数' AFTER `experience`,
ADD COLUMN `total_revives` int(11) DEFAULT 0 COMMENT '总复活次数' AFTER `total_deaths`,
ADD COLUMN `today_revives` int(11) DEFAULT 0 COMMENT '今日复活次数' AFTER `total_revives`,
ADD COLUMN `last_death_time` int(11) DEFAULT 0 COMMENT '上次死亡时间' AFTER `today_revives`,
ADD COLUMN `last_revive_time` int(11) DEFAULT 0 COMMENT '上次复活时间' AFTER `last_death_time`;
