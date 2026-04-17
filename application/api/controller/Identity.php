<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 身份创建API (X317)
 * 处理玩家身份创建、初始化角色数据
 */
class Identity extends Api
{
    protected $noNeedLogin = ['create', 'info', 'list'];
    protected $noNeedRight = '*';

    /**
     * 创建身份（选择开局身份）
     * POST /api/identity/create
     */
    public function create()
    {
        $userId = $this->auth->id ?? 0;
        
        // 获取参数
        $identityId = $this->request->post('identity_id', '', 'trim');
        $playerName = $this->request->post('player_name', '', 'trim');
        $linggenType = $this->request->post('linggen_type', 'mixed', 'trim');
        $tizhiType = $this->request->post('tizhi_type', 'normal', 'trim');
        $avatar = $this->request->post('avatar', '/assets/img/avatar.png', 'trim');
        $extraData = $this->request->post('extra_data', '{}', 'trim');
        
        // 参数验证
        if (empty($identityId)) {
            $this->error('请选择身份');
        }
        if (empty($playerName)) {
            $this->error('请输入角色名称');
        }
        if (mb_strlen($playerName) < 2 || mb_strlen($playerName) > 20) {
            $this->error('角色名称长度需在2-20字符之间');
        }
        
        // 身份类型映射
        $identityConfig = [
            'sanxiu' => [
                'name' => '散修',
                'description' => '无宗门无背景，自由修炼，初期资源匮乏但成长自由',
                'bonus' => ['cultivate_speed' => 10, 'flexibility' => 20]
            ],
            'duoshe' => [
                'name' => '夺舍',
                'description' => '夺舍重生，前世记忆残缺，修炼速度极快但每次死亡损失更多修为',
                'bonus' => ['exp_rate' => 30, 'death_penalty' => 15]
            ],
            'menrei' => [
                'name' => '门徒',
                'description' => '入宗门修习，有师父指点，初期资源充足但受宗门约束',
                'bonus' => ['resource_gain' => 20, 'freedom' => -10]
            ],
            'guoyu' => [
                'name' => '国御',
                'description' => '接受国家供奉，资源稳定但需履行义务，荣誉与责任并存',
                'bonus' => ['social_status' => 30, 'resource_stability' => 25]
            ]
        ];
        
        if (!isset($identityConfig[$identityId])) {
            $this->error('无效的身份类型');
        }
        
        // 灵根类型配置
        $linggenConfig = [
            'metal' => ['name' => '金灵根', 'bonus' => ['attack' => 15, 'crit_rate' => 10]],
            'wood' => ['name' => '木灵根', 'bonus' => ['hp' => 15, 'cultivate_speed' => 10]],
            'water' => ['name' => '水灵根', 'bonus' => ['defense' => 15, 'spirit_regen' => 15]],
            'fire' => ['name' => '火灵根', 'bonus' => ['attack' => 20, 'speed' => 10]],
            'earth' => ['name' => '土灵根', 'bonus' => ['hp' => 20, 'defense' => 10]],
            'mixed' => ['name' => '杂灵根', 'bonus' => ['all_attributes' => 5]]
        ];
        
        if (!isset($linggenConfig[$linggenType])) {
            $linggenType = 'mixed';
        }
        
        // 体质类型配置
        $tizhiConfig = [
            'normal' => ['name' => '普通体质', 'bonus' => []],
            'weak' => ['name' => '孱弱体质', 'bonus' => ['max_hp' => -10, 'exp_rate' => 10]],
            'strong' => ['name' => '强健体质', 'bonus' => ['max_hp' => 15, 'attack' => 5]],
            'spirit' => ['name' => '灵体体质', 'bonus' => ['max_spirit' => 20, 'skill_power' => 10]],
            'born' => ['name' => '天生道体', 'bonus' => ['cultivate_speed' => 20, 'breakthrough_rate' => 15]]
        ];
        
        if (!isset($tizhiConfig[$tizhiType])) {
            $tizhiType = 'normal';
        }
        
        $identityInfo = $identityConfig[$identityId];
        $linggenInfo = $linggenConfig[$linggenType];
        $tizhiInfo = $tizhiConfig[$tizhiType];
        
        // 检查角色名是否已被使用
        $existingPlayer = \think\Db::name('player')->where('player_name', $playerName)->find();
        if ($existingPlayer) {
            $this->error('该角色名已被使用');
        }
        
        // 检查用户是否已有角色
        $existingUserPlayer = \think\Db::name('player')->where('user_id', $userId)->find();
        if ($existingUserPlayer) {
            $this->error('您已创建过角色，每个账号仅能创建一个角色');
        }
        
        // 计算初始属性
        $baseHealth = 100;
        $baseSpirit = 100;
        $baseAttack = 10;
        $baseDefense = 5;
        $baseSpeed = 10;
        
        // 灵根加成
        if (isset($linggenConfig[$linggenType]['bonus']['attack'])) {
            $baseAttack += $linggenConfig[$linggenType]['bonus']['attack'];
        }
        if (isset($linggenConfig[$linggenType]['bonus']['hp'])) {
            $baseHealth += $linggenConfig[$linggenType]['bonus']['hp'];
        }
        if (isset($linggenConfig[$linggenType]['bonus']['defense'])) {
            $baseDefense += $linggenConfig[$linggenType]['bonus']['defense'];
        }
        if (isset($linggenConfig[$linggenType]['bonus']['speed'])) {
            $baseSpeed += $linggenConfig[$linggenType]['bonus']['speed'];
        }
        
        // 体质加成
        if (isset($tizhiConfig[$tizhiType]['bonus']['max_hp'])) {
            $baseHealth += $tizhiConfig[$tizhiType]['bonus']['max_hp'];
        }
        if (isset($tizhiConfig[$tizhiType]['bonus']['attack'])) {
            $baseAttack += $tizhiConfig[$tizhiType]['bonus']['attack'];
        }
        
        // 身份加成
        if ($identityId === 'duoshe') {
            $baseAttack += 5;
            $baseSpeed += 5;
        }
        
        // 创建角色数据
        $playerData = [
            'user_id' => $userId,
            'player_name' => $playerName,
            'avatar' => $avatar,
            'identity_id' => $identityId,
            'identity_name' => $identityInfo['name'],
            'realm_level' => 1,
            'realm_name' => '练体期',
            'exp' => 0,
            'age' => $identityId === 'duoshe' ? 18 : 16, // 夺舍者年龄稍大
            'max_age' => 60,
            'health' => $baseHealth,
            'max_health' => $baseHealth,
            'spirit' => $baseSpirit,
            'max_spirit' => $baseSpirit,
            'attack' => $baseAttack,
            'defense' => $baseDefense,
            'speed' => $baseSpeed,
            'lingshi' => $identityId === 'menrei' ? 500 : 200, // 门徒初始灵石更多
            'daojin' => 0,
            'linggen_type' => $linggenType,
            'linggen_name' => $linggenInfo['name'],
            'tizhi_type' => $tizhiType,
            'tizhi_name' => $tizhiInfo['name'],
            'equipped_skills' => json_encode([]),
            'inventory' => json_encode([
                ['id' => 'item_potion_small', 'name' => '小还丹', 'count' => 3, 'type' => 'consumable'],
                ['id' => 'item_lingshi_10', 'name' => '下品灵石', 'count' => $identityId === 'menrei' ? 50 : 20, 'type' => 'currency']
            ]),
            'realm_slots' => json_encode([
                ['slot' => 'body', 'realm_id' => '', 'realm_name' => '无'],
                ['slot' => 'hand', 'realm_id' => '', 'realm_name' => '无'],
                ['slot' => 'head', 'realm_id' => '', 'realm_name' => '无']
            ]),
            'current_story_id' => 1,
            'completed_tasks' => json_encode([]),
            'npc_interactions' => json_encode([]),
            'last_update_time' => time(),
            'createtime' => time(),
            'updatetime' => time(),
            'status' => 'normal'
        ];
        
        try {
            $playerId = \think\Db::name('player')->insertGetId($playerData);
            
            // 更新排行榜
            $this->updateRanking($playerId, $playerName, $avatar, 'daojin', 0);
            $this->updateRanking($playerId, $playerName, $avatar, 'level', 1);
            
            $this->success('角色创建成功', [
                'player_id' => $playerId,
                'player_name' => $playerName,
                'identity' => [
                    'id' => $identityId,
                    'name' => $identityInfo['name'],
                    'description' => $identityInfo['description'],
                    'bonus' => $identityInfo['bonus']
                ],
                'linggen' => [
                    'type' => $linggenType,
                    'name' => $linggenInfo['name'],
                    'bonus' => $linggenInfo['bonus']
                ],
                'tizhi' => [
                    'type' => $tizhiType,
                    'name' => $tizhiInfo['name'],
                    'bonus' => $tizhiInfo['bonus']
                ],
                'attributes' => [
                    'health' => $baseHealth,
                    'max_health' => $baseHealth,
                    'spirit' => $baseSpirit,
                    'max_spirit' => $baseSpirit,
                    'attack' => $baseAttack,
                    'defense' => $baseDefense,
                    'speed' => $baseSpeed
                ],
                'initial_items' => $playerData['inventory'],
                'realm' => [
                    'level' => 1,
                    'name' => '练体期'
                ]
            ]);
        } catch (\Exception $e) {
            $this->error('创建失败：' . $e->getMessage());
        }
    }

