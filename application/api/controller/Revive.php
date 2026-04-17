<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 复活系统API (X322)
 * 处理死亡复活逻辑
 */
class Revive extends Api
{
    protected $noNeedLogin = ['options', 'doRevive'];
    protected $noNeedRight = '*';

    /**
     * 获取复活选项
     * GET /api/revive/options
     * X320: 根据复活次数限制决定可用的复活方式
     */
    public function options()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        if ($playerId <= 0) {
            $this->error('玩家ID不能为空');
        }
        
        // 获取玩家当前状态
        $player = \think\Db::name('player')->where('id', $playerId)->find();
        if (!$player) {
            $this->error('玩家不存在');
        }
        
        // X320: 获取复活次数信息
        $reviveCount = (int)($player['revive_count'] ?? 0);
        $maxReviveCount = (int)($player['max_revive_count'] ?? 3);
        $canRevive = $reviveCount < $maxReviveCount;
        $remainingRevives = max(0, $maxReviveCount - $reviveCount);
        
        // 获取最新死亡记录
        $deathRecord = \think\Db::name('revive')
            ->where('player_id', $playerId)
            ->order('createtime', 'desc')
            ->find();
        
        // 获取玩家背包
        $inventory = json_decode($player['inventory'] ?? '[]', true);
        $fuhuodanCount = 0;
        foreach ($inventory as $item) {
            if ($item['id'] === 'fuhuodan') {
                $fuhuodanCount = $item['count'] ?? 0;
                break;
            }
        }
        
        // 计算复活选项
        $canUseLingshi = $player['lingshi'] >= 1000;
        $canUseItem = $fuhuodanCount > 0;
        
        // X320: 复活次数耗尽时，只允许回城复活
        if (!$canRevive) {
            // 复活次数已用尽，只能回城复活
            $options = [
                [
                    'type' => 'hospital',
                    'name' => '回城复活',
                    'description' => '复活次数已用尽，必须回城休息',
                    'cost' => ['exp_percent' => 30],
                    'cost_desc' => '损失30%当前经验',
                    'available' => true,
                    'forced' => true,
                    'forced_reason' => "复活次数({$reviveCount}/{$maxReviveCount})已用尽，无法原地复活"
                ]
            ];
        } else {
            // 仍有复活次数，显示所有选项
            $options = [
                [
                    'type' => 'normal',
                    'name' => '原地复活',
                    'description' => "消耗50%当前经验直接复活（第{$reviveCount}次死亡后复活）",
                    'cost' => ['exp_percent' => 50],
                    'cost_desc' => '损失50%当前经验',
                    'available' => $player['exp'] > 0
                ],
                [
                    'type' => 'lingshi',
                    'name' => '灵石复活',
                    'description' => '消耗1000灵石复活，无需损失经验',
                    'cost' => ['lingshi' => 1000],
                    'cost_desc' => '消耗1000灵石',
                    'available' => $canUseLingshi
                ],
                [
                    'type' => 'item',
                    'name' => '复活丹复活',
                    'description' => '使用复活丹复活，保留全部经验和物品',
                    'cost' => ['item_id' => 'fuhuodan', 'count' => 1],
                    'cost_desc' => '消耗复活丹×1',
                    'available' => $canUseItem
                ],
                [
                    'type' => 'hospital',
                    'name' => '回城复活',
                    'description' => '免费回城复活，损失30%经验，复活次数重置',
                    'cost' => ['exp_percent' => 30],
                    'cost_desc' => '损失30%当前经验',
                    'available' => true,
                    'note' => '回城后本次战斗的复活次数将重置'
                ]
            ];
        }
        
