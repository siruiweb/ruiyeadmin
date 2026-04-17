-- ============================================
-- 脑力王者 - 玩家存档数据表
-- 数据库: brain_game
-- ============================================

-- 创建存档表
CREATE TABLE IF NOT EXISTS `fa_brain_game_save` (
    `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `player_id` INT(11) NOT NULL DEFAULT 0 COMMENT '玩家ID',
    `game_data` TEXT COMMENT '游戏数据JSON',
    `created_at` INT(11) DEFAULT NULL COMMENT '创建时间戳',
    `updated_at` INT(11) DEFAULT NULL COMMENT '更新时间戳',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_player_id` (`player_id`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='玩家存档表';

-- ============================================
-- 执行说明：
-- 1. 确保已选择数据库: USE brain_game;
-- 2. 或在SQL工具中指定数据库后执行
-- ============================================
