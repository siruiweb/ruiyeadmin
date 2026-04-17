<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 排行榜API
 * 处理道行榜、等级榜、战斗力榜等
 */
class Ranking extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 获取排行榜列表
     */
    public function lists()
    {
        $rankTypes = [
            ['id' => 'daojin', 'name' => '道行榜', 'icon' => '📿', 'description' => '按道行值排名'],
            ['id' => 'level', 'name' => '等级榜', 'icon' => '⬆️', 'description' => '按境界等级排名'],
            ['id' => 'battle', 'name' => '战力榜', 'icon' => '⚔️', 'description' => '按战斗力排名'],
            ['id' => 'wealth', 'name' => '财富榜', 'icon' => '💰', 'description' => '按灵石排名'],
            ['id' => 'pvp', 'name' => 'PVP榜', 'icon' => '🏆', 'description' => '按PVP胜率排名'],
            ['id' => 'killer', 'name' => '击杀榜', 'icon' => '💀', 'description' => '按击杀数排名']
        ];
        
        $this->success('获取成功', ['rank_types' => $rankTypes]);
    }

    /**
     * 获取指定排行榜
     */
    public function get()
    {
        $rankType = $this->request->get('type', 'daojin', 'trim');
        $page = $this->request->get('page', 1, 'intval');
        $pageSize = $this->request->get('page_size', 20, 'intval');
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        // 模拟排行榜数据
        $rankings = [
            [
                'rank' => 1,
                'player_id' => 10001,
                'player_name' => '太上长老',
                'player_avatar' => '',
                'realm_name' => '大乘期',
                'value' => 500000,
                'change' => 0
            ],
            [
                'rank' => 2,
                'player_id' => 10002,
                'player_name' => '瑶池仙子',
                'player_avatar' => '',
                'realm_name' => '渡劫期',
                'value' => 450000,
                'change' => 1
            ],
            [
                'rank' => 3,
                'player_id' => 10003,
                'player_name' => '剑圣',
                'player_avatar' => '',
                'realm_name' => '合体期',
                'value' => 400000,
                'change' => -1
            ],
            [
                'rank' => 4,
                'player_id' => 10004,
                'player_name' => '丹王',
                'player_avatar' => '',
                'realm_name' => '合体期',
                'value' => 380000,
                'change' => 0
            ],
            [
                'rank' => 5,
                'player_id' => 10005,
                'player_name' => '阵法师',
                'player_avatar' => '',
                'realm_name' => '化神期',
                'value' => 350000,
                'change' => 2
            ],
            [
                'rank' => 6,
                'player_id' => 10006,
                'player_name' => '器灵子',
                'player_avatar' => '',
                'realm_name' => '化神期',
                'value' => 320000,
                'change' => -1
            ],
            [
                'rank' => 7,
                'player_id' => 10007,
                'player_name' => '御兽真人',
                'player_avatar' => '',
                'realm_name' => '元婴期',
                'value' => 280000,
                'change' => 0
            ],
            [
                'rank' => 8,
                'player_id' => 10008,
                'player_name' => '符箓师',
                'player_avatar' => '',
                'realm_name' => '元婴期',
                'value' => 250000,
                'change' => 3
            ],
            [
                'rank' => 9,
                'player_id' => 10009,
                'player_name' => '灵植师',
                'player_avatar' => '',
                'realm_name' => '元婴期',
                'value' => 220000,
                'change' => -2
            ],
            [
                'rank' => 10,
                'player_id' => 10010,
                'player_name' => '散修甲',
                'player_avatar' => '',
                'realm_name' => '金丹期',
                'value' => 200000,
                'change' => 0
            ]
        ];
        
        // 获取玩家排名
        $playerRank = null;
        if ($playerId > 0) {
            $playerRank = [
                'rank' => 666,
                'player_id' => $playerId,
                'player_name' => '你',
                'player_avatar' => '',
                'realm_name' => '筑基期',
                'value' => 50000,
                'change' => 5
            ];
        }
        
        $this->success('获取成功', [
            'rank_type' => $rankType,
            'rankings' => $rankings,
            'player_rank' => $playerRank,
            'total' => 1000,
            'page' => $page,
            'page_size' => $pageSize
        ]);
    }

    /**
     * 获取玩家排名详情
     */
    public function playerRank()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        $ranks = [
            ['type' => 'daojin', 'rank' => 666, 'value' => 50000, 'change' => 5],
            ['type' => 'level', 'rank' => 500, 'value' => 5, 'change' => 0],
            ['type' => 'battle', 'rank' => 800, 'value' => 5000, 'change' => -10],
            ['type' => 'wealth', 'rank' => 1000, 'value' => 10000, 'change' => 100],
            ['type' => 'pvp', 'rank' => 0, 'value' => 0, 'change' => 0],
            ['type' => 'killer', 'rank' => 200, 'value' => 100, 'change' => 5]
        ];
        
        $this->success('获取成功', [
            'player_id' => $playerId,
            'ranks' => $ranks
        ]);
    }

    /**
     * 获取历史排名
     */
    public function history()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $rankType = $this->request->get('type', 'daojin', 'trim');
        
        $history = [
            ['date' => date('Y-m-d'), 'rank' => 666, 'value' => 50000],
            ['date' => date('Y-m-d', strtotime('-1 day')), 'rank' => 671, 'value' => 49500],
            ['date' => date('Y-m-d', strtotime('-2 days')), 'rank' => 680, 'value' => 49000],
            ['date' => date('Y-m-d', strtotime('-7 days')), 'rank' => 700, 'value' => 45000]
        ];
        
        $this->success('获取成功', [
            'history' => $history,
            'type' => $rankType
        ]);
    }

    /**
     * 获取赛季信息
     */
    public function season()
    {
        $season = [
            'season_id' => 1,
            'season_name' => '第一赛季·筑基篇',
            'start_time' => strtotime('2026-03-01'),
            'end_time' => strtotime('2026-04-01'),
            'status' => 'ongoing',
            'rewards' => [
                ['rank' => 1, 'reward' => '限定称号+神兵'],
                ['rank' => 2, 'reward' => '限定称号+绝学'],
                ['rank' => 3, 'reward' => '限定称号+法宝'],
                ['rank' => 'top10', 'reward' => '限定称号+珍稀材料'],
                ['rank' => 'top100', 'reward' => '限定称号+大量灵石']
            ]
        ];
        
        $this->success('获取成功', $season);
    }

    /**
     * 获取跨服排行榜
     */
    public function crossServer()
    {
        $rankType = $this->request->get('type', 'daojin', 'trim');
        
        $rankings = [
            [
                'rank' => 1,
                'server_id' => 1,
                'server_name' => '诸天仙途·一区',
                'player_id' => 10001,
                'player_name' => '太上长老',
                'realm_name' => '大乘期',
                'value' => 500000
            ],
            [
                'rank' => 2,
                'server_id' => 2,
                'server_name' => '诸天仙途·二区',
                'player_id' => 20001,
                'player_name' => '天帝',
                'realm_name' => '大乘期',
                'value' => 480000
            ]
        ];
        
        $this->success('获取成功', [
            'rankings' => $rankings,
            'rank_type' => $rankType
        ]);
    }

    /**
     * 获取排行榜奖励
     */
    public function rewards()
    {
        $rankType = $this->request->get('type', 'daojin', 'trim');
        $seasonId = $this->request->get('season_id', 1, 'intval');
        
        $rewards = [
            ['rank_start' => 1, 'rank_end' => 1, 'rewards' => ['称号' => '天下第一', '灵石' => 1000000, '道具' => '神兵碎片×10']],
            ['rank_start' => 2, 'rank_end' => 3, 'rewards' => ['称号' => '名列前茅', '灵石' => 500000, '道具' => '绝学残页×5']],
            ['rank_start' => 4, 'rank_end' => 10, 'rewards' => ['称号' => '仙道翘楚', '灵石' => 200000]],
            ['rank_start' => 11, 'rank_end' => 100, 'rewards' => ['灵石' => 50000]],
            ['rank_start' => 101, 'rank_end' => 1000, 'rewards' => ['灵石' => 10000]]
        ];
        
        $this->success('获取成功', [
            'rank_type' => $rankType,
            'season_id' => $seasonId,
            'rewards' => $rewards
        ]);
    }
}
