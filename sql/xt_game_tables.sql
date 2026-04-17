-- =========================================
-- 诸天仙途 游戏后台管理表结构
-- 数据库: zhutianxiantu
-- 表前缀: fa_xt_
-- =========================================

-- 1. 玩家管理表
DROP TABLE IF EXISTS `fa_xt_player`;
CREATE TABLE `fa_xt_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` varchar(64) NOT NULL COMMENT '玩家ID',
  `username` varchar(64) NOT NULL COMMENT '玩家名称',
  `level` int(11) DEFAULT 1 COMMENT '等级',
  `experience` int(11) DEFAULT 0 COMMENT '经验值',
  `spirit` int(11) DEFAULT 0 COMMENT '灵力',
  `gold` int(11) DEFAULT 0 COMMENT '金币',
  `diamond` int(11) DEFAULT 0 COMMENT '钻石',
  `stamina` int(11) DEFAULT 100 COMMENT '体力',
  `identity_id` int(11) DEFAULT 0 COMMENT '身份ID',
  `skill_ids` varchar(512) DEFAULT '' COMMENT '功法IDs',
  `formation` varchar(128) DEFAULT '' COMMENT '阵法',
  `achievement` text COMMENT '成就JSON',
  `status` enum('normal','banned') DEFAULT 'normal' COMMENT '状态',
  `last_login_time` int(11) DEFAULT 0 COMMENT '最后登录时间',
  `last_login_ip` varchar(64) DEFAULT '' COMMENT '最后登录IP',
  `create_time` int(11) DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_id` (`player_id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='玩家管理表';

-- 2. 天道题目表
DROP TABLE IF EXISTS `fa_xt_tiandao`;
CREATE TABLE `fa_xt_tiandao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(32) DEFAULT 'default' COMMENT '题目分类',
  `question` text NOT NULL COMMENT '题目内容',
  `options` text COMMENT '选项JSON',
  `answer` varchar(8) NOT NULL COMMENT '正确答案',
  `difficulty` tinyint(1) DEFAULT 1 COMMENT '难度1-5',
  `score` int(11) DEFAULT 10 COMMENT '分值',
  `explanation` text COMMENT '解析',
  `sort` int(11) DEFAULT 0 COMMENT '排序',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `difficulty` (`difficulty`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='天道题目表';

-- 3. 初心碑表
DROP TABLE IF EXISTS `fa_xt_initial`;
CREATE TABLE `fa_xt_initial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL COMMENT '玩家ID',
  `player_name` varchar(64) DEFAULT '' COMMENT '玩家名称',
  `content` text NOT NULL COMMENT '初心内容',
  `word_count` int(11) DEFAULT 0 COMMENT '字数',
  `likes` int(11) DEFAULT 0 COMMENT '点赞数',
  `is_top` tinyint(1) DEFAULT 0 COMMENT '是否置顶',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `likes` (`likes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='初心碑表';

-- 4. 身份管理表
DROP TABLE IF EXISTS `fa_xt_identity`;
CREATE TABLE `fa_xt_identity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '身份名称',
  `level` int(11) DEFAULT 1 COMMENT '等级',
  `description` text COMMENT '描述',
  `icon` varchar(255) DEFAULT '' COMMENT '图标',
  `privilege` text COMMENT '特权JSON',
  `requirement` text COMMENT '解锁条件',
  `sort` int(11) DEFAULT 0 COMMENT '排序',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='身份管理表';

-- 5. 功法管理表
DROP TABLE IF EXISTS `fa_xt_skill`;
CREATE TABLE `fa_xt_skill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '功法名称',
  `type` varchar(32) DEFAULT 'default' COMMENT '功法类型',
  `level` int(11) DEFAULT 1 COMMENT '等级',
  `description` text COMMENT '功法描述',
  `icon` varchar(255) DEFAULT '' COMMENT '图标',
  `effect` text COMMENT '效果JSON',
  `cost` text COMMENT '消耗资源',
  `cooldown` int(11) DEFAULT 0 COMMENT '冷却时间(秒)',
  `skill_type` enum('active','passive') DEFAULT 'active' COMMENT '主动/被动',
  `requirement` text COMMENT '学习条件',
  `sort` int(11) DEFAULT 0 COMMENT '排序',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='功法管理表';

-- 6. 商城管理表
DROP TABLE IF EXISTS `fa_xt_shop`;
CREATE TABLE `fa_xt_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '商品名称',
  `type` varchar(32) DEFAULT 'item' COMMENT '商品类型',
  `price` int(11) DEFAULT 0 COMMENT '价格',
  `currency` enum('gold','diamond','rmb') DEFAULT 'gold' COMMENT '货币类型',
  `icon` varchar(255) DEFAULT '' COMMENT '图标',
  `description` text COMMENT '描述',
  `content` text COMMENT '商品内容/奖励',
  `stock` int(11) DEFAULT -1 COMMENT '库存(-1无限)',
  `limit_type` enum('none','daily','weekly','monthly') DEFAULT 'none' COMMENT '限购类型',
  `limit_count` int(11) DEFAULT 0 COMMENT '限购数量',
  `sort` int(11) DEFAULT 0 COMMENT '排序',
  `status` enum('normal','hidden','soldout') DEFAULT 'normal' COMMENT '状态',
  `start_time` int(11) DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) DEFAULT 0 COMMENT '结束时间',
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商城管理表';

-- 7. 任务管理表
DROP TABLE IF EXISTS `fa_xt_task`;
CREATE TABLE `fa_xt_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL COMMENT '任务名称',
  `type` varchar(32) DEFAULT 'daily' COMMENT '任务类型',
  `description` text COMMENT '任务描述',
  `requirement` text COMMENT '完成条件JSON',
  `reward` text COMMENT '奖励JSON',
  `target_type` varchar(32) DEFAULT '' COMMENT '目标类型',
  `target_count` int(11) DEFAULT 1 COMMENT '目标数量',
  `expire_time` int(11) DEFAULT 0 COMMENT '过期时间',
  `sort` int(11) DEFAULT 0 COMMENT '排序',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务管理表';

-- 8. 剧情管理表
DROP TABLE IF EXISTS `fa_xt_story`;
CREATE TABLE `fa_xt_story` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chapter` int(11) DEFAULT 1 COMMENT '章节',
  `title` varchar(128) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '剧情内容',
  `choices` text COMMENT '选项JSON',
  `effects` text COMMENT '影响JSON',
  `next_id` int(11) DEFAULT 0 COMMENT '下一剧情ID',
  `requirement` text COMMENT '触发条件',
  `type` enum('main','branch','event') DEFAULT 'main' COMMENT '剧情类型',
  `sort` int(11) DEFAULT 0 COMMENT '排序',
  `status` enum('normal','hidden') DEFAULT 'normal' COMMENT '状态',
  `create_time` int(11) DEFAULT 0,
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `chapter` (`chapter`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='剧情管理表';

-- 9. 排行管理表
DROP TABLE IF EXISTS `fa_xt_ranking`;
CREATE TABLE `fa_xt_ranking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL COMMENT '玩家ID',
  `player_name` varchar(64) DEFAULT '' COMMENT '玩家名称',
  `type` varchar(32) NOT NULL COMMENT '排行类型',
  `score` int(11) DEFAULT 0 COMMENT '分数',
  `rank` int(11) DEFAULT 0 COMMENT '排名',
  `data` text COMMENT '额外数据JSON',
  `period` varchar(32) DEFAULT '' COMMENT '周期',
  `update_time` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_type_period` (`player_id`,`type`,`period`),
  KEY `type_rank` (`type`,`rank`),
  KEY `score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='排行管理表';
