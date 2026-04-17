<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 战利品获取API (X319)
 * 处理搜索尸体、获得物品等系统
 */
class Loot extends Api
{
    protected $noNeedLogin = ['search', 'locations'];
    protected $noNeedRight = '*';

    /**
     * 搜索尸体
     * POST /api/loot/search
     */
    public function search()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $location = $this->request->post('location', 'muyaoshan', 'trim');
        $searchLevel = $this->request->post('search_level', 1, 'intval');
        
        if ($playerId <= 0) {
            $playerId = $this->auth->id ?? 0;
        }
        
        if ($playerId <= 0) {
            $this->error('请先登录');
        }
        
        // 获取玩家信息
        $player = \think\Db::name('player')->where('id', $playerId)->find();
        if (!$player) {
            $this->error('玩家不存在');
        }
        
        // 检查是否有足够的灵力
        $spiritCost = 20;
        if ($player['spirit'] < $spiritCost) {
            $this->error('灵力不足，无法搜索');
        }
        
        // 位置配置
        $locationConfig = [
            'muyaoshan' => [
                'name' => '牧羊山',
                'level_min' => 1,
                'level_max' => 5,
                'corpses' => [
                    ['name' => '青纹狼尸体', 'level' => 1, 'items' => ['兽皮', '狼牙'], 'daojin' => 5, 'lingshi' => 10],
                    ['name' => '野兔尸体', 'level' => 1, 'items' => ['兔毛', '兔肉'], 'daojin' => 2, 'lingshi' => 5],
                    ['name' => '筑基散修尸体', 'level' => 3, 'items' => ['下品灵石×3', '基础功法残页'], 'daojin' => 15, 'lingshi' => 50]
                ]
            ],
            'qingyunzhen' => [
                'name' => '青云镇外',
                'level_min' => 2,
                'level_max' => 8,
                'corpses' => [
                    ['name' => '镖师尸体', 'level' => 3, 'items' => ['镖旗', '银两'], 'daojin' => 10, 'lingshi' => 30],
                    ['name' => '散修尸体', 'level' => 5, 'items' => ['灵石', '丹药'], 'daojin' => 20, 'lingshi' => 80],
                    ['name' => '妖兽尸体', 'level' => 4, 'items' => ['妖兽内丹', '妖兽骨'], 'daojin' => 18, 'lingshi' => 60]
                ]
            ],
            'xuanyuangu' => [
                'name' => '玄渊谷',
                'level_min' => 5,
                'level_max' => 15,
                'corpses' => [
                    ['name' => '金丹期修士尸体', 'level' => 8, 'items' => ['中品灵石×5', '金丹碎片'], 'daojin' => 40, 'lingshi' => 150],
                    ['name' => '上古妖兽尸体', 'level' => 10, 'items' => ['上古兽皮', '妖丹'], 'daojin' => 60, 'lingshi' => 200]
                ]
            ],
            'moyaolin' => [
                'name' => '魔曜林',
                'level_min' => 10,
                'level_max' => 20,
                'corpses' => [
                    ['name' => '魔修尸体', 'level' => 12, 'items' => ['魔晶', '魔功残页'], 'daojin' => 80, 'lingshi' => 300],
                    ['name' => '妖兽王尸体', 'level' => 15, 'items' => ['兽王内丹', '王骨'], 'daojin' => 100, 'lingshi' => 500]
                ]
            ],
            'cangshenge' => [
                'name' => '藏尸阁',
                'level_min' => 1,
                'level_max' => 30,
                'corpses' => [
                    ['name' => '神秘修士尸体', 'level' => 20, 'items' => ['神秘令牌', '上古功法'], 'daojin' => 200, 'lingshi' => 1000],
                    ['name' => '远古妖兽尸体', 'level' => 25, 'items' => ['远古龙骨', '龙鳞'], 'daojin' => 500, 'lingshi' => 2000]
                ]
            ]
        ];
        
        // 验证位置
        if (!isset($locationConfig[$location])) {
            $location = 'muyaoshan';
        }
        
        $locationInfo = $locationConfig[$location];
        
        // 根据玩家境界筛选尸体
        $playerRealm = $player['realm_level'];
        $availableCorpses = array_filter($locationInfo['corpses'], function($corpse) use ($playerRealm) {
            return $corpse['level'] <= $playerRealm + 3;
        });
        
