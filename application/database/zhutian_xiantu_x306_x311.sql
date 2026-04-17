/*
 诸天仙途 - X306-X311 战斗系统重构数据库更新
 Date: 2026-03-26
*/

SET FOREIGN_KEY_CHECKS = 0;

-- ========================================
-- X306: 战斗属性扩展 - fa_player表添加战斗相关字段
-- ========================================
ALTER TABLE `fa_player` 
  ADD COLUMN `hp` smallint(5) unsigned DEFAULT '100' COMMENT '气血值' AFTER `age`,
  ADD COLUMN `max_hp` smallint(5) unsigned DEFAULT '100' COMMENT '最大气血值' AFTER `hp`,
  ADD COLUMN `spirit_power` smallint(5) unsigned DEFAULT '10' COMMENT '灵气值' AFTER `max_spirit`,
  ADD COLUMN `max_sp` smallint(5) unsigned DEFAULT '10' COMMENT '最大灵气值' AFTER `spirit_power`,
  ADD COLUMN `crit_rate` decimal(4,2) unsigned DEFAULT '5.00' COMMENT '暴击率(%)' AFTER `speed`,
  ADD COLUMN `dodge_rate` decimal(4,2) unsigned DEFAULT '5.00' COMMENT '闪避率(%)' AFTER `crit_rate`,
  ADD COLUMN `realm` varchar(50) DEFAULT '练体期' COMMENT '境界名称' AFTER `realm_name`,
  ADD COLUMN `realm_order` tinyint(3) unsigned DEFAULT '1' COMMENT '境界等级顺序' AFTER `realm`,
  MODIFY COLUMN `spirit` smallint(5) unsigned DEFAULT '100' COMMENT '灵力值(旧字段兼容)' AFTER `max_health`,
  MODIFY COLUMN `max_spirit` smallint(5) unsigned DEFAULT '100' COMMENT '最大灵力值(旧字段兼容)' AFTER `spirit`;

-- ========================================
-- X307: 境界数据表
-- ========================================
CREATE TABLE IF NOT EXISTS `fa_realm` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '境界ID',
  `realm_order` tinyint(3) unsigned NOT NULL COMMENT '境界等级',
  `realm_name` varchar(50) NOT NULL COMMENT '境界名称',
  `realm_desc` varchar(200) DEFAULT '' COMMENT '境界描述',
  `hp_bonus` smallint(5) unsigned DEFAULT '0' COMMENT '气血加成',
  `attack_bonus` smallint(5) unsigned DEFAULT '0' COMMENT '攻击加成',
  `defense_bonus` smallint(5) unsigned DEFAULT '0' COMMENT '防御加成',
  `spirit_bonus` smallint(5) unsigned DEFAULT '0' COMMENT '灵气加成',
  `suppression_factor` decimal(3,2) unsigned DEFAULT '1.00' COMMENT '对低境界压制系数',
  `exp_required` int(10) unsigned DEFAULT '0' COMMENT '突破所需经验',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='境界表';

-- 插入境界数据
BEGIN;
INSERT INTO `fa_realm` VALUES 
(1, 1, '练体期', '强身健体，淬炼肉身', 0, 0, 0, 0, 1.00, 0),
(2, 2, '练气期', '吸纳灵气，凝聚真气', 20, 5, 2, 5, 1.30, 100),
(3, 3, '筑基期', '筑就道基，丹田中品', 50, 15, 8, 15, 1.50, 500),
(4, 4, '金丹期', '结成金丹，神识初成', 100, 30, 20, 30, 2.00, 2000),
(5, 5, '元婴期', '元婴出窍，神游太虚', 200, 60, 40, 60, 2.50, 8000),
(6, 6, '化神期', '化虚为实，掌控法则', 400, 100, 80, 100, 3.00, 30000),
(7, 7, '大乘期', '大乘圆满，道法自然', 800, 180, 150, 180, 3.50, 100000),
(8, 8, '渡劫期', '渡劫飞升，羽化登仙', 1500, 300, 250, 300, 4.00, 999999),
(9, 9, '真仙境', '位列真仙，长生不死', 3000, 500, 400, 500, 4.50, 999999),
(10, 10, '金仙境', '金仙不朽，万劫不灭', 5000, 800, 600, 800, 5.00, 999999);
COMMIT;

-- ========================================
-- X306/X307: 战斗状态表 - 存储实时战斗数据
-- ========================================
CREATE TABLE IF NOT EXISTS `fa_battle_stats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `battle_id` varchar(50) DEFAULT '' COMMENT '战斗ID',
  `hp` smallint(5) unsigned DEFAULT '100' COMMENT '当前气血',
  `max_hp` smallint(5) unsigned DEFAULT '100' COMMENT '最大气血',
  `spirit_power` smallint(5) unsigned DEFAULT '10' COMMENT '当前灵气',
  `max_sp` smallint(5) unsigned DEFAULT '10' COMMENT '最大灵气',
  `attack` smallint(5) unsigned DEFAULT '10' COMMENT '攻击力',
  `defense` smallint(5) unsigned DEFAULT '5' COMMENT '防御力',
  `speed` smallint(5) unsigned DEFAULT '10' COMMENT '速度',
  `realm_order` tinyint(3) unsigned DEFAULT '1' COMMENT '境界等级',
  `realm_name` varchar(50) DEFAULT '练体期' COMMENT '境界名称',
  `status_desc` varchar(100) DEFAULT '状态良好' COMMENT '状态描述',
  `buffs` text COMMENT '增益状态JSON',
  `debuffs` text COMMENT '减益状态JSON',
  `is_desperation` tinyint(1) unsigned DEFAULT '0' COMMENT '是否绝境',
  `update_time` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `battle_id` (`battle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='战斗状态表';

