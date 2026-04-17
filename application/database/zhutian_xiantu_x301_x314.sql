/*
 诸天仙途 - 数据库扩展脚本 X301-X314
 Date: 2026-03-25
 新增表结构
*/

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for fa_achievement (成就配置)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_achievement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '成就ID',
  `achievement_code` varchar(50) NOT NULL COMMENT '成就代码',
  `name` varchar(100) NOT NULL COMMENT '成就名称',
  `description` varchar(500) DEFAULT '' COMMENT '成就描述',
  `icon` varchar(50) DEFAULT '' COMMENT '图标',
  `category` varchar(20) DEFAULT 'combat' COMMENT '分类:combat/resource/explore/social/special',
  `rarity` varchar(20) DEFAULT 'common' COMMENT '稀有度:common/uncommon/rare/epic/legendary',
  `rarity_order` tinyint(3) unsigned DEFAULT '1' COMMENT '稀有度排序',
  `target` int(10) unsigned DEFAULT '1' COMMENT '目标数量',
  `rewards` text COMMENT '奖励JSON',
  `reward_points` int(10) unsigned DEFAULT '0' COMMENT '成就点数',
  `is_active` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启用',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `achievement_code` (`achievement_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='成就配置';

-- ----------------------------
-- Table structure for fa_player_achievement (玩家成就进度)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_player_achievement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `achievement_id` varchar(50) NOT NULL COMMENT '成就代码',
  `progress` int(10) unsigned DEFAULT '0' COMMENT '当前进度',
  `is_completed` tinyint(1) unsigned DEFAULT '0' COMMENT '是否完成',
  `is_unlocked` tinyint(1) unsigned DEFAULT '1' COMMENT '是否解锁',
  `completed_time` bigint(16) DEFAULT NULL COMMENT '完成时间',
  `reward_claimed` tinyint(1) unsigned DEFAULT '0' COMMENT '奖励是否已领取',
  `reward_points` int(10) unsigned DEFAULT '0' COMMENT '成就点数',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_achievement` (`player_id`,`achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='玩家成就进度';

-- ----------------------------
-- Table structure for fa_market_item (市场商品)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_market_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `seller_id` int(10) unsigned NOT NULL COMMENT '卖家ID',
  `seller_name` varchar(50) DEFAULT '' COMMENT '卖家名称',
  `name` varchar(100) NOT NULL COMMENT '商品名称',
  `item_type` varchar(20) DEFAULT 'equipment' COMMENT '商品类型:equipment/pill/material/pet',
  `rarity` varchar(20) DEFAULT 'common' COMMENT '稀有度',
  `description` varchar(500) DEFAULT '' COMMENT '商品描述',
  `price` int(10) unsigned DEFAULT '0' COMMENT '价格',
  `stock` tinyint(3) unsigned DEFAULT '1' COMMENT '库存',
  `status` varchar(20) DEFAULT 'onsale' COMMENT '状态:onsale/soldout/removed',
  `sales_count` int(10) unsigned DEFAULT '0' COMMENT '销量',
  `properties` text COMMENT '属性JSON',
  `item_id` int(10) unsigned DEFAULT NULL COMMENT '关联物品ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '上架时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='市场商品';