        if (empty($availableCorpses)) {
            $availableCorpses = $locationInfo['corpses'];
        }
        
        // 随机选择一具尸体
        $selectedCorpse = $availableCorpses[array_rand($availableCorpses)];
        
        // 计算幸运加成
        $luckBonus = 0;
        // 可以根据玩家装备、身份等计算幸运加成
        
        // 随机获得物品
        $foundItems = [];
        $itemCount = rand(1, 3);
        $items = $selectedCorpse['items'];
        shuffle($items);
        for ($i = 0; $i < min($itemCount, count($items)); $i++) {
            $count = rand(1, 3) + floor($luckBonus / 20);
            $foundItems[] = [
                'name' => $items[$i],
                'count' => min($count, 10) // 最多10个
            ];
        }
        
        // 计算道行和灵石奖励
        $daojinReward = $selectedCorpse['daojin'] + rand(0, 5) + floor($luckBonus / 10);
        $lingshiReward = $selectedCorpse['lingshi'] + rand(0, 20) + floor($luckBonus / 5);
        
        // 更新玩家数据
        try {
            \think\Db::startTrans();
            
            // 扣除灵力
            $newSpirit = $player['spirit'] - $spiritCost;
            
            // 更新背包
            $inventory = json_decode($player['inventory'] ?? '[]', true);
            foreach ($foundItems as $item) {
                $found = false;
                foreach ($inventory as &$invItem) {
                    if ($invItem['name'] === $item['name']) {
                        $invItem['count'] += $item['count'];
                        $found = true;
                        break;
                    }
                }
                unset($invItem);
                
                if (!$found) {
                    $inventory[] = [
                        'id' => 'item_' . uniqid(),
                        'name' => $item['name'],
                        'count' => $item['count'],
                        'type' => 'material'
                    ];
                }
            }
            
            // 更新玩家
            \think\Db::name('player')->where('id', $playerId)->update([
                'spirit' => $newSpirit,
                'daojin' => $player['daojin'] + $daojinReward,
                'lingshi' => $player['lingshi'] + $lingshiReward,
                'inventory' => json_encode($inventory),
                'updatetime' => time()
            ]);
            
            // 记录搜索
            \think\Db::name('loot')->insert([
                'player_id' => $playerId,
                'corpse_name' => $selectedCorpse['name'],
                'corpse_level' => $selectedCorpse['level'],
                'found_items' => json_encode($foundItems),
                'daojin_reward' => $daojinReward,
                'lingshi_reward' => $lingshiReward,
                'createtime' => time()
            ]);
            
            \think\Db::commit();
            
            $this->success('搜索成功', [
                'corpse' => [
                    'name' => $selectedCorpse['name'],
                    'level' => $selectedCorpse['level'],
                    'description' => '这具尸体似乎死亡不久，还残留着一些灵韵...'
                ],
                'items' => $foundItems,
                'rewards' => [
                    'daojin' => $daojinReward,
                    'lingshi' => $lingshiReward
                ],
                'costs' => [
                    'spirit' => $spiritCost
                ],
                'current_status' => [
                    'spirit' => $newSpirit,
                    'max_spirit' => $player['max_spirit'],
                    'daojin' => $player['daojin'] + $daojinReward,
                    'lingshi' => $player['lingshi'] + $lingshiReward
                ],
                'search_time' => time()
            ]);
        } catch (\Exception $e) {
            \think\Db::rollback();
            $this->error('搜索失败：' . $e->getMessage());
        }
    }

    /**
     * 获取搜索记录
     * GET /api/loot/records
     */
    public function records()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $page = $this->request->get('page', 1, 'intval');
        $pageSize = $this->request->get('page_size', 20, 'intval');
        
        if ($playerId <= 0) {
            $playerId = $this->auth->id ?? 0;
        }
        
        if ($playerId <= 0) {
            $this->error('请先登录');
        }
        
        $offset = ($page - 1) * $pageSize;
        
        $records = \think\Db::name('loot')
            ->where('player_id', $playerId)
            ->order('createtime', 'desc')
            ->limit($offset, $pageSize)
            ->select();
        
        // 格式化记录
        foreach ($records as &$record) {
            $record['found_items'] = json_decode($record['found_items'] ?? '[]', true);
            $record['time_desc'] = date('Y-m-d H:i', $record['createtime']);
        }
        unset($record);
        
        $total = \think\Db::name('loot')
            ->where('player_id', $playerId)
            ->count();
        
        $this->success('获取成功', [
            'records' => $records,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize
        ]);
    }

    /**
     * 获取搜索统计
     * GET /api/loot/stats
     */
    public function stats()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        if ($playerId <= 0) {
            $playerId = $this->auth->id ?? 0;
        }
        
        if ($playerId <= 0) {
            $this->error('请先登录');
        }
        
        // 统计总搜索次数
        $totalSearches = \think\Db::name('loot')->where('player_id', $playerId)->count();
        
        // 统计总获得物品
        $totalItems = 0;
        $totalDaojin = 0;
        $totalLingshi = 0;
        
        $records = \think\Db::name('loot')->where('player_id', $playerId)->select();
        $corpseCounts = [];
        
        foreach ($records as $record) {
            $items = json_decode($record['found_items'] ?? '[]', true);
            $totalItems += count($items);
            $totalDaojin += $record['daojin_reward'] ?? 0;
            $totalLingshi += $record['lingshi_reward'] ?? 0;
            
            $corpseName = $record['corpse_name'] ?? '';
            if ($corpseName) {
                if (!isset($corpseCounts[$corpseName])) {
                    $corpseCounts[$corpseName] = 0;
                }
                $corpseCounts[$corpseName]++;
            }
        }
        
        // 找出最常遇到的尸体
        arsort($corpseCounts);
        $favoriteCorpse = key($corpseCounts) ?: '';
        
        // 计算幸运次数（假设获得传说物品为幸运）
        $luckyCount = 0;
        
        $stats = [
            'total_searches' => $totalSearches,
            'total_items' => $totalItems,
            'total_daojin' => $totalDaojin,
            'total_lingshi' => $totalLingshi,
            'favorite_corpse' => $favoriteCorpse,
            'lucky_count' => $luckyCount,
            'corpse_stats' => array_slice($corpseCounts, 0, 5)
        ];
        
        $this->success('获取成功', $stats);
    }

    /**
     * 搜索位置列表
     * GET /api/loot/locations
     */
    public function locations()
    {
        $userId = $this->auth->id ?? 0;
        
        // 获取玩家境界
        $playerRealmLevel = 1;
        if ($userId > 0) {
            $player = \think\Db::name('player')->where('user_id', $userId)->find();
            if ($player) {
                $playerRealmLevel = $player['realm_level'];
            }
        }
        
        $locations = [
            [
                'id' => 'muyaoshan',
                'name' => '牧羊山',
                'level_range' => '1-5',
                'description' => '山势平缓，是散修常来之地',
                'corpse_types' => ['妖兽', '低阶散修'],
                'spirit_cost' => 20,
                'unlocked' => true,
                'recommended' => $playerRealmLevel <= 3
            ],
            [
                'id' => 'qingyunzhen',
                'name' => '青云镇外',
                'level_range' => '2-8',
                'description' => '常有修士在此陨落',
                'corpse_types' => ['散修', '镖师'],
                'spirit_cost' => 30,
                'unlocked' => $playerRealmLevel >= 2,
                'recommended' => $playerRealmLevel >= 2 && $playerRealmLevel <= 5
            ],
            [
                'id' => 'xuanyuangu',
                'name' => '玄渊谷',
                'level_range' => '5-15',
                'description' => '传闻有上古遗迹',
                'corpse_types' => ['高阶修士', '上古妖兽'],
                'spirit_cost' => 50,
                'unlocked' => $playerRealmLevel >= 5,
                'recommended' => $playerRealmLevel >= 5 && $playerRealmLevel <= 10
            ],
            [
                'id' => 'moyaolin',
                'name' => '魔曜林',
                'level_range' => '10-20',
                'description' => '危险区域，强者陨落之地',
                'corpse_types' => ['魔修', '妖兽王'],
                'spirit_cost' => 80,
                'unlocked' => $playerRealmLevel >= 10,
                'recommended' => $playerRealmLevel >= 10
            ],
            [
                'id' => 'cangshenge',
                'name' => '藏尸阁',
                'level_range' => '1-30',
                'description' => '神秘之地，尸体来源不明',
                'corpse_types' => ['各种修士'],
                'spirit_cost' => 100,
                'unlocked' => $playerRealmLevel >= 15,
                'recommended' => $playerRealmLevel >= 15
            ]
        ];
        
        $this->success('获取成功', ['locations' => $locations]);
    }
}