-- ========================================
-- X308: 战斗日志表
-- ========================================
CREATE TABLE IF NOT EXISTS `fa_battle_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `battle_id` varchar(50) DEFAULT '' COMMENT '战斗ID',
  `round_num` tinyint(3) unsigned DEFAULT '1' COMMENT '回合数',
  `log_type` varchar(20) DEFAULT 'action' COMMENT '日志类型:action/damage/heal/system',
  `log_content` varchar(500) NOT NULL COMMENT '日志内容',
  `actor` varchar(20) DEFAULT 'player' COMMENT '执行者:player/enemy/system',
  `createtime` bigint(16) DEFAULT NULL COMMENT '时间戳',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `battle_id` (`battle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='战斗日志表';

-- ========================================
-- X311: 境界压制表
-- ========================================
CREATE TABLE IF NOT EXISTS `fa_realm_suppression` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `attacker_realm` tinyint(3) unsigned NOT NULL COMMENT '攻击方境界等级',
  `defender_realm` tinyint(3) unsigned NOT NULL COMMENT '防御方境界等级',
  `damage_factor` decimal(4,2) unsigned DEFAULT '1.00' COMMENT '伤害系数',
  `can_penetrate` tinyint(1) unsigned DEFAULT '1' COMMENT '是否可以破防',
  `description` varchar(200) DEFAULT '' COMMENT '压制描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `realm_combo` (`attacker_realm`,`defender_realm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='境界压制表';

-- 插入境界压制数据（每差1个境界，伤害-30%或+30%）
BEGIN;
-- 练体期攻击
INSERT INTO `fa_realm_suppression` VALUES 
(1, 1, 1, 1.00, 1, '境界持平'),
(2, 1, 2, 0.50, 1, '【境界劣势】练气期对你有压制'),
(3, 1, 3, 0.20, 0, '【无法破防】筑基期防御太强'),
(4, 1, 4, 0.00, 0, '【绝对碾压】金丹期无视你的攻击'),
-- 练气期攻击
(5, 2, 1, 1.50, 1, '【境界压制】你对练体期有优势'),
(6, 2, 2, 1.00, 1, '境界持平'),
(7, 2, 3, 0.50, 1, '【境界劣势】筑基期压制你'),
(8, 2, 4, 0.20, 0, '【无法破防】金丹期防御太强'),
-- 筑基期攻击
(9, 3, 1, 1.80, 1, '【绝对压制】你对练体期碾压'),
(10, 3, 2, 1.30, 1, '【境界压制】你对练气期有优势'),
(11, 3, 3, 1.00, 1, '境界持平'),
(12, 3, 4, 0.50, 1, '【境界劣势】金丹期压制你'),
(13, 3, 5, 0.20, 0, '【无法破防】元婴期防御太强'),
-- 金丹期攻击
(14, 4, 1, 2.00, 1, '【绝对压制】你对练体期碾压'),
(15, 4, 2, 1.80, 1, '【绝对压制】你对练气期碾压'),
(16, 4, 3, 1.50, 1, '【境界压制】你对筑基期有优势'),
(17, 4, 4, 1.00, 1, '境界持平'),
(18, 4, 5, 0.50, 1, '【境界劣势】元婴期压制你'),
(19, 4, 6, 0.20, 0, '【无法破防】化神期防御太强'),
-- 元婴期攻击
(20, 5, 1, 2.50, 1, '【绝对压制】'),
(21, 5, 2, 2.00, 1, '【绝对压制】'),
(22, 5, 3, 1.80, 1, '【绝对压制】'),
(23, 5, 4, 1.50, 1, '【境界压制】'),
(24, 5, 5, 1.00, 1, '境界持平'),
(25, 5, 6, 0.50, 1, '【境界劣势】');
COMMIT;

-- ========================================
-- 更新现有战斗记录表，添加新字段
-- ========================================
ALTER TABLE `fa_battle` 
  ADD COLUMN `player_hp_before` smallint(5) unsigned DEFAULT '100' COMMENT '战斗前玩家气血' AFTER `duration`,
  ADD COLUMN `player_hp_after` smallint(5) unsigned DEFAULT '0' COMMENT '战斗后玩家气血' AFTER `player_hp_before`,
  ADD COLUMN `player_realm` varchar(50) DEFAULT '' COMMENT '玩家境界' AFTER `player_hp_after`,
  ADD COLUMN `enemy_realm` varchar(50) DEFAULT '' COMMENT '敌人境界' AFTER `player_realm`,
  ADD COLUMN `suppression_factor` decimal(4,2) unsigned DEFAULT '1.00' COMMENT '境界压制系数' AFTER `enemy_realm`,
  ADD COLUMN `is_reversal` tinyint(1) unsigned DEFAULT '0' COMMENT '是否绝境翻盘' AFTER `suppression_factor`,
  ADD COLUMN `battle_logs` text COMMENT '战斗日志JSON' AFTER `is_reversal`;

SET FOREIGN_KEY_CHECKS = 1;
