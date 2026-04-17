<?php

namespace app\api\controller;

/**
 * 多人副本接口 X303
 */
class Dungeon extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     * 获取副本列表
     */
    public function list()
    {
        $dungeons = [
            [
                'id' => 'forest',
                'name' => '妖兽森林',
                'description' => '森林深处潜伏着强大的妖兽',
                'difficulty' => 'normal',
                'recommendedCombat' => 5000,
                'teamSize' => 4,
                'duration' => 30,
                'icon' => '🌲'
            ],
            [
                'id' => 'volcano',
                'name' => '烈焰洞窟',
                'description' => '火属性怪物聚集之地',
                'difficulty' => 'hard',
                'recommendedCombat' => 15000,
                'teamSize' => 4,
                'duration' => 35,
                'icon' => '🔥'
            ],
            [
                'id' => 'frozen',
                'name' => '冰霜深渊',
                'description' => '极寒之地，考验队伍实力',
                'difficulty' => 'hard',
                'recommendedCombat' => 25000,
                'teamSize' => 4,
                'duration' => 40,
                'icon' => '❄️'
            ],
            [
                'id' => 'thunder',
                'name' => '雷霆禁地',
                'description' => '雷电交加，危机四伏',
                'difficulty' => 'hell',
                'recommendedCombat' => 50000,
                'teamSize' => 4,
                'duration' => 45,
                'icon' => '⚡'
            ],
            [
                'id' => 'demon',
                'name' => '万魔窟',
                'description' => '群魔乱舞，险象环生',
                'difficulty' => 'nightmare',
                'recommendedCombat' => 100000,
                'teamSize' => 4,
                'duration' => 60,
                'icon' => '👹'
            ]
        ];
        
        $this->success('ok', $dungeons);
    }

    /**
     * 获取副本详情
     */
    public function detail()
    {
        $dungeonId = $this->request->get('dungeon_id', 'forest');
        
        $dungeons = [
            'forest' => [
                'name' => '妖兽森林',
                'description' => '森林深处潜伏着强大的妖兽，击败它们可获得丰厚奖励',
                'recommendedCombat' => 5000,
                'teamSize' => 4,
                'duration' => 30,
                'bgUrl' => '',
                'rewards' => ['exp' => 5000, 'lingshi' => 2000, 'daojin' => 500],
                'boss' => [
                    'name' => '妖兽之王',
                    'icon' => '👹',
                    'level' => 50,
                    'health' => 100000,
                    'attack' => 5000,
                    'defense' => 3000,
                    'skills' => ['狂怒', '撕裂', '兽群召唤']
                ]
            ],
            'volcano' => [
                'name' => '烈焰洞窟',
                'description' => '熔岩与火焰的世界，充满危险',
                'recommendedCombat' => 15000,
                'teamSize' => 4,
                'duration' => 35,
                'bgUrl' => '',
                'rewards' => ['exp' => 12000, 'lingshi' => 5000, 'daojin' => 1200],
                'boss' => [
                    'name' => '火焰领主',
                    'icon' => '🔥',
                    'level' => 80,
                    'health' => 250000,
                    'attack' => 12000,
                    'defense' => 8000,
                    'skills' => ['烈焰冲击', '熔岩护盾', '火雨']
                ]
            ]
        ];
        
        $data = $dungeons[$dungeonId] ?? $dungeons['forest'];
        
        $this->success('ok', $data);
    }

    /**
     * 创建队伍
     */
    public function createTeam()
    {
        $dungeonId = $this->request->post('dungeon_id');
        $difficulty = $this->request->post('difficulty', 'normal');
        
        if (!$dungeonId) {
            $this->error('请选择副本');
        }
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        // 创建队伍
        $team = new \app\common\model\DungeonTeam();
        $team->dungeon_id = $dungeonId;
        $team->difficulty = $difficulty;
        $team->leader_id = $player->id;
        $team->status = 'waiting';
        $team->member_count = 1;
        $team->createtime = time();
        
        $team->save();
        
        $this->success('队伍创建成功', ['teamId' => $team->id]);
    }

    /**
     * 加入队伍
     */
    public function joinTeam()
    {
        $teamId = $this->request->post('team_id');
        
        if (!$teamId) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        $team = \app\common\model\DungeonTeam::get($teamId);
        
        if (!$team) {
            $this->error('队伍不存在');
        }
        
        if ($team->status !== 'waiting') {
            $this->error('队伍已开始或已结束');
        }
        
        if ($team->member_count >= 4) {
            $this->error('队伍已满');
        }
        
        // 添加成员
        $members = json_decode($team->members, true) ?: [];
        $members[] = [
            'playerId' => $player->id,
            'name' => $player->player_name,
            'level' => $player->realm_level,
            'levelName' => $player->realm_name,
            'combat' => $player->attack + $player->defense + $player->health
        ];
        
        $team->members = json_encode($members);
        $team->member_count = count($members);
        $team->save();
        
        $this->success('加入成功');
    }

    /**
     * 离开队伍
     */
    public function leaveTeam()
    {
        $playerId = $this->auth->id;
        
        $team = \app\common\model\DungeonTeam::where('leader_id', $playerId)
            ->where('status', 'waiting')
            ->find();
        
        if ($team) {
            $team->delete();
        }
        
        $this->success('已离开队伍');
    }

    /**
     * 快速匹配
     */
    public function quickMatch()
    {
        $dungeonId = $this->request->post('dungeon_id', 'forest');
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        // 查找等待中的队伍
        $team = \app\common\model\DungeonTeam::where('dungeon_id', $dungeonId)
            ->where('status', 'waiting')
            ->where('member_count', '<', 4)
            ->order('createtime', 'asc')
            ->find();
        
        if ($team) {
            // 加入已有队伍
            $members = json_decode($team->members, true) ?: [];
            $members[] = [
                'playerId' => $player->id,
                'name' => $player->player_name,
                'level' => $player->realm_level,
                'levelName' => $player->realm_name,
                'combat' => $player->attack + $player->defense + $player->health
            ];
            
            $team->members = json_encode($members);
            $team->member_count = count($members);
            $team->save();
            
            $this->success('匹配成功', ['teamId' => $team->id]);
        } else {
            // 创建新队伍
            $team = new \app\common\model\DungeonTeam();
            $team->dungeon_id = $dungeonId;
            $team->difficulty = 'normal';
            $team->leader_id = $player->id;
            $team->status = 'waiting';
            $team->member_count = 1;
            $team->members = json_encode([[
                'playerId' => $player->id,
                'name' => $player->player_name,
                'level' => $player->realm_level,
                'levelName' => $player->realm_name,
                'combat' => $player->attack + $player->defense + $player->health
            ]]);
            $team->createtime = time();
            $team->save();
            
            $this->success('已创建队伍等待匹配', ['teamId' => $team->id]);
        }
    }

    /**
     * 开始副本战斗
     */
    public function startBattle()
    {
        $dungeonId = $this->request->post('dungeon_id');
        $difficulty = $this->request->post('difficulty', 'normal');
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        // 创建战斗记录
        $battle = new \app\common\model\DungeonBattle();
        $battle->player_id = $player->id;
        $battle->dungeon_id = $dungeonId;
        $battle->difficulty = $difficulty;
        $battle->status = 'ongoing';
        $battle->boss_health = 100000;
        $battle->max_boss_health = 100000;
        $battle->start_time = time();
        $battle->createtime = time();
        
        $battle->save();
        
        $this->success('战斗开始', [
            'battleId' => $battle->id,
            'bossHealth' => $battle->boss_health,
            'bossMaxHealth' => $battle->max_boss_health
        ]);
    }

    /**
     * 战斗操作
     */
    public function battleAction()
    {
        $action = $this->request->post('action', 'attack');
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        $battle = \app\common\model\DungeonBattle::where('player_id', $player->id)
            ->where('status', 'ongoing')
            ->order('createtime', 'desc')
            ->find();
        
        if (!$battle) {
            $this->error('没有进行中的战斗');
        }
        
        $logs = [];
        $bossDamage = 0;
        
        switch ($action) {
            case 'attack':
                $bossDamage = rand(3000, 5000);
                $logs[] = "你发动了攻击，造成 {$bossDamage} 点伤害！";
                break;
            case 'skill':
                $bossDamage = rand(5000, 10000);
                $logs[] = "你使用了技能，造成 {$bossDamage} 点伤害！";
                break;
            case 'defend':
                $logs[] = "你进入了防御姿态";
                break;
            case 'heal':
                $logs[] = "你使用了治疗术，恢复了5000点生命";
                break;
        }
        
        // Boss反击
        $playerDamage = rand(1000, 2000);
        $logs[] = "Boss发动攻击，造成 {$playerDamage} 点伤害！";
        
        // 更新Boss血量
        $battle->boss_health = max(0, $battle->boss_health - $bossDamage);
        $battle->save();
        
        // 检查战斗是否结束
        $victory = false;
        if ($battle->boss_health <= 0) {
            $victory = true;
            $battle->status = 'victory';
            $battle->end_time = time();
            $battle->save();
            
            // 发放奖励
            $player->lingshi += 2000;
            $player->daojin += 500;
            $player->exp += 5000;
            $player->save();
        }
        
        $this->success('ok', [
            'logs' => $logs,
            'bossDamage' => $bossDamage,
            'bossHealth' => $battle->boss_health,
            'victory' => $victory
        ]);
    }

    /**
     * 结束战斗
     */
    public function endBattle()
    {
        $playerId = $this->auth->id;
        
        $battle = \app\common\model\DungeonBattle::where('player_id', $playerId)
            ->where('status', 'ongoing')
            ->find();
        
        if ($battle) {
            $battle->status = 'surrender';
            $battle->end_time = time();
            $battle->save();
        }
        
        $this->success('ok');
    }

    /**
     * 获取副本记录
     */
    public function records()
    {
        $playerId = $this->auth->id;
        
        $list = \app\common\model\DungeonBattle::where('player_id', $playerId)
            ->order('createtime', 'desc')
            ->limit(50)
            ->select();
        
        $data = [];
        foreach ($list as $battle) {
            $data[] = [
                'id' => $battle->id,
                'dungeonId' => $battle->dungeon_id,
                'difficulty' => $battle->difficulty,
                'status' => $battle->status,
                'startTime' => $battle->start_time * 1000,
                'endTime' => $battle->end_time ? $battle->end_time * 1000 : 0
            ];
        }
        
        $this->success('ok', $data);
    }
}
