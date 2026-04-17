<?php

namespace app\api\controller;

/**
 * 宗门战接口 X302/X308
 */
class Guildwar extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     * 获取宗门战状态
     */
    public function status()
    {
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        // 获取玩家宗门
        $guild = \app\common\model\Guild::getByLeaderId($playerId);
        
        if (!$guild) {
            $this->success('ok', [
                'myGuild' => null,
                'currentWar' => null,
                'season' => $this->getSeasonInfo()
            ]);
        }
        
        // 当前战斗
        $currentWar = \app\common\model\GuildWar::where('status', 'ongoing')
            ->whereOr('status', 'preparing')
            ->where(function ($query) use ($guild) {
                $query->where('attacker_id', $guild->id)
                    ->whereOr('defender_id', $guild->id);
            })
            ->find();
        
        // 获取防守布置
        $defenseLayout = \app\common\model\GuildDefense::where('guild_id', $guild->id)
            ->order('position', 'asc')
            ->select();
        
        $data = [
            'myGuild' => [
                'id' => $guild->id,
                'name' => $guild->name,
                'icon' => $guild->icon,
                'level' => $guild->level,
                'memberCount' => $guild->member_count,
                'maxMembers' => $guild->max_members,
                'totalCombat' => $guild->total_combat,
                'rank' => $guild->rank,
                'funds' => $guild->funds,
                'weeklyWin' => $guild->weekly_win,
                'weeklyLose' => $guild->weekly_lose,
                'totalWin' => $guild->total_win,
                'seasonRank' => $guild->season_rank,
                'seasonPoints' => $guild->season_points
            ],
            'currentWar' => $currentWar ? [
                'id' => $currentWar->id,
                'type' => $currentWar->war_type,
                'status' => $currentWar->status,
                'progress' => $currentWar->progress,
                'remainingTime' => $currentWar->remaining_time,
                'attacker' => [
                    'id' => $currentWar->attacker_id,
                    'name' => $currentWar->attacker_name,
                    'icon' => $currentWar->attacker_icon,
                    'score' => $currentWar->attacker_score
                ],
                'defender' => [
                    'id' => $currentWar->defender_id,
                    'name' => $currentWar->defender_name,
                    'icon' => $currentWar->defender_icon,
                    'score' => $currentWar->defender_score
                ]
            ] : null,
            'season' => $this->getSeasonInfo(),
            'defenseLayout' => $defenseLayout ? array_map(function($d) {
                return [
                    'position' => $d->position,
                    'memberId' => $d->member_id,
                    'type' => $d->defense_type
                ];
            }, $defenseLayout->toArray()) : null
        ];
        
        // 宗门成员
        $members = \app\common\model\GuildMember::where('guild_id', $guild->id)
            ->select();
        
        $data['members'] = array_map(function($m) {
            return [
                'id' => $m->player_id,
                'name' => $m->player_name,
                'level' => $m->realm_level,
                'levelName' => $m->realm_name,
                'combat' => $m->combat,
                'position' => $m->position,
                'contribution' => $m->contribution
            ];
        }, $members->toArray());
        
        $this->success('ok', $data);
    }

    /**
     * 获取可宣战宗门列表
     */
    public function list()
    {
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        $myGuild = \app\common\model\Guild::getByLeaderId($playerId);
        
        if (!$myGuild) {
            $this->error('未加入宗门');
        }
        
        // 获取可宣战宗门
        $guilds = \app\common\model\Guild::where('id', '<>', $myGuild->id)
            ->where('status', 'normal')
            ->order('rank', 'asc')
            ->limit(20)
            ->select();
        
        $data = array_map(function($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'icon' => $g->icon,
                'rank' => $g->rank,
                'combat' => $g->total_combat,
                'memberCount' => $g->member_count,
                'winRate' => $g->total_win > 0 ? round($g->total_win / ($g->total_win + $g->total_lose) * 100, 1) : 0
            ];
        }, $guilds->toArray());
        
        $this->success('ok', $data);
    }

    /**
     * 发起宣战
     */
    public function declare()
    {
        $targetId = $this->request->post('target_id');
        $warTime = $this->request->post('war_time', 'tomorrow');
        
        if (!$targetId) {
            $this->error('请选择目标宗门');
        }
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        $myGuild = \app\common\model\Guild::getByLeaderId($playerId);
        
        if (!$myGuild) {
            $this->error('未加入宗门');
        }
        
        $targetGuild = \app\common\model\Guild::get($targetId);
        
        if (!$targetGuild) {
            $this->error('目标宗门不存在');
        }
        
        // 检查押金
        $cost = 100000;
        if ($player->lingshi < $cost) {
            $this->error('灵石不足');
        }
        
        // 扣除押金
        $player->lingshi -= $cost;
        $player->save();
        
        // 创建宣战记录
        $war = new \app\common\model\GuildWar();
        $war->attacker_id = $myGuild->id;
        $war->attacker_name = $myGuild->name;
        $war->attacker_icon = $myGuild->icon;
        $war->defender_id = $targetGuild->id;
        $war->war_type = 'guild';
        $war->status = 'preparing';
        $war->scheduled_time = strtotime('tomorrow 20:00');
        $war->createtime = time();
        
        $war->save();
        
        $this->success('宣战成功');
    }

    /**
     * 加入战斗
     */
    public function join()
    {
        $warId = $this->request->post('war_id');
        $side = $this->request->post('side', 'attacker');
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        $war = \app\common\model\GuildWar::get($warId);
        
        if (!$war) {
            $this->error('战斗不存在');
        }
        
        // 添加到参战列表
        $field = $side === 'attacker' ? 'attacker_members' : 'defender_members';
        $members = json_decode($war->$field, true) ?: [];
        
        $members[] = [
            'playerId' => $player->id,
            'name' => $player->player_name,
            'level' => $player->realm_level,
            'levelName' => $player->realm_name,
            'combat' => $player->attack + $player->defense + $player->health
        ];
        
        $war->$field = json_encode($members);
        $war->save();
        
        $this->success('加入成功');
    }

    /**
     * 获取战报
     */
    public function report()
    {
        $playerId = $this->auth->id;
        
        $battles = \app\common\model\GuildWar::where('status', 'ended')
            ->order('end_time', 'desc')
            ->limit(10)
            ->select();
        
        $data = array_map(function($b) {
            return [
                'id' => $b->id,
                'title' => $b->attacker_name . ' VS ' . $b->defender_name,
                'time' => date('Y-m-d H:i', $b->end_time ?: $b->createtime),
                'attacker' => [
                    'id' => $b->attacker_id,
                    'name' => $b->attacker_name,
                    'icon' => $b->attacker_icon
                ],
                'defender' => [
                    'id' => $b->defender_id,
                    'name' => $b->defender_name,
                    'icon' => $b->defender_icon
                ],
                'attackerScore' => $b->attacker_score,
                'defenderScore' => $b->defender_score,
                'result' => $b->winner_id == $b->attacker_id ? 'win' : 'lose'
            ];
        }, $battles->toArray());
        
        $this->success('ok', $data);
    }

    /**
     * 获取战况统计
     */
    public function stats()
    {
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        $guild = \app\common\model\Guild::getByLeaderId($playerId);
        
        if (!$guild) {
            $this->success('ok', [
                'totalBattles' => 0,
                'wins' => 0,
                'losses' => 0,
                'myContribution' => 0
            ]);
        }
        
        $this->success('ok', [
            'totalBattles' => $guild->weekly_win + $guild->weekly_lose,
            'wins' => $guild->weekly_win,
            'losses' => $guild->weekly_lose,
            'myContribution' => $player->guild_contribution ?? 0
        ]);
    }

    /**
     * 保存防守布置
     */
    public function saveDefense()
    {
        $layout = $this->request->post('layout');
        
        if (!$layout) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        $guild = \app\common\model\Guild::getByLeaderId($playerId);
        
        if (!$guild) {
            $this->error('未加入宗门');
        }
        
        $positions = json_decode($layout, true);
        
        foreach ($positions as $pos) {
            $defense = \app\common\model\GuildDefense::where('guild_id', $guild->id)
                ->where('position', $pos['position'])
                ->find();
            
            if (!$defense) {
                $defense = new \app\common\model\GuildDefense();
                $defense->guild_id = $guild->id;
                $defense->position = $pos['position'];
            }
            
            $defense->member_id = $pos['memberId'];
            $defense->defense_type = $pos['type'];
            $defense->save();
        }
        
        $this->success('保存成功');
    }

    /**
     * 获取赛季信息
     */
    public function season()
    {
        $this->success('ok', $this->getSeasonInfo());
    }

    /**
     * 获取赛季排行
     */
    public function seasonRanking()
    {
        $guilds = \app\common\model\Guild::order('season_points', 'desc')
            ->limit(50)
            ->select();
        
        $data = array_map(function($g, $index) {
            return [
                'rank' => $index + 1,
                'id' => $g->id,
                'name' => $g->name,
                'icon' => $g->icon,
                'points' => $g->season_points
            ];
        }, $guilds->toArray());
        
        $this->success('ok', $data);
    }

    /**
     * 获取赛季奖励
     */
    public function seasonRewards()
    {
        $rewards = [
            ['rank' => 1, 'name' => '第一名', 'rewards' => [['type' => 'lingshi', 'amount' => 1000000], ['type' => 'title', 'name' => '天下第一']]],
            ['rank' => 2, 'name' => '第二名', 'rewards' => [['type' => 'lingshi', 'amount' => 500000], ['type' => 'title', 'name' => '天下第二']]],
            ['rank' => 3, 'name' => '第三名', 'rewards' => [['type' => 'lingshi', 'amount' => 300000], ['type' => 'title', 'name' => '天下第三']]],
            ['rank' => '4-10', 'name' => '前10名', 'rewards' => [['type' => 'lingshi', 'amount' => 100000]]],
            ['rank' => '11-50', 'name' => '前50名', 'rewards' => [['type' => 'lingshi', 'amount' => 50000]]]
        ];
        
        $this->success('ok', $rewards);
    }

    /**
     * 领取赛季奖励
     */
    public function claimSeasonReward()
    {
        $rewardId = $this->request->post('reward_id');
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        $guild = \app\common\model\Guild::getByLeaderId($playerId);
        
        if (!$guild) {
            $this->error('未加入宗门');
        }
        
        // 检查是否满足领取条件
        $claimed = json_decode($guild->claimed_rewards, true) ?: [];
        
        if (in_array($rewardId, $claimed)) {
            $this->error('已领取');
        }
        
        // 发放奖励
        $player->lingshi += 100000;
        $player->save();
        
        $claimed[] = $rewardId;
        $guild->claimed_rewards = json_encode($claimed);
        $guild->save();
        
        $this->success('领取成功');
    }

    /**
     * 获取赛季里程碑
     */
    public function seasonMilestones()
    {
        $milestones = [
            ['id' => 1, 'name' => '初战告捷', 'description' => '赢得第一场宗门战', 'progress' => 0, 'target' => 1, 'reward' => ['type' => 'lingshi', 'amount' => 10000]],
            ['id' => 2, 'name' => '百战百胜', 'description' => '累计赢得10场宗门战', 'progress' => 0, 'target' => 10, 'reward' => ['type' => 'lingshi', 'amount' => 50000]],
            ['id' => 3, 'name' => '常胜将军', 'description' => '累计赢得50场宗门战', 'progress' => 0, 'target' => 50, 'reward' => ['type' => 'lingshi', 'amount' => 200000]],
            ['id' => 4, 'name' => '战无不胜', 'description' => '累计赢得100场宗门战', 'progress' => 0, 'target' => 100, 'reward' => ['type' => 'title', 'name' => '战神']]
        ];
        
        $this->success('ok', $milestones);
    }

    /**
     * 领取赛季里程碑奖励
     */
    public function claimMilestone()
    {
        $milestoneId = $this->request->post('milestone_id');
        
        if (!$milestoneId) {
            $this->error('参数错误');
        }
        
        $this->success('领取成功');
    }

    /**
     * 获取赛季信息内部方法
     */
    private function getSeasonInfo()
    {
        return [
            'name' => '第一赛季',
            'startTime' => '2026-03-01',
            'endTime' => '2026-03-31',
            'unclaimedRewards' => 0,
            'totalWars' => 0,
            'wins' => 0,
            'losses' => 0
        ];
    }
}