        $this->success('获取成功', [
            'options' => $options,
            // X320: 复活次数信息
            'revive_limit' => [
                'current_revives' => $reviveCount,
                'max_revives' => $maxReviveCount,
                'remaining_revives' => $remainingRevives,
                'can_revive' => $canRevive,
                'limit_exhausted' => !$canRevive
            ],
            'player_status' => [
                'current_exp' => $player['exp'],
                'current_lingshi' => $player['lingshi'],
                'current_health' => $player['health'],
                'fuhuodan_count' => $fuhuodanCount
            ],
            'death_info' => $deathRecord ? [
                'death_time' => $deathRecord['death_time'],
                'death_location' => '牧羊山深处',
                'death_cause' => $deathRecord['death_reason'] ?: '被妖兽袭击',
                'killer_name' => '妖兽'
            ] : [
                'death_time' => time(),
                'death_location' => '未知',
                'death_cause' => '未知',
                'killer_name' => '未知'
            ]
        ]);
    }

    /**
     * 执行复活
     * POST /api/revive/doRevive
     * X320: 复活次数限制 + 回城时重置复活次数
     */
    public function doRevive()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $reviveType = $this->request->post('revive_type', 'normal', 'trim');
        
        if ($playerId <= 0) {
            $this->error('玩家ID不能为空');
        }
        
        // 获取玩家数据
        $player = \think\Db::name('player')->where('id', $playerId)->find();
        if (!$player) {
            $this->error('玩家不存在');
        }
        
        // X320: 获取复活次数信息
        $reviveCount = (int)($player['revive_count'] ?? 0);
        $maxReviveCount = (int)($player['max_revive_count'] ?? 3);
        $canRevive = $reviveCount < $maxReviveCount;
        
        // 验证复活选项
        $costs = [
            'normal' => ['type' => 'exp', 'value' => 50],
            'lingshi' => ['type' => 'lingshi', 'value' => 1000],
            'item' => ['type' => 'item', 'value' => 'fuhuodan', 'count' => 1],
            'hospital' => ['type' => 'exp', 'value' => 30]
        ];
        
        if (!isset($costs[$reviveType])) {
            $this->error('无效的复活类型');
        }
        
        // X320: 非回城复活必须检查复活次数
        if ($reviveType !== 'hospital' && !$canRevive) {
            $this->error("复活次数已用尽({$reviveCount}/{$maxReviveCount})，无法继续复活，请使用回城复活");
        }
        
        $cost = $costs[$reviveType];
        $loss = [];
        $success = false;
        
        // 计算复活消耗
        if ($cost['type'] === 'exp') {
            $expLoss = floor($player['exp'] * $cost['value'] / 100);
            $loss['exp_loss'] = $expLoss;
            $newExp = $player['exp'] - $expLoss;
            $success = true;
        } elseif ($cost['type'] === 'lingshi') {
            if ($player['lingshi'] < $cost['value']) {
                $this->error('灵石不足，无法使用灵石复活');
            }
            $loss['lingshi_cost'] = $cost['value'];
            $success = true;
        } elseif ($cost['type'] === 'item') {
            $inventory = json_decode($player['inventory'] ?? '[]', true);
            $found = false;
            foreach ($inventory as &$item) {
                if ($item['id'] === $cost['value'] && $item['count'] >= $cost['count']) {
                    $item['count'] -= $cost['count'];
                    if ($item['count'] <= 0) {
                        // 移除物品
                        $inventory = array_filter($inventory, function($i) use ($item) {
                            return $i['id'] !== $item['id'] || $i['count'] > 0;
                        });
                    }
                    $found = true;
                    break;
                }
            }
            unset($item);
            
            if (!$found) {
                $this->error('复活丹不足');
            }
            $loss['item_cost'] = ['id' => 'fuhuodan', 'count' => 1];
            $success = true;
        }
        
        if (!$success) {
            $this->error('复活失败');
        }
        
        // 更新玩家状态
        $updateData = [
            'health' => $player['max_health'],
            'spirit' => $player['max_spirit'],
            'status' => 'normal',
            'updatetime' => time()
        ];
        
        if (isset($loss['exp_loss'])) {
            $updateData['exp'] = $player['exp'] - $loss['exp_loss'];
        }
        
        if (isset($loss['lingshi_cost'])) {
            $updateData['lingshi'] = $player['lingshi'] - $loss['lingshi_cost'];
        }
        
        if (isset($loss['item_cost'])) {
            $updateData['inventory'] = json_encode(array_values($inventory));
        }
        
        // X320: 回城复活时重置复活次数
        $reviveLocation = $reviveType === 'hospital' ? '青云镇' : '死亡地点';
        $reviveCountReset = false;
        if ($reviveType === 'hospital') {
            // 回城复活，重置复活次数
            $updateData['revive_count'] = 0;
            $updateData['last_revive_reset'] = time();
            $reviveCountReset = true;
        }
        
        try {
            \think\Db::startTrans();
            
            // 更新玩家数据
            \think\Db::name('player')->where('id', $playerId)->update($updateData);
            
            // 记录复活
            \think\Db::name('revive')->insert([
                'player_id' => $playerId,
                'death_reason' => '被妖兽袭击',
                'death_time' => time() - 60,
                'revive_type' => $reviveType,
                'revive_cost' => $cost['value'] ?? 0,
                'revive_time' => time(),
                'exp_loss' => $loss['exp_loss'] ?? 0
            ]);
            
            \think\Db::commit();
            
            // 获取更新后的玩家数据
            $updatedPlayer = \think\Db::name('player')->where('id', $playerId)->find();
            
            // X320: 返回复活次数信息
            $newReviveCount = (int)($updatedPlayer['revive_count'] ?? 0);
            $newMaxReviveCount = (int)($updatedPlayer['max_revive_count'] ?? 3);
            
            $this->success('复活成功', [
                'revive_type' => $reviveType,
                'revive_location' => $reviveLocation,
                'losses' => $loss,
                'current_status' => [
                    'health' => $updatedPlayer['health'],
                    'max_health' => $updatedPlayer['max_health'],
                    'spirit' => $updatedPlayer['spirit'],
                    'max_spirit' => $updatedPlayer['max_spirit'],
                    'exp' => $updatedPlayer['exp'],
                    'lingshi' => $updatedPlayer['lingshi']
                ],
                'revive_time' => time(),
                // X320: 复活次数信息
                'revive_limit' => [
                    'revive_count' => $newReviveCount,
                    'max_revive_count' => $newMaxReviveCount,
                    'remaining_revives' => max(0, $newMaxReviveCount - $newReviveCount),
                    'was_reset' => $reviveCountReset,
                    'reset_message' => $reviveCountReset ? '回城休息后，复活次数已重置' : ''
                ]
            ]);
        } catch (\Exception $e) {
            \think\Db::rollback();
            $this->error('复活失败：' . $e->getMessage());
        }
    }

    /**
     * 获取死亡记录
     * GET /api/revive/records
     */
    public function records()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $page = $this->request->get('page', 1, 'intval');
        $pageSize = $this->request->get('page_size', 20, 'intval');
        
        if ($playerId <= 0) {
            $playerId = $this->auth->id ?? 0;
        }
        
        $offset = ($page - 1) * $pageSize;
        
        $records = \think\Db::name('revive')
            ->where('player_id', $playerId)
            ->order('createtime', 'desc')
            ->limit($offset, $pageSize)
            ->select();
        
        $total = \think\Db::name('revive')
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
     * 获取死亡统计
     * GET /api/revive/stats
     */
    public function stats()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        if ($playerId <= 0) {
            $playerId = $this->auth->id ?? 0;
        }
        
        // 获取玩家数据（含X320复活次数）
        $player = \think\Db::name('player')->where('id', $playerId)->find();
        
        // 获取所有死亡记录
        $records = \think\Db::name('revive')
            ->where('player_id', $playerId)
            ->select();
        
        $totalDeaths = count($records);
        $totalExpLost = 0;
        $totalLingshiSpent = 0;
        $reviveTypeStats = [
            'normal' => 0,
            'lingshi' => 0,
            'item' => 0,
            'hospital' => 0
        ];
        
        $deathsByEnemy = [];
        
        foreach ($records as $record) {
            $totalExpLost += $record['exp_loss'] ?? 0;
            if (isset($reviveTypeStats[$record['revive_type']])) {
                $reviveTypeStats[$record['revive_type']]++;
            }
            if ($record['revive_type'] === 'lingshi') {
                $totalLingshiSpent += $record['revive_cost'] ?? 0;
            }
        }
        
        // X320: 当前战斗的复活次数限制
        $currentReviveCount = (int)($player['revive_count'] ?? 0);
        $maxReviveCount = (int)($player['max_revive_count'] ?? 3);
        
        $stats = [
            'total_deaths' => $totalDeaths,
            'deaths_by_enemy' => $deathsByEnemy,
            'total_exp_lost' => $totalExpLost,
            'total_lingshi_spent' => $totalLingshiSpent,
            'revive_type_stats' => $reviveTypeStats,
            'longest_live_time' => 0,
            'shortest_live_time' => 0,
            // X320: 当前战斗复活次数信息
            'current_battle_revive' => [
                'revive_count' => $currentReviveCount,
                'max_revive_count' => $maxReviveCount,
                'remaining_revives' => max(0, $maxReviveCount - $currentReviveCount),
                'can_revive' => $currentReviveCount < $maxReviveCount,
                'last_reset_time' => $player['last_revive_reset'] ?? 0
            ]
        ];
        
        $this->success('获取成功', $stats);
    }

    /**
     * 保护状态检查
     * GET /api/revive/protection
     */
    public function protection()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        if ($playerId <= 0) {
            $playerId = $this->auth->id ?? 0;
        }
        
        // 简化：新手保护期24小时
        $player = \think\Db::name('player')->where('id', $playerId)->find();
        
        $protection = [
            'is_protected' => false,
            'protection_type' => '',
            'remaining_time' => 0,
            'protection_desc' => ''
        ];
        
        if ($player) {
            $createTime = $player['createtime'] ?? time();
            $protectionEnd = $createTime + 86400; // 24小时保护
            $now = time();
            
            if ($now < $protectionEnd) {
                $protection['is_protected'] = true;
                $protection['protection_type'] = 'newbie';
                $protection['remaining_time'] = $protectionEnd - $now;
                $protection['protection_desc'] = '新手保护中，死亡不掉落物品';
            }
        }
        
        $this->success('获取成功', $protection);
    }

    /**
     * 购买保护
     * POST /api/revive/buyProtection
     */
    public function buyProtection()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $protectionType = $this->request->post('protection_type', 'bless', 'trim');
        $duration = $this->request->post('duration', 3600, 'intval');
        
        if ($playerId <= 0) {
            $playerId = $this->auth->id ?? 0;
        }
        
        if ($playerId <= 0) {
            $this->error('请先登录');
        }
        
        $prices = [
            'bless' => 100,
            'guild' => 50
        ];
        
        if (!isset($prices[$protectionType])) {
            $this->error('无效的保护类型');
        }
        
        $price = $prices[$protectionType];
        $totalCost = ceil($duration / 3600) * $price;
        
        // 检查玩家灵石是否足够
        $player = \think\Db::name('player')->where('id', $playerId)->find();
        if (!$player) {
            $this->error('玩家不存在');
        }
        
        if ($player['lingshi'] < $totalCost) {
            $this->error('灵石不足');
        }
        
        try {
            \think\Db::startTrans();
            
            // 扣除灵石
            \think\Db::name('player')->where('id', $playerId)->update([
                'lingshi' => $player['lingshi'] - $totalCost,
                'updatetime' => time()
            ]);
            
            \think\Db::commit();
            
            $this->success('购买成功', [
                'protection_type' => $protectionType,
                'duration' => $duration,
                'cost' => $totalCost,
                'expire_time' => time() + $duration
            ]);
        } catch (\Exception $e) {
            \think\Db::rollback();
            $this->error('购买失败');
        }
    }
}