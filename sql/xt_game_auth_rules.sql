-- =========================================
-- 诸天仙途 游戏后台菜单权限配置
-- 数据库: zhutianxiantu
-- 插入到 auth_rule 表
-- =========================================

-- 游戏管理父菜单 (假设pid为0的顶级菜单)
-- 请根据实际情况调整pid值

INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game', '游戏管理', 'fa fa-gamepad', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');

-- 获取刚插入的父菜单ID后，需要手动设置为对应的pid
-- 以下SQL需要将 {parent_id} 替换为实际的父菜单ID

-- 玩家管理
INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game/player', '玩家管理', 'fa fa-user', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/player/index', '查看', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/player/edit', '编辑', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/player/del', '删除', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');

-- 天道题目
INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game/tiandao', '天道题目', 'fa fa-question-circle', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/tiandao/index', '查看', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/tiandao/add', '添加', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/tiandao/edit', '编辑', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/tiandao/del', '删除', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');

-- 初心碑
INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game/initial', '初心碑', 'fa fa-file-text', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/initial/index', '查看', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/initial/edit', '编辑', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/initial/del', '删除', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');

-- 身份管理
INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game/identity', '身份管理', 'fa fa-id-card', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/identity/index', '查看', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/identity/add', '添加', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/identity/edit', '编辑', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/identity/del', '删除', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');

-- 功法管理
INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game/skill', '功法管理', 'fa fa-book', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/skill/index', '查看', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/skill/add', '添加', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/skill/edit', '编辑', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/skill/del', '删除', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');

-- 商城管理
INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game/shop', '商城管理', 'fa fa-shopping-cart', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/shop/index', '查看', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/shop/add', '添加', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/shop/edit', '编辑', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/shop/del', '删除', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');

-- 任务管理
INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game/task', '任务管理', 'fa fa-tasks', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/task/index', '查看', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/task/add', '添加', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/task/edit', '编辑', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/task/del', '删除', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');

-- 剧情管理
INSERT INTO `fa_auth_rule` (`id`, `type`, `pid`, `name`, `title`, `icon`, `condition`, `remark`, `ismenu`, `createtime`, `updatetime`, `weession`, `status`) VALUES
(NULL, 'file', 0, 'game/story', '剧情管理', 'fa fa-book-open', '', '', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/story/index', '查看', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/story/add', '添加', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/story/edit', '编辑', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal'),
(NULL, 'file', 0, 'game/story/del', '删除', '', '', '', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL, 'normal');
