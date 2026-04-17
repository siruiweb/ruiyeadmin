<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 诸天仙途 - 功法系统API
 * 处理功法获取、学习、升级、装备
 */
class Skill extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 获取功法列表
     */
    public function list()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $realmLevel = $this->request->get('realm_level', 1, 'intval');
        
        $skills = [
            // 攻击类
            [
                'id' => 'skill_001',
                'code' => 'basic_attack',
                'name' => '基础攻击',
                'type' => 'attack',
                'level' => 1,
                'realm_required' => 1,
                'description' => '最基本的攻击方式',
                'effects' => ['damage' => 10, 'cost' => 0],
                'icon' => '⚔️',
                'rarity' => 'common'
            ],
            [
                'id' => 'skill_002',
                'code' => 'fire_ball',
                'name' => '火球术',
                'type' => 'attack',
                'level' => 1,
                'realm_required' => 2,
                'description' => '释放一团火焰攻击敌人',
                'effects' => ['damage' => 25, 'cost' => 15],
                'icon' => '🔥',
                'rarity' => 'uncommon'
            ],
            [
                'id' => 'skill_003',
                'code' => 'thunder_strike',
                'name' => '天雷击',
                'type' => 'attack',
                'level' => 3,
                'realm_required' => 3,
                'description' => '召唤天雷攻击',
                'effects' => ['damage' => 50, 'cost' => 30, 'stun' => 1],
                'icon' => '⚡',
                'rarity' => 'rare'
            ],
            // 防御类
            [
                'id' => 'skill_004',
                'code' => 'shield',
                'name' => '灵气护盾',
                'type' => 'defense',
                'level' => 1,
                'realm_required' => 1,
                'description' => '用灵气形成护盾',
                'effects' => ['defense' => 20, 'cost' => 10, 'duration' => 3],
                'icon' => '🛡️',
                'rarity' => 'common'
            ],
            [
                'id' => 'skill_005',
                'code' => 'iron_skin',
                'name' => '金刚诀',
                'type' => 'defense',
                'level' => 2,
                'realm_required' => 3,
                'description' => '大幅提升防御力',
                'effects' => ['defense' => 50, 'cost' => 25, 'duration' => 5],
                'icon' => '🛡️',
                'rarity' => 'rare'
            ],
            // 辅助类
            [
                'id' => 'skill_006',
                'code' => 'heal',
                'name' => '治愈术',
                'type' => 'heal',
                'level' => 1,
                'realm_required' => 2,
                'description' => '恢复生命值',
                'effects' => ['heal' => 30, 'cost' => 20],
                'icon' => '💚',
                'rarity' => 'uncommon'
            ],
            [
                'id' => 'skill_007',
                'code' => 'speed_boost',
                'name' => '疾风步',
                'type' => 'buff',
                'level' => 1,
                'realm_required' => 2,
                'description' => '提升速度',
                'effects' => ['speed' => 50, 'cost' => 15, 'duration' => 3],
                'icon' => '💨',
                'rarity' => 'uncommon'
            ],
            // 被动技能
            [
                'id' => 'skill_008',
                'code' => 'passive_vitality',
                'name' => '生生不息',
                'type' => 'passive',
                'level' => 1,
                'realm_required' => 1,
                'description' => '永久提升生命恢复',
                'effects' => ['health_regen' => 5],
                'icon' => '💖',
                'rarity' => 'common'
            ],
            // 稀有技能
            [
                'id' => 'skill_009',
                'code' => 'dragon_fist',
                'name' => '龙拳',
                'type' => 'attack',
                'level' => 5,
                'realm_required' => 5,
                'description' => '传说中龙族的拳法',
                'effects' => ['damage' => 150, 'cost' => 50, 'pierce' => true],
                'icon' => '🐉',
                'rarity' => 'epic'
            ],
            [
                'id' => 'skill_010',
                'code' => 'heaven_secret',
                'name' => '天机密法',
                'type' => 'attack',
                'level' => 7,
                'realm_required' => 7,
                'description' => '天阶秘法，威力无穷',
                'effects' => ['damage' => 500, 'cost' => 100, 'aoe' => true],
                'icon' => '✨',
                'rarity' => 'legendary'
            ]
        ];
        
        // 根据境界筛选可用功法
        $availableSkills = array_filter($skills, function($skill) use ($realmLevel) {
            return $skill['realm_required'] <= $realmLevel;
        });
        
        $this->success('获取成功', [
            'skills' => array_values($availableSkills),
            'owned_skills' => [],
            'equipped_skills' => []
        ]);
    }

    /**
     * 学习功法
     */
    public function learn()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $skillId = $this->request->post('skill_id', '', 'trim');
        $cost = $this->request->post('cost', 0, 'intval');
        
        if (empty($skillId)) {
            $this->error('功法ID不能为空');
        }
        
        // 检查是否已学习
        // 检查境界是否满足
        // 扣除灵石
        
        $result = [
            'skill_id' => $skillId,
            'learn_time' => time(),
            'remaining_skills' => 8 // 还能学多少功法
        ];
        
        $this->success('学习成功', $result);
    }

    /**
     * 升级功法
     */
    public function upgrade()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $skillId = $this->request->post('skill_id', '', 'trim');
        $currentLevel = $this->request->post('current_level', 1, 'intval');
        
        $newLevel = $currentLevel + 1;
        $upgradeCost = $currentLevel * 100;
        
        $result = [
            'skill_id' => $skillId,
            'old_level' => $currentLevel,
            'new_level' => $newLevel,
            'cost' => $upgradeCost,
            'effects_improved' => true
        ];
        
        $this->success('升级成功', $result);
    }

    /**
     * 装备功法
     */
    public function equip()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $skillId = $this->request->post('skill_id', '', 'trim');
        $slot = $this->request->post('slot', 0, 'intval'); // 装备槽位
        
        $result = [
            'skill_id' => $skillId,
            'slot' => $slot,
            'equipped' => true,
            'equipped_time' => time()
        ];
        
        $this->success('装备成功', $result);
    }

    /**
     * 卸下功法
     */
    public function unequip()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $skillId = $this->request->post('skill_id', '', 'trim');
        
        $result = [
            'skill_id' => $skillId,
            'equipped' => false,
            'unequipped_time' => time()
        ];
        
        $this->success('卸下成功', $result);
    }

    /**
     * 获取功法详情
     */
    public function detail()
    {
        $skillId = $this->request->get('skill_id', '', 'trim');
        
        $skill = [
            'id' => $skillId,
            'name' => '火球术',
            'type' => 'attack',
            'level' => 1,
            'realm_required' => 2,
            'description' => '释放一团火焰攻击敌人',
            'effects' => ['damage' => 25, 'cost' => 15],
            'icon' => '🔥',
            'rarity' => 'uncommon',
            'learn_cost' => 200,
            'upgrade_costs' => [100, 200, 400, 800, 1600]
        ];
        
        $this->success('获取成功', $skill);
    }

    /**
     * 获取功法图鉴
     */
    public function handbook()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        $handbook = [
            'total_skills' => 50,
            'owned_skills' => 5,
            'categories' => [
                ['name' => '攻击', 'count' => 20, 'owned' => 2],
                ['name' => '防御', 'count' => 10, 'owned' => 1],
                ['name' => '辅助', 'count' => 15, 'owned' => 1],
                ['name' => '被动', 'count' => 5, 'owned' => 1]
            ]
        ];
        
        $this->success('获取成功', $handbook);
    }

    /**
     * 获取功法连携效果
     */
    public function combos()
    {
        $equippedSkills = $this->request->get('equipped_skills', '[]', 'trim');
        
        $combos = [
            [
                'id' => 'combo_001',
                'name' => '火雷交织',
                'skills' => ['fire_ball', 'thunder_strike'],
                'description' => '火球+天雷造成额外伤害',
                'bonus' => ['damage_boost' => 20]
            ],
            [
                'id' => 'combo_002',
                'name' => '攻防一体',
                'skills' => ['shield', 'basic_attack'],
                'description' => '护盾状态下攻击增强',
                'bonus' => ['damage_boost' => 15, 'defense_boost' => 10]
            ]
        ];
        
        $this->success('获取成功', ['combos' => $combos]);
    }
}
