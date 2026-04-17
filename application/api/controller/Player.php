<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 诸天仙途 - 玩家数据API
 * 处理玩家基础数据、年龄增长、境界突破等
 */
class Player extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 更新玩家数据
     */
    public function update()
    {
        $realmLevel = $this->request->post('realm_level', 1, 'intval');
        $realmName = $this->request->post('realm_name', '', 'trim');
        $breakthroughTime = $this->request->post('breakthrough_time', 0, 'intval');
        
        // 更新逻辑
        
        $this->success('更新成功', [
            'realm_level' => $realmLevel,
            'realm_name' => $realmName
        ]);
    }

    /**
     * 年龄同步
     */
    public function syncAge()
    {
        $currentAge = $this->request->post('current_age', 16, 'intval');
        $lastUpdateTime = $this->request->post('last_update_time', 0, 'intval');
        $hoursElapsed = $this->request->post('hours_elapsed', 0, 'intval');
        
        $this->success('同步成功', [
            'age' => $currentAge,
            'hours_elapsed' => $hoursElapsed,
            'timestamp' => time()
        ]);
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
        $action = $this->request->post('action', 'equip', 'trim');
        
        $this->success('装备记录成功', [
            'realm_level' => $realmLevel,
            'skill_id' => $skillId,
            'action' => $action
        ]);
    }

    /**
     * 获取玩家游戏数据
     */
    public function gameData()
    {
        // 计算离线收益
        $offlineBonus = [];
        
        $this->success('获取成功', [
            'data' => [],
            'offline_bonus' => $offlineBonus
        ]);
    }

    /**
     * 同步境界卡槽
     */
    public function syncRealmSlots()
    {
        $data = $this->request->post('data', '', 'trim');
        
        $this->success('同步成功', ['timestamp' => time()]);
    }

    /**
     * 保存初心碑数据 X305
     * POST /api/player/chuxinSave
     * 存储玩家初心碑选择：出身、天赋、问答答案、铭文
     */
    public function chuxinSave()
    {
        $playerId = $this->auth->id ?? 0;
        
        // 获取请求数据
        $origin = $this->request->post('origin', '', 'trim');           // 出身
        $talent = $this->request->post('talent', '', 'trim');           // 天赋
        $identity = $this->request->post('identity', '', 'trim');       // 身份
        $answers = $this->request->post('answers', '[]', 'trim');        // 问答答案JSON
        $inscription = $this->request->post('inscription', '', 'trim'); // 铭文
        $inscriptionTime = $this->request->post('inscription_time', 0, 'intval'); // 刻碑时间
        
        // 解析问答答案
        $answersData = json_decode($answers, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $answersData = [];
        }
        
        // 验证数据
        if (empty($answersData) && empty($inscription)) {
            $this->error('请至少完成一个问题或填写铭文');
        }
        
        // 构建初心数据
        $chuxinData = [
            'origin' => $origin,
            'talent' => $talent,
            'identity' => $identity,
            'answers' => $answersData,
            'inscription' => $inscription,
            'inscription_time' => $inscriptionTime ?: time(),
            'completed' => true,
            'update_time' => time()
        ];
        
        // 存储到数据库 player 表的 chuxin_data 字段
        // 这里使用 JSON 格式存储到 player 表
        // 实际项目中可以创建专门的 chuxin 表
        
        // 模拟保存成功
        $this->success('初心碑保存成功', [
            'chuxin_data' => $chuxinData,
            'completed' => true,
            'bonuses' => [
                'combat_exp' => 10,      // 战斗经验加成%
                'cultivate_speed' => 5,  // 修炼速度加成%
                'dao_xin_rate' => 5      // 悟道成功率加成%
            ]
        ]);
    }

    /**
     * 获取初心碑数据 X304
     * GET /api/player/chuxinInfo
     */
    public function chuxinInfo()
    {
        $playerId = $this->auth->id ?? 0;
        
        // 模拟初心数据
        $chuxinData = [
            'origin' => '凡尘俗世',
            'talent' => '中等资质',
            'identity' => '散修',
            'answers' => [
                ['question' => '你为何踏上修仙之路？', 'answer' => '追求长生'],
                ['question' => '修仙路上遇到强敌，你会如何抉择？', 'answer' => '智取为上'],
                ['question' => '获得无上力量后，你最想做什么？', 'answer' => '守护苍生']
            ],
            'inscription' => '道法自然，天人合一',
            'inscription_time' => time() - 86400,
            'completed' => true
        ];
        
        $this->success('获取成功', $chuxinData);
    }

    /**
     * 获取先贤铭文 X304
     * GET /api/player/historyInscriptions
     */
    public function historyInscriptions()
    {
        // 模拟先贤铭文列表
        $history = [
            [
                'id' => 1,
                'name' => '张三丰',
                'avatar' => '🧙',
                'realm' => '大乘期',
                'inscription' => '道法自然，顺应天道',
                'time' => time() - 86400 * 30
            ],
            [
                'id' => 2,
                'name' => '叶凡',
                'avatar' => '🧝',
                'realm' => '真仙境',
                'inscription' => '逆天改命，唯我独尊',
                'time' => time() - 86400 * 15
            ],
            [
                'id' => 3,
                'name' => '狠人女帝',
                'avatar' => '👸',
                'realm' => '红尘仙',
                'inscription' => '守护苍生，不忘初心',
                'time' => time() - 86400 * 7
            ]
        ];
        
        $this->success('获取成功', [
            'history' => $history,
            'total' => count($history)
        ]);
    }
}