    /**
     * 更新排行榜
     */
    private function updateRanking($playerId, $playerName, $avatar, $rankType, $rankValue)
    {
        $existing = \think\Db::name('ranking')->where('player_id', $playerId)->where('rank_type', $rankType)->find();
        
        if ($existing) {
            \think\Db::name('ranking')->where('player_id', $playerId)->where('rank_type', $rankType)->update([
                'rank_value' => $rankValue,
                'update_time' => time()
            ]);
        } else {
            \think\Db::name('ranking')->insert([
                'player_id' => $playerId,
                'player_name' => $playerName,
                'player_avatar' => $avatar,
                'rank_type' => $rankType,
                'rank_value' => $rankValue,
                'rank_position' => 0,
                'update_time' => time()
            ]);
        }
    }

    /**
     * 获取身份列表
     * GET /api/identity/list
     */
    public function list()
    {
        $identities = [
            [
                'id' => 'sanxiu',
                'name' => '散修',
                'description' => '无宗门无背景，自由修炼，初期资源匮乏但成长自由',
                'pros' => ['修炼速度+10%', '自由度+20%'],
                'cons' => ['初始资源较少'],
                'recommended' => true,
                'difficulty' => 'normal'
            ],
            [
                'id' => 'duoshe',
                'name' => '夺舍',
                'description' => '夺舍重生，前世记忆残缺，修炼速度极快但每次死亡损失更多修为',
                'pros' => ['经验获取+30%', '攻防属性+5'],
                'cons' => ['死亡损失15%修为(额外)'],
                'recommended' => false,
                'difficulty' => 'hard'
            ],
            [
                'id' => 'menrei',
                'name' => '门徒',
                'description' => '入宗门修习，有师父指点，初期资源充足但受宗门约束',
                'pros' => ['初始灵石+300', '资源获取+20%'],
                'cons' => ['自由度-10%'],
                'recommended' => false,
                'difficulty' => 'easy'
            ],
            [
                'id' => 'guoyu',
                'name' => '国御',
                'description' => '接受国家供奉，资源稳定但需履行义务，荣誉与责任并存',
                'pros' => ['社会地位+30', '资源稳定性+25%'],
                'cons' => ['需完成国家任务'],
                'recommended' => false,
                'difficulty' => 'medium'
            ]
        ];
        
        $this->success('获取成功', ['identities' => $identities]);
    }

