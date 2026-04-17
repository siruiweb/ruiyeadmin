/*
 诸天仙途 - 数据库表结构
 Date: 2026-03-25
*/

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for fa_player (玩家表)
-- ----------------------------
CREATE TABLE `fa_player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '玩家ID',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '用户ID',
  `player_name` varchar(50) DEFAULT '' COMMENT '角色名',
  `avatar` varchar(255) DEFAULT '/assets/img/avatar.png' COMMENT '头像',
  `identity_id` varchar(20) DEFAULT 'sanxiu' COMMENT '身份类型:sanxiu=散修,duoshe=夺舍',
  `identity_name` varchar(50) DEFAULT '散修' COMMENT '身份名称',
  `realm_level` tinyint(3) unsigned DEFAULT '1' COMMENT '境界等级',
  `realm_name` varchar(50) DEFAULT '练体期' COMMENT '境界名称',
  `exp` int(10) unsigned DEFAULT '0' COMMENT '经验值',
  `age` tinyint(3) unsigned DEFAULT '16' COMMENT '当前年龄',
  `max_age` smallint(5) unsigned DEFAULT '60' COMMENT '最大寿命',
  `health` smallint(5) unsigned DEFAULT '100' COMMENT '生命值',
  `max_health` smallint(5) unsigned DEFAULT '100' COMMENT '最大生命值',
  `spirit` smallint(5) unsigned DEFAULT '100' COMMENT '灵力值',
  `max_spirit` smallint(5) unsigned DEFAULT '100' COMMENT '最大灵力值',
  `attack` smallint(5) unsigned DEFAULT '10' COMMENT '攻击力',
  `defense` smallint(5) unsigned DEFAULT '5' COMMENT '防御力',
  `speed` smallint(5) unsigned DEFAULT '10' COMMENT '速度',
  `lingshi` int(10) unsigned DEFAULT '100' COMMENT '灵石',
  `daojin` int(10) unsigned DEFAULT '0' COMMENT '道行',
  `linggen_type` varchar(20) DEFAULT 'mixed' COMMENT '灵根类型:metal/mixed/wood/water/fire/earth',
  `linggen_name` varchar(50) DEFAULT '杂灵根' COMMENT '灵根名称',
  `tizhi_type` varchar(20) DEFAULT 'normal' COMMENT '体质类型',
  `tizhi_name` varchar(50) DEFAULT '普通体质' COMMENT '体质名称',
  `equipped_skills` text COMMENT '已装备功法JSON',
  `inventory` text COMMENT '背包数据JSON',
  `realm_slots` text COMMENT '境界卡槽JSON',
  `current_story_id` int(10) unsigned DEFAULT '1' COMMENT '当前剧情ID',
  `completed_tasks` text COMMENT '已完成任务JSON',
  `npc_interactions` text COMMENT 'NPC交互记录JSON',
  `last_update_time` bigint(16) DEFAULT NULL COMMENT '最后更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `player_name` (`player_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='玩家表';

-- ----------------------------
-- Table structure for fa_tiandao_question (天道问答题库)
-- ----------------------------
CREATE TABLE `fa_tiandao_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '题目ID',
  `question_text` varchar(500) NOT NULL COMMENT '题目内容',
  `options` text NOT NULL COMMENT '选项JSON',
  `answer` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '正确答案索引',
  `difficulty` tinyint(3) unsigned DEFAULT '1' COMMENT '难度等级:1-5',
  `category` varchar(50) DEFAULT 'general' COMMENT '题目分类',
  `times_answered` int(10) unsigned DEFAULT '0' COMMENT '回答次数',
  `times_correct` int(10) unsigned DEFAULT '0' COMMENT '正确次数',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='天道问答题库';

-- ----------------------------
-- Table structure for fa_tiandao_record (天道问答记录)
-- ----------------------------
CREATE TABLE `fa_tiandao_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '题目ID',
  `answer_index` tinyint(3) unsigned DEFAULT '0' COMMENT '回答索引',
  `is_correct` tinyint(1) unsigned DEFAULT '0' COMMENT '是否正确',
  `reward_daojin` int(10) unsigned DEFAULT '0' COMMENT '道行奖励',
  `reward_exp` int(10) unsigned DEFAULT '0' COMMENT '经验奖励',
  `createtime` bigint(16) DEFAULT NULL COMMENT '回答时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='天道问答记录';