-- ----------------------------
-- Table structure for fa_mail (邮件)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_mail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '邮件ID',
  `sender_id` int(10) unsigned DEFAULT '0' COMMENT '发件人ID',
  `sender_name` varchar(50) DEFAULT '' COMMENT '发件人名称',
  `receiver_id` int(10) unsigned NOT NULL COMMENT '收件人ID',
  `receiver_name` varchar(50) DEFAULT '' COMMENT '收件人名称',
  `title` varchar(100) NOT NULL COMMENT '邮件标题',
  `content` text COMMENT '邮件内容',
  `attachments` text COMMENT '附件JSON',
  `is_system` tinyint(1) unsigned DEFAULT '0' COMMENT '是否系统邮件',
  `is_read` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已读',
  `is_important` tinyint(1) unsigned DEFAULT '0' COMMENT '是否重要',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `read_time` bigint(16) DEFAULT NULL COMMENT '阅读时间',
  `delete_time` bigint(16) DEFAULT NULL COMMENT '删除时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '发送时间',
  PRIMARY KEY (`id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='邮件';

-- ----------------------------
-- Table structure for fa_dungeon_team (副本队伍)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_dungeon_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '队伍ID',
  `dungeon_id` varchar(20) NOT NULL COMMENT '副本ID',
  `difficulty` varchar(20) DEFAULT 'normal' COMMENT '难度',
  `leader_id` int(10) unsigned NOT NULL COMMENT '队长ID',
  `members` text COMMENT '成员JSON',
  `member_count` tinyint(3) unsigned DEFAULT '1' COMMENT '成员数量',
  `status` varchar(20) DEFAULT 'waiting' COMMENT '状态:waiting/ongoing/finished',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `leader_id` (`leader_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='副本队伍';

-- ----------------------------
-- Table structure for fa_dungeon_battle (副本战斗记录)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_dungeon_battle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '战斗ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `dungeon_id` varchar(20) NOT NULL COMMENT '副本ID',
  `difficulty` varchar(20) DEFAULT 'normal' COMMENT '难度',
  `status` varchar(20) DEFAULT 'ongoing' COMMENT '状态:ongoing/victory/defeat/surrender',
  `boss_health` int(10) unsigned DEFAULT '0' COMMENT 'Boss剩余血量',
  `max_boss_health` int(10) unsigned DEFAULT '0' COMMENT 'Boss总血量',
  `damage_dealt` int(10) unsigned DEFAULT '0' COMMENT '造成伤害',
  `damage_taken` int(10) unsigned DEFAULT '0' COMMENT '受到伤害',
  `rewards` text COMMENT '奖励JSON',
  `start_time` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `end_time` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='副本战斗记录';

-- ----------------------------
-- Table structure for fa_guild (宗门)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_guild` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '宗门ID',
  `name` varchar(50) NOT NULL COMMENT '宗门名称',
  `icon` varchar(50) DEFAULT '🏯' COMMENT '宗门图标',
  `leader_id` int(10) unsigned NOT NULL COMMENT '宗主ID',
  `leader_name` varchar(50) DEFAULT '' COMMENT '宗主名称',
  `level` tinyint(3) unsigned DEFAULT '1' COMMENT '宗门等级',
  `member_count` tinyint(3) unsigned DEFAULT '1' COMMENT '成员数量',
  `max_members` tinyint(3) unsigned DEFAULT '50' COMMENT '最大成员',
  `total_combat` int(10) unsigned DEFAULT '0' COMMENT '总战力',
  `rank` int(10) unsigned DEFAULT '0' COMMENT '排名',
  `funds` int(10) unsigned DEFAULT '0' COMMENT '宗门资金',
  `weekly_win` int(10) unsigned DEFAULT '0' COMMENT '本周胜场',
  `weekly_lose` int(10) unsigned DEFAULT '0' COMMENT '本周负场',
  `total_win` int(10) unsigned DEFAULT '0' COMMENT '总胜场',
  `total_lose` int(10) unsigned DEFAULT '0' COMMENT '总负场',
  `season_rank` int(10) unsigned DEFAULT '0' COMMENT '赛季排名',
  `season_points` int(10) unsigned DEFAULT '0' COMMENT '赛季积分',
  `claimed_rewards` text COMMENT '已领取奖励JSON',
  `status` varchar(20) DEFAULT 'normal' COMMENT '状态:normal/dissolved',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='宗门';

-- ----------------------------
-- Table structure for fa_guild_member (宗门成员)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_guild_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `guild_id` int(10) unsigned NOT NULL COMMENT '宗门ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `player_name` varchar(50) DEFAULT '' COMMENT '玩家名称',
  `realm_level` tinyint(3) unsigned DEFAULT '1' COMMENT '境界等级',
  `realm_name` varchar(50) DEFAULT '' COMMENT '境界名称',
  `combat` int(10) unsigned DEFAULT '0' COMMENT '战力',
  `position` varchar(20) DEFAULT 'member' COMMENT '职位:leader/elder/member',
  `contribution` int(10) unsigned DEFAULT '0' COMMENT '贡献度',
  `join_time` bigint(16) DEFAULT NULL COMMENT '加入时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_guild` (`guild_id`,`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='宗门成员';

-- ----------------------------
-- Table structure for fa_guild_war (宗门战)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_guild_war` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '战斗ID',
  `war_type` varchar(20) DEFAULT 'guild' COMMENT '战斗类型:guild/territory',
  `attacker_id` int(10) unsigned NOT NULL COMMENT '进攻方宗门ID',
  `attacker_name` varchar(50) DEFAULT '' COMMENT '进攻方名称',
  `attacker_icon` varchar(50) DEFAULT '' COMMENT '进攻方图标',
  `attacker_score` int(10) unsigned DEFAULT '0' COMMENT '进攻方得分',
  `attacker_members` text COMMENT '进攻方参战成员JSON',
  `defender_id` int(10) unsigned NOT NULL COMMENT '防守方宗门ID',
  `defender_name` varchar(50) DEFAULT '' COMMENT '防守方名称',
  `defender_icon` varchar(50) DEFAULT '' COMMENT '防守方图标',
  `defender_score` int(10) unsigned DEFAULT '0' COMMENT '防守方得分',
  `defender_members` text COMMENT '防守方参战成员JSON',
  `winner_id` int(10) unsigned DEFAULT '0' COMMENT '胜利方ID',
  `status` varchar(20) DEFAULT 'preparing' COMMENT '状态:preparing/ongoing/ended',
  `progress` tinyint(3) unsigned DEFAULT '0' COMMENT '战斗进度',
  `remaining_time` int(10) unsigned DEFAULT '0' COMMENT '剩余时间(秒)',
  `scheduled_time` bigint(16) DEFAULT NULL COMMENT '计划时间',
  `start_time` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `end_time` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='宗门战';

-- ----------------------------
-- Table structure for fa_guild_defense (宗门防守布置)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_guild_defense` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `guild_id` int(10) unsigned NOT NULL COMMENT '宗门ID',
  `position` tinyint(3) unsigned DEFAULT '0' COMMENT '位置(0-8)',
  `member_id` int(10) unsigned DEFAULT '0' COMMENT '成员ID',
  `defense_type` varchar(20) DEFAULT 'defense' COMMENT '防守类型:attack/defense/support',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `guild_position` (`guild_id`,`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='宗门防守布置';

-- ----------------------------
-- Table structure for fa_season (赛季)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_season` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '赛季ID',
  `name` varchar(50) NOT NULL COMMENT '赛季名称',
  `number` tinyint(3) unsigned DEFAULT '1' COMMENT '赛季编号',
  `start_time` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `end_time` bigint(16) DEFAULT NULL COMMENT '结束时间',
  `status` varchar(20) DEFAULT 'active' COMMENT '状态:active/ended',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='赛季';

-- ----------------------------
-- Table structure for fa_title (称号)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '称号ID',
  `title_code` varchar(50) NOT NULL COMMENT '称号代码',
  `name` varchar(50) NOT NULL COMMENT '称号名称',
  `description` varchar(200) DEFAULT '' COMMENT '称号描述',
  `icon` varchar(50) DEFAULT '' COMMENT '称号图标',
  `rarity` varchar(20) DEFAULT 'common' COMMENT '稀有度',
  `source` varchar(20) DEFAULT 'achievement' COMMENT '来源:achievement/battle/reputation',
  `source_id` varchar(50) DEFAULT '' COMMENT '来源ID',
  `attributes` text COMMENT '属性加成JSON',
  `is_active` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启用',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_code` (`title_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='称号';

-- ----------------------------
-- Table structure for fa_player_title (玩家称号)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `fa_player_title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `title_id` int(10) unsigned NOT NULL COMMENT '称号ID',
  `is_equipped` tinyint(1) unsigned DEFAULT '0' COMMENT '是否装备',
  `acquire_time` bigint(16) DEFAULT NULL COMMENT '获得时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_title` (`player_id`,`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='玩家称号';

-- ----------------------------
-- Records for fa_achievement
-- ----------------------------
BEGIN;
INSERT INTO `fa_achievement` VALUES 
(1, 'ach_001', '初试锋芒', '赢得第一场战斗', '⚔️', 'combat', 'common', 1, 1, '[{\"type\":\"daojin\",\"amount\":100}]', 10, 1, 1742870400, 1742870400),
(2, 'ach_002', '连胜勇士', '连续赢得3场战斗', '🔥', 'combat', 'uncommon', 2, 3, '[{\"type\":\"daojin\",\"amount\":300}]', 25, 1, 1742870400, 1742870400),
(3, 'ach_003', '百战百胜', '赢得100场战斗', '🏅', 'combat', 'rare', 3, 100, '[{\"type\":\"daojin\",\"amount\":5000}]', 100, 1, 1742870400, 1742870400),
(4, 'ach_004', '战无不胜', '赢得1000场战斗', '👑', 'combat', 'epic', 4, 1000, '[{\"type\":\"daojin\",\"amount\":50000}]', 500, 1, 1742870400, 1742870400),
(5, 'ach_005', '小有家底', '累计拥有10000灵石', '💰', 'resource', 'common', 1, 10000, '[{\"type\":\"lingshi\",\"amount\":1000}]', 10, 1, 1742870400, 1742870400),
(6, 'ach_006', '富甲一方', '累计拥有100000灵石', '💎', 'resource', 'uncommon', 2, 100000, '[{\"type\":\"lingshi\",\"amount\":10000}]', 50, 1, 1742870400, 1742870400),
(7, 'ach_007', '腰缠万贯', '累计拥有1000000灵石', '🤑', 'resource', 'rare', 3, 1000000, '[{\"type\":\"lingshi\",\"amount\":100000}]', 200, 1, 1742870400, 1742870400),
(8, 'ach_008', '初出茅庐', '探索地图100次', '🗺️', 'explore', 'common', 1, 100, '[{\"type\":\"exp\",\"amount\":1000}]', 10, 1, 1742870400, 1742870400),
(9, 'ach_009', '足迹遍布', '探索地图1000次', '🌍', 'explore', 'uncommon', 2, 1000, '[{\"type\":\"exp\",\"amount\":10000}]', 50, 1, 1742870400, 1742870400),
(10, 'ach_010', '踏遍山河', '探索地图10000次', '⭐', 'explore', 'rare', 3, 10000, '[{\"type\":\"exp\",\"amount\":100000}]', 200, 1, 1742870400, 1742870400),
(11, 'ach_011', '初入江湖', '添加10个好友', '🤝', 'social', 'common', 1, 10, '[{\"type\":\"reputation\",\"amount\":100}]', 10, 1, 1742870400, 1742870400),
(12, 'ach_012', '广结善缘', '添加100个好友', '👥', 'social', 'uncommon', 2, 100, '[{\"type\":\"reputation\",\"amount\":1000}]', 50, 1, 1742870400, 1742870400),
(13, 'ach_013', '天选之人', '首次突破境界', '🌟', 'special', 'rare', 3, 1, '[{\"type\":\"title\",\"name\":\"天选之人\",\"id\":1}]', 100, 1, 1742870400, 1742870400),
(14, 'ach_014', '道心坚定', '完成初心碑问答', '💫', 'special', 'uncommon', 2, 1, '[{\"type\":\"reputation\",\"amount\":500}]', 30, 1, 1742870400, 1742870400),
(15, 'ach_015', '传说开始', '达到筑基期', '✨', 'special', 'legendary', 5, 1, '[{\"type\":\"title\",\"name\":\"筑基修士\",\"id\":2}]', 300, 1, 1742870400, 1742870400);
COMMIT;

-- ----------------------------
-- Records for fa_season
-- ----------------------------
BEGIN;
INSERT INTO `fa_season` VALUES 
(1, '第一赛季', 1, 1743465600, 1746057600, 'active', 1742870400);
COMMIT;

-- ----------------------------
-- Records for fa_title
-- ----------------------------
BEGIN;
INSERT INTO `fa_title` VALUES 
(1, '天选之人', '天选之人', '完成首次境界突破', '🌟', 'rare', 'achievement', 'ach_013', '{}', 1, 1742870400),
(2, '筑基修士', '筑基修士', '达到筑基期', '✨', 'rare', 'achievement', 'ach_015', '{}', 1, 1742870400),
(3, '战神', '战神', '累计赢得100场宗门战', '⚔️', 'epic', 'achievement', 'ach_100', '{}', 1, 1742870400);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