    /**
     * 获取灵根列表
     * GET /api/identity/linggenList
     */
    public function linggenList()
    {
        $linggens = [
            [
                'type' => 'metal',
                'name' => '金灵根',
                'description' => '金属性灵根，擅长攻击与暴击',
                'bonus' => ['attack' => '+15%', 'crit_rate' => '+10%'],
                'color' => '#FFD700'
            ],
            [
                'type' => 'wood',
                'name' => '木灵根',
                'description' => '木属性灵根，擅长生命与修炼',
                'bonus' => ['hp' => '+15%', 'cultivate_speed' => '+10%'],
                'color' => '#228B22'
            ],
            [
                'type' => 'water',
                'name' => '水灵根',
                'description' => '水属性灵根，擅长防御与回蓝',
                'bonus' => ['defense' => '+15%', 'spirit_regen' => '+15%'],
                'color' => '#4169E1'
            ],
            [
                'type' => 'fire',
                'name' => '火灵根',
                'description' => '火属性灵根，擅长攻击与速度',
                'bonus' => ['attack' => '+20%', 'speed' => '+10%'],
                'color' => '#FF4500'
            ],
            [
                'type' => 'earth',
                'name' => '土灵根',
                'description' => '土属性灵根，擅长生命与防御',
                'bonus' => ['hp' => '+20%', 'defense' => '+10%'],
                'color' => '#8B4513'
            ],
            [
                'type' => 'mixed',
                'name' => '杂灵根',
                'description' => '五行混杂，潜力未知，可能有惊喜',
                'bonus' => ['all_attributes' => '+5%'],
                'color' => '#9370DB'
            ]
        ];
        
        $this->success('获取成功', ['linggens' => $linggens]);
    }

