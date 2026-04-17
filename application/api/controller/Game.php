<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 游戏数据API
 * 处理玩家数据、年龄增长、境界突破等游戏核心逻辑
 */
class Game extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 玩家数据更新
     */
    public function update()
    {
        $data = $this->request->post('data', '', 'trim');
        if (empty($data)) {
            $this->error('参数不能为空');
        }
        
        $playerData = json_decode($data, true);
        if (!$playerData) {
            $this->error('数据格式错误');
        }
        
        // 这里可以添加数据库保存逻辑
        // $this->savePlayerData($playerData);
        
        $this->success('更新成功', ['timestamp' => time()]);
    }

    /**
     * 保存玩家数据
     */
    public function savePlayer()
    {
        $data = $this->request->post('data', '', 'trim');
        if (empty($data)) {
            $this->error('参数不能为空');
        }
        
        $playerData = json_decode($data, true);
        if (!$playerData) {
            $this->error('数据格式错误');
        }
        
        // 保存逻辑
        $this->success('保存成功', ['timestamp' => time()]);
    }

    /**
     * 加载玩家数据
     */
    public function loadPlayer()
    {
        // 加载逻辑
        $this->success('加载成功', ['data' => null]);
    }

    /**
     * 年龄同步
     * 处理玩家离线时的年龄增长
     */
    public function syncAge()
    {
        $currentAge = $this->request->post('current_age', 16, 'intval');
        $lastUpdateTime = $this->request->post('last_update_time', 0, 'intval');
        $hoursElapsed = $this->request->post('hours_elapsed', 0, 'intval');
        
        // 计算离线收益
        // 正常情况下每小时+1岁
        
        $result = [
            'age' => $currentAge,
            'hours_elapsed' => $hoursElapsed,
            'timestamp' => time()
        ];
        
        $this->success('同步成功', $result);
    }

    /**
     * 境界突破记录
     */
    public function breakthrough()
    {
        $realmLevel = $this->request->post('realm_level', 1, 'intval');
        $realmName = $this->request->post('realm_name', '', 'trim');
        $breakthroughTime = $this->request->post('breakthrough_time', time(), 'intval');
        $reward = $this->request->post('reward', '{}', 'trim');
        
        // 记录突破日志
        // 可以保存到数据库
        
        $this->success('突破记录成功', [
            'realm_level' => $realmLevel,
            'realm_name' => $realmName,
            'time' => date('Y-m-d H:i:s', $breakthroughTime)
        ]);
    }

    /**
     * 功法装备记录
     */
    public function equipSkill()
    {
        $realmLevel = $this->request->post('realm_level', 1, 'intval');
        $skillId = $this->request->post('skill_id', 0, 'intval');
        $skillName = $this->request->post('skill_name', '', 'trim');
        $action = $this->request->post('action', 'equip', 'trim'); // equip or unequip
        
        $this->success('装备记录成功', [
            'realm_level' => $realmLevel,
            'skill_id' => $skillId,
            'skill_name' => $skillName,
            'action' => $action
        ]);
    }

    /**
     * 同步境界卡槽数据
     */
    public function syncRealmSlots()
    {
        $data = $this->request->post('data', '', 'trim');
        if (empty($data)) {
            $this->error('参数不能为空');
        }
        
        $slotsData = json_decode($data, true);
        if (!$slotsData) {
            $this->error('数据格式错误');
        }
        
        // 保存卡槽数据逻辑
        
        $this->success('同步成功', ['timestamp' => time()]);
    }

    /**
     * 获取玩家游戏数据（含离线收益计算）
     */
    public function getGameData()
    {
        // 获取并计算离线收益
        
        $this->success('获取成功', [
            'data' => [],
            'offline_bonus' => []
        ]);
    }

    /**
     * 心跳保活
     */
    public function heartbeat()
    {
        $this->success('在线', ['timestamp' => time()]);
    }

    /**
     * 游戏事件上报
     */
    public function event()
    {
        $type = $this->request->post('type', '', 'trim');
        $data = $this->request->post('data', '{}', 'trim');
        
        // 记录游戏事件
        
        $this->success('事件已记录', ['type' => $type]);
    }

    /**
     * 获取游戏配置
     */
    public function config()
    {
        $config = [
            'version' => '1.0.0',
            'realms' => [
                ['level' => 1, 'name' => '练体', 'exp' => 0, 'maxExp' => 100],
                ['level' => 2, 'name' => '筑基', 'exp' => 100, 'maxExp' => 500],
                ['level' => 3, 'name' => '金丹', 'exp' => 500, 'maxExp' => 2000],
                ['level' => 4, 'name' => '元婴', 'exp' => 2000, 'maxExp' => 8000],
                ['level' => 5, 'name' => '化神', 'exp' => 8000, 'maxExp' => 30000],
                ['level' => 6, 'name' => '大乘', 'exp' => 30000, 'maxExp' => 100000],
                ['level' => 7, 'name' => '渡劫', 'exp' => 100000, 'maxExp' => 999999],
            ],
            'age_interval' => 3600, // 秒
            'max_life' => 60, // 默认寿命
        ];
        
        $this->success('获取成功', $config);
    }

    /**
     * 保存背包数据
     */
    public function saveBag()
    {
        $data = $this->request->post('data', '', 'trim');
        
        $this->success('保存成功', ['timestamp' => time()]);
    }

    /**
     * 加载背包数据
     */
    public function loadBag()
    {
        $this->success('加载成功', ['data' => []]);
    }

    /**
     * 保存任务数据
     */
    public function saveTask()
    {
        $data = $this->request->post('data', '', 'trim');
        
        $this->success('保存成功', ['timestamp' => time()]);
    }

    /**
     * 加载任务数据
     */
    public function loadTask()
    {
        $this->success('加载成功', ['data' => []]);
    }

    /**
     * X320: 获取玩家复活次数信息
     * GET /api/game/revivalInfo
     */
    public function revivalInfo()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        if ($playerId <= 0) {
            $this->error('玩家ID不能为空');
        }
        
        // 获取玩家复活次数
        $player = \think\Db::table('fa_player')->where('id', $playerId)->find();
        if (!$player) {
            $this->error('玩家不存在');
        }
        
        $currentReviveCount = (int)($player['revive_count'] ?? 0);
        $maxReviveCount = (int)($player['max_revive_count'] ?? 3);
        $remainingRevives = max(0, $maxReviveCount - $currentReviveCount);
        $canRevive = $currentReviveCount < $maxReviveCount;
        
        // 获取最近复活记录
        $recentRecords = \think\Db::table('fa_xiuxian_revival_log')
            ->where('player_id', $playerId)
            ->order('createtime', 'desc')
            ->limit(10)
            ->select();
        
        // 格式化记录
        $formattedRecords = [];
        foreach ($recentRecords as $record) {
            $formattedRecords[] = [
                'id' => $record['id'],
                'death_time' => $record['death_time'],
                'revival_time' => $record['revival_time'],
                'realm' => $record['realm'],
                'revival_count' => $record['revival_count'],
                'death_location' => $record['death_location'] ?? '牧羊山',
                'death_cause' => $record['death_cause'] ?? '被妖兽袭击'
            ];
        }
        
        $this->success('获取成功', [
            'player_id' => $playerId,
            'current_revive_count' => $currentReviveCount,
            'max_revive_count' => $maxReviveCount,
            'remaining_revives' => $remainingRevives,
            'can_revive' => $canRevive,
            'limit_exhausted' => !$canRevive,
            'revive_limit_desc' => $canRevive 
                ? "剩余复活次数：{$remainingRevives}/{$maxReviveCount}"
                : "复活次数已用尽，必须回城休息",
            'recent_records' => $formattedRecords,
            'last_reset_time' => $player['last_revive_reset'] ?? 0
        ]);
    }

    /**
     * X320: 重置玩家复活次数（回城时调用）
     * POST /api/game/resetReviveCount
     */
    public function resetReviveCount()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        
        if ($playerId <= 0) {
            $this->error('玩家ID不能为空');
        }
        
        $player = \think\Db::table('fa_player')->where('id', $playerId)->find();
        if (!$player) {
            $this->error('玩家不存在');
        }
        
        // 重置复活次数
        \think\Db::table('fa_player')->where('id', $playerId)->update([
            'revive_count' => 0,
            'last_revive_reset' => time(),
            'updatetime' => time()
        ]);
        
        $this->success('重置成功', [
            'player_id' => $playerId,
            'revive_count' => 0,
            'max_revive_count' => $player['max_revive_count'] ?? 3,
            'reset_time' => time()
        ]);
    }
}
