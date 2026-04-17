<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 诸天仙途 - 赛季系统API
 * 处理赛季信息、排行榜、赛季奖励等
 */
class Season extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 获取当前赛季信息
     */
    public function current()
    {
        $season = [
            'id' => 'season_1',
            'name' => '初入仙途',
            'description' => '欢迎来到诸天仙途！这是你的修仙之旅起点。',
            'start_time' => time() - 86400 * 7,
            'end_time' => time() + 86400 * 23,
            'days_remaining' => 23,
            'status' => 'active',
            'realm_limit' => '筑基期',
            'rewards' => [
                ['rank' => 1, 'title' => '诸天之主', 'daojin' => 10000, 'daoyu' => 1000, 'items' => ['传说称号', '限定时装']],
                ['rank' => 2, 'title' => '仙道魁首', 'daojin' => 5000, 'daoyu' => 500, 'items' => ['史诗称号', '稀有时装']],
                ['rank' => 3, 'title' => '天骄', 'daojin' => 2000, 'daoyu' => 200, 'items' => ['稀有时装']],
            ]
        ];
        
        $this->success('获取成功', $season);
    }

    /**
     * 获取赛季排行榜
     */
    public function ranking()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $type = $this->request->get('type', 'comprehensive', 'trim');
        
        $ranking = [
            ['rank' => 1, 'player_name' => '叶凡', 'realm' => '金丹期', 'score' => 99999, 'avatar' => ''],
            ['rank' => 2, 'player_name' => '狠人女帝', 'realm' => '元婴期', 'score' => 88888, 'avatar' => ''],
            ['rank' => 3, 'player_name' => '张三丰', 'realm' => '化神期', 'score' => 77777, 'avatar' => ''],
            ['rank' => 4, 'player_name' => '萧炎', 'realm' => '筑基期', 'score' => 66666, 'avatar' => ''],
            ['rank' => 5, 'player_name' => '韩立', 'realm' => '筑基期', 'score' => 55555, 'avatar' => ''],
        ];
        
        $this->success('获取成功', [
            'ranking' => $ranking,
            'my_rank' => null,
            'type' => $type
        ]);
    }

    /**
     * 获取赛季进度
     */
    public function progress()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        $this->success('获取成功', [
            'season_score' => 0,
            'season_level' => 1,
            'tasks' => [],
            'days_remaining' => 23
        ]);
    }
}