-- ----------------------------
-- Table structure for fa_initial_answer (初心回答)
-- ----------------------------
CREATE TABLE `fa_initial_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '回答ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `question_id` tinyint(3) unsigned NOT NULL COMMENT '题目ID',
  `answer_text` varchar(1000) NOT NULL COMMENT '回答内容',
  `createtime` bigint(16) DEFAULT NULL COMMENT '回答时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='初心回答';

-- ----------------------------
-- Table structure for fa_initial_inscription (初心碑铭文)
-- ----------------------------
CREATE TABLE `fa_initial_inscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '铭文ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `player_name` varchar(50) DEFAULT '' COMMENT '玩家名称',
  `player_avatar` varchar(255) DEFAULT '' COMMENT '玩家头像',
  `realm_name` varchar(50) DEFAULT '' COMMENT '境界名称',
  `inscription` varchar(500) NOT NULL COMMENT '铭文内容',
  `likes` int(10) unsigned DEFAULT '0' COMMENT '点赞数',
  `createtime` bigint(16) DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='初心碑铭文';

-- ----------------------------
-- Table structure for fa_loot (尸体搜索记录)
-- ----------------------------
CREATE TABLE `fa_loot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `corpse_name` varchar(100) DEFAULT '' COMMENT '尸体名称',
  `corpse_level` tinyint(3) unsigned DEFAULT '1' COMMENT '尸体等级',
  `found_items` text COMMENT '获得物品JSON',
  `daojin_reward` int(10) unsigned DEFAULT '0' COMMENT '道行奖励',
  `lingshi_reward` int(10) unsigned DEFAULT '0' COMMENT '灵石奖励',
  `createtime` bigint(16) DEFAULT NULL COMMENT '搜索时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='尸体搜索记录';

-- ----------------------------
-- Table structure for fa_revive (复活记录)
-- ----------------------------
CREATE TABLE `fa_revive` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `death_reason` varchar(200) DEFAULT '' COMMENT '死亡原因',
  `death_time` bigint(16) DEFAULT NULL COMMENT '死亡时间',
  `revive_type` varchar(20) DEFAULT 'normal' COMMENT '复活类型:normal=普通,lingshi=灵石,item=道具',
  `revive_cost` int(10) unsigned DEFAULT '0' COMMENT '复活消耗',
  `revive_time` bigint(16) DEFAULT NULL COMMENT '复活时间',
  `exp_loss` int(10) unsigned DEFAULT '0' COMMENT '经验损失',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='复活记录';

-- ----------------------------
-- Table structure for fa_battle (战斗记录)
-- ----------------------------
CREATE TABLE `fa_battle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '战斗ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `battle_type` varchar(20) DEFAULT 'pve' COMMENT '战斗类型:pve/pvp/boss',
  `enemy_id` varchar(50) DEFAULT '' COMMENT '敌人ID',
  `enemy_name` varchar(100) DEFAULT '' COMMENT '敌人名称',
  `enemy_level` tinyint(3) unsigned DEFAULT '1' COMMENT '敌人等级',
  `result` varchar(20) DEFAULT 'lose' COMMENT '战斗结果:win/lose/draw',
  `damage_dealt` int(10) unsigned DEFAULT '0' COMMENT '造成伤害',
  `damage_taken` int(10) unsigned DEFAULT '0' COMMENT '受到伤害',
  `reward_exp` int(10) unsigned DEFAULT '0' COMMENT '经验奖励',
  `reward_lingshi` int(10) unsigned DEFAULT '0' COMMENT '灵石奖励',
  `is_critical` tinyint(1) unsigned DEFAULT '0' COMMENT '是否暴击',
  `duration` int(10) unsigned DEFAULT '0' COMMENT '战斗时长(秒)',
  `createtime` bigint(16) DEFAULT NULL COMMENT '战斗时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='战斗记录';