    /**
     * 获取体质列表
     * GET /api/identity/tizhiList
     */
    public function tizhiList()
    {
        $tizhis = [
            [
                'type' => 'normal',
                'name' => '普通体质',
                'description' => '平平无奇，无特殊加成',
                'bonus' => [],
                'color' => '#808080'
            ],
            [
                'type' => 'weak',
                'name' => '孱弱体质',
                'description' => '体弱多病，但心性坚韧，经验获取提升',
                'bonus' => ['max_hp' => '-10%', 'exp_rate' => '+10%'],
                'color' => '#FFB6C1'
            ],
            [
                'type' => 'strong',
                'name' => '强健体质',
                'description' => '身强体壮，生命力顽强',
                'bonus' => ['max_hp' => '+15%', 'attack' => '+5'],
                'color' => '#CD853F'
            ],
            [
                'type' => 'spirit',
                'name' => '灵体体质',
                'description' => '天生灵体，灵力充沛',
                'bonus' => ['max_spirit' => '+20%', 'skill_power' => '+10%'],
                'color' => '#00CED1'
            ],
            [
                'type' => 'born',
                'name' => '天生道体',
                'description' => '万中无一的天生道体，修炼速度极快',
                'bonus' => ['cultivate_speed' => '+20%', 'breakthrough_rate' => '+15%'],
                'color' => '#9400D3'
            ]
        ];
        
        $this->success('获取成功', ['tizhis' => $tizhis]);
    }

    /**
     * 获取角色信息
     * GET /api/identity/info
     */
    public function info()
    {
        $userId = $this->auth->id ?? 0;
        
        if ($userId <= 0) {
            $this->error('请先登录');
        }
        
        $player = \think\Db::name('player')->where('user_id', $userId)->find();
        
        if (!$player) {
            $this->error('您还未创建角色');
        }
        
        $this->success('获取成功', [
            'player_id' => $player['id'],
            'player_name' => $player['player_name'],
            'avatar' => $player['avatar'],
            'identity' => [
                'id' => $player['identity_id'],
                'name' => $player['identity_name']
            ],
            'linggen' => [
                'type' => $player['linggen_type'],
                'name' => $player['linggen_name']
            ],
            'tizhi' => [
                'type' => $player['tizhi_type'],
                'name' => $player['tizhi_name']
            ],
            'realm' => [
                'level' => $player['realm_level'],
                'name' => $player['realm_name']
            ],
            'attributes' => [
                'health' => $player['health'],
                'max_health' => $player['max_health'],
                'spirit' => $player['spirit'],
                'max_spirit' => $player['max_spirit'],
                'attack' => $player['attack'],
                'defense' => $player['defense'],
                'speed' => $player['speed']
            ],
            'resources' => [
                'lingshi' => $player['lingshi'],
                'daojin' => $player['daojin'],
                'exp' => $player['exp']
            ],
            'age' => $player['age'],
            'max_age' => $player['max_age'],
            'current_story_id' => $player['current_story_id'],
            'status' => $player['status']
        ]);
    }

    /**
     * 更新身份进度
     */
    public function updateProgress()
    {
        $userId = $this->auth->id ?? 0;
        
        $currentStoryId = $this->request->post('current_story_id', 1, 'intval');
        $completedTasks = $this->request->post('completed_tasks', '[]', 'trim');
        $npcInteractions = $this->request->post('npc_interactions', '[]', 'trim');
        
        if ($userId <= 0) {
            $this->error('请先登录');
        }
        
        try {
            \think\Db::name('player')->where('user_id', $userId)->update([
                'current_story_id' => $currentStoryId,
                'completed_tasks' => $completedTasks,
                'npc_interactions' => $npcInteractions,
                'updatetime' => time()
            ]);
            
            $this->success('更新成功', [
                'current_story_id' => $currentStoryId
            ]);
        } catch (\Exception $e) {
            $this->error('更新失败');
        }
    }

    /**
     * 获取散修剧情
     */
    public function sanxiu()
    {
        $story = [
            [
                'id' => 1,
                'name' => '初入仙途',
                'level' => 1,
                'description' => '你来到牧羊山脚下的青云镇，这是一个修士与凡人混居的小镇...',
                'tasks' => [
                    ['id' => 'task1_1', 'name' => '寻找修炼之地', 'desc' => '在青云镇外找到一处适合修炼的场所', 'reward' => ['lingshi' => 100]],
                    ['id' => 'task1_2', 'name' => '第一次修炼', 'desc' => '完成第一次修炼', 'reward' => ['daojin' => 10]]
                ],
                'npcs' => [
                    ['id' => 'npc1', 'name' => '老村长', 'type' => 'quest', 'desc' => '青云镇的老村长，知晓镇上许多秘密...']
                ]
            ],
            [
                'id' => 2,
                'name' => '练体初成',
                'level' => 5,
                'description' => '你的身体素质已经有了显著提升，可以尝试更困难的修炼...',
                'tasks' => [
                    ['id' => 'task2_1', 'name' => '击杀青纹狼', 'desc' => '击败5只青纹狼', 'reward' => ['lingshi' => 200]]
                ],
                'npcs' => [
                    ['id' => 'npc2', 'name' => '铁匠老张', 'type' => 'shop', 'desc' => '青云镇唯一的铁匠，可以打造基础武器...']
                ]
            ],
            [
                'id' => 3,
                'name' => '筑基之路',
                'level' => 10,
                'description' => '你即将突破到筑基境界，这是修仙路上第一个重要关卡...',
                'tasks' => [
                    ['id' => 'task3_1', 'name' => '收集筑基丹材料', 'desc' => '收集3份筑基丹材料', 'reward' => ['daojin' => 50]]
                ],
                'npcs' => [
                    ['id' => 'npc3', 'name' => '炼丹师', 'type' => 'shop', 'desc' => '神秘的炼丹师，住在青云镇郊外...']
                ]
            ]
        ];
        
        $this->success('获取成功', ['story' => $story]);
    }

    /**
     * 获取夺舍剧情
     */
    public function duoshe()
    {
        $story = [
            [
                'id' => 1,
                'name' => '残魂觉醒',
                'level' => 1,
                'description' => '你在一具凡人体内苏醒，前世的记忆残缺不全，只记得自己是修士...',
                'tasks' => [
                    ['id' => 'd_task1_1', 'name' => '恢复残魂', 'desc' => '完成第一次修炼恢复残魂', 'reward' => ['daojin' => 30]],
                    ['id' => 'd_task1_2', 'name' => '寻找灵根', 'desc' => '寻找能恢复灵根的天材地宝', 'reward' => ['lingshi' => 200]]
                ],
                'npcs' => [
                    ['id' => 'd_npc1', 'name' => '前世的道童', 'type' => 'guide', 'desc' => '忠心耿耿的道童，似乎在寻找什么人...']
                ],
                'special' => '夺舍惩罚：每次死亡额外损失10%修为'
            ],
            [
                'id' => 2,
                'name' => '前世纠葛',
                'level' => 5,
                'description' => '你的残魂逐渐恢复，开始回忆起前世的恩怨情仇...',
                'tasks' => [
                    ['id' => 'd_task2_1', 'name' => '复仇之路', 'desc' => '击败前世的仇敌', 'reward' => ['daojin' => 100]]
                ],
                'npcs' => [
                    ['id' => 'd_npc2', 'name' => '前世的仇敌', 'type' => 'enemy', 'desc' => '当年害你的人，如今已是筑基修士...']
                ]
            ],
            [
                'id' => 3,
                'name' => '重塑金身',
                'level' => 10,
                'description' => '你需要重塑金身，恢复前世的修为，再攀修仙高峰...',
                'tasks' => [
                    ['id' => 'd_task3_1', 'name' => '收集天材', 'desc' => '收集重塑金身所需的天材地宝', 'reward' => ['daojin' => 200]]
                ],
                'npcs' => [
                    ['id' => 'd_npc3', 'name' => '前世的弟子', 'type' => 'friend', 'desc' => '当年你最得意的弟子，如今已是金丹期...']
                ]
            ]
        ];
        
        $this->success('获取成功', ['story' => $story]);
    }
}