-- ----------------------------
-- Table structure for fa_skill (功法表)
-- ----------------------------
CREATE TABLE `fa_skill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '功法ID',
  `skill_code` varchar(50) NOT NULL COMMENT '功法代码',
  `skill_name` varchar(100) NOT NULL COMMENT '功法名称',
  `skill_type` varchar(20) DEFAULT 'attack' COMMENT '功法类型:attack/defense/buff/heal/passive',
  `skill_level` tinyint(3) unsigned DEFAULT '1' COMMENT '功法等级',
  `realm_required` tinyint(3) unsigned DEFAULT '1' COMMENT '所需境界',
  `description` varchar(500) DEFAULT '' COMMENT '功法描述',
  `effects` text COMMENT '功法效果JSON',
  `icon` varchar(255) DEFAULT '' COMMENT '图标',
  `rarity` varchar(20) DEFAULT 'common' COMMENT '稀有度:common/uncommon/rare/epic/legendary',
  `is_equipped` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已装备',
  `player_id` int(10) unsigned DEFAULT NULL COMMENT '拥有玩家ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '获得时间',
  PRIMARY KEY (`id`),
  KEY `skill_code` (`skill_code`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='功法表';

-- ----------------------------
-- Table structure for fa_task (任务表)
-- ----------------------------
CREATE TABLE `fa_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `task_code` varchar(50) NOT NULL COMMENT '任务代码',
  `task_name` varchar(100) NOT NULL COMMENT '任务名称',
  `task_type` varchar(20) DEFAULT 'story' COMMENT '任务类型:story/daily/weekly/achievement',
  `task_desc` varchar(500) DEFAULT '' COMMENT '任务描述',
  `task_status` varchar(20) DEFAULT 'pending' COMMENT '任务状态:pending/in_progress/completed/failed',
  `progress` tinyint(3) unsigned DEFAULT '0' COMMENT '任务进度',
  `target` tinyint(3) unsigned DEFAULT '1' COMMENT '目标数量',
  `reward` text COMMENT '奖励JSON',
  `deadline` bigint(16) DEFAULT NULL COMMENT '截止时间',
  `completed_time` bigint(16) DEFAULT NULL COMMENT '完成时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '接取时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `task_code` (`task_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='任务表';

-- ----------------------------
-- Table structure for fa_story (剧情进度)
-- ----------------------------
CREATE TABLE `fa_story` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '进度ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `chapter_id` tinyint(3) unsigned NOT NULL COMMENT '章节ID',
  `chapter_name` varchar(100) DEFAULT '' COMMENT '章节名称',
  `stage_id` tinyint(3) unsigned DEFAULT '1' COMMENT '阶段ID',
  `stage_name` varchar(100) DEFAULT '' COMMENT '阶段名称',
  `choices_made` text COMMENT '已做选择JSON',
  `npc_relations` text COMMENT 'NPC关系JSON',
  `status` varchar(20) DEFAULT 'in_progress' COMMENT '状态:in_progress/completed',
  `createtime` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='剧情进度';

-- ----------------------------
-- Table structure for fa_chat_message (聊天消息)
-- ----------------------------
CREATE TABLE `fa_chat_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '发送者ID',
  `player_name` varchar(50) DEFAULT '' COMMENT '发送者名称',
  `channel` varchar(20) DEFAULT 'world' COMMENT '频道:world/guild/private',
  `receiver_id` int(10) unsigned DEFAULT NULL COMMENT '接收者ID(私聊)',
  `content` varchar(500) NOT NULL COMMENT '消息内容',
  `message_type` varchar(20) DEFAULT 'text' COMMENT '消息类型:text/image/system',
  `createtime` bigint(16) DEFAULT NULL COMMENT '发送时间',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `channel` (`channel`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='聊天消息';

-- ----------------------------
-- Table structure for fa_ranking (排行榜)
-- ----------------------------
CREATE TABLE `fa_ranking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '排名ID',
  `player_id` int(10) unsigned NOT NULL COMMENT '玩家ID',
  `player_name` varchar(50) DEFAULT '' COMMENT '玩家名称',
  `player_avatar` varchar(255) DEFAULT '' COMMENT '玩家头像',
  `rank_type` varchar(20) DEFAULT 'daojin' COMMENT '排名类型:daojin/level/battle/win',
  `rank_value` int(10) unsigned DEFAULT '0' COMMENT '排名数值',
  `rank_position` int(10) unsigned DEFAULT '0' COMMENT '排名位置',
  `update_time` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_rank` (`player_id`,`rank_type`),
  KEY `rank_position` (`rank_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='排行榜';

-- ----------------------------
-- Table structure for fa_server (服务器状态)
-- ----------------------------
CREATE TABLE `fa_server` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '服务器ID',
  `server_name` varchar(100) DEFAULT '' COMMENT '服务器名称',
  `server_ip` varchar(50) DEFAULT '' COMMENT '服务器IP',
  `server_port` int(10) unsigned DEFAULT '8080' COMMENT '服务器端口',
  `status` varchar(20) DEFAULT 'online' COMMENT '状态:online/offline/maintenance',
  `online_players` int(10) unsigned DEFAULT '0' COMMENT '在线人数',
  `max_players` int(10) unsigned DEFAULT '1000' COMMENT '最大人数',
  `maintenance_start` bigint(16) DEFAULT NULL COMMENT '维护开始时间',
  `maintenance_end` bigint(16) DEFAULT NULL COMMENT '维护结束时间',
  `announcement` text COMMENT '公告内容',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='服务器状态';

-- ----------------------------
-- Records for fa_tiandao_question
-- ----------------------------
BEGIN;
INSERT INTO `fa_tiandao_question` VALUES 
(1, '修仙之根本在于？', '["追求力量","逆天改命","顺应天道","长生不老"]', 2, 1, 'basic', 0, 0, 1742870400),
(2, '以下哪个不是修仙所需的？', '["灵根","灵气","功法","金钱"]', 3, 1, 'basic', 0, 0, 1742870400),
(3, '筑基期修士最主要的修炼方式是？', '["服用丹药","打坐修炼","战斗历练","沉睡"]', 1, 2, 'cultivation', 0, 0, 1742870400),
(4, '灵根的属性包括？', '["金木水火土","天地人和","日月星","风雷雨电"]', 0, 1, 'basic', 0, 0, 1742870400),
(5, '修仙者突破境界需要？', '["大量灵石","感悟天道","战斗经验","灵丹妙药"]', 1, 2, 'breakthrough', 0, 0, 1742870400),
(6, '以下哪个是修仙大忌？', '["修炼","杀戮","炼丹","阵法"]', 1, 3, 'danger', 0, 0, 1742870400),
(7, '修仙界最看重的是什么？', '["实力","资质","气运","资源"]', 1, 2, 'core', 0, 0, 1742870400),
(8, '灵宠对修仙者的帮助是？', '["增加战力","探测敌情","代步工具","以上都是"]', 3, 2, 'pet', 0, 0, 1742870400),
(9, '金丹期修士的标志是？', '["结成金丹","开辟气海","凝聚神识","炼化法宝"]', 0, 3, 'realm', 0, 0, 1742870400),
(10, '以下哪种功法最稀有？', '["普通功法","进阶功法","镇派绝学","天阶秘典"]', 3, 4, 'skill', 0, 0, 1742870400);
COMMIT;

-- ----------------------------
-- Records for fa_server
-- ----------------------------
BEGIN;
INSERT INTO `fa_server` VALUES 
(1, '诸天仙途·一区', 'zhutian01.shengame.net', 8080, 'online', 0, 1000, NULL, NULL, '欢迎来到诸天仙途！修仙路漫漫，道心永不改。', 1742870400, 1742870400),
(2, '诸天仙途·二区', 'zhutian02.shengame.net', 8080, 'online', 0, 1000, NULL, NULL, '道法自然，仙途无尽。', 1742870400, 1742870400);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
