<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 诸天仙途 - 剧情系统API
 * 处理剧情选择、NPC交互等
 */
class Story extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 获取剧情章节
     */
    public function chapters()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $identityType = $this->request->get('identity_type', 'sanxiu', 'trim');
        
        $chapters = [
            [
                'id' => 1,
                'name' => '初入仙途',
                'identity_type' => 'sanxiu',
                'description' => '你来到牧羊山脚下的青云镇，开始了你的修仙之路...',
                'status' => 'in_progress',
                'progress' => 50,
                'stages' => 5
            ],
            [
                'id' => 2,
                'name' => '练体初成',
                'identity_type' => 'sanxiu',
                'description' => '经过一段时间的修炼，你的身体素质有了显著提升...',
                'status' => 'locked',
                'progress' => 0,
                'stages' => 5,
                'unlock_level' => 5
            ],
            [
                'id' => 3,
                'name' => '筑基之路',
                'identity_type' => 'sanxiu',
                'description' => '你即将突破到筑基境界，这是修仙的第一道门槛...',
                'status' => 'locked',
                'progress' => 0,
                'stages' => 5,
                'unlock_level' => 10
            ],
            [
                'id' => 4,
                'name' => '残魂觉醒',
                'identity_type' => 'duoshe',
                'description' => '你在一具凡人体内苏醒，前世的记忆残缺不全...',
                'status' => 'in_progress',
                'progress' => 30,
                'stages' => 5
            ],
            [
                'id' => 5,
                'name' => '前世纠葛',
                'identity_type' => 'duoshe',
                'description' => '你的残魂逐渐恢复，开始回忆起前世的恩怨...',
                'status' => 'locked',
                'progress' => 0,
                'stages' => 5,
                'unlock_level' => 5
            ]
        ];
        
        // 筛选身份对应的剧情
        $filteredChapters = array_filter($chapters, function($chapter) use ($identityType) {
            return $chapter['identity_type'] == $identityType || $chapter['identity_type'] == 'sanxiu';
        });
        
        $this->success('获取成功', [
            'chapters' => array_values($filteredChapters),
            'current_chapter' => 1
        ]);
    }

    /**
     * 获取剧情阶段
     */
    public function stage()
    {
        $chapterId = $this->request->get('chapter_id', 1, 'intval');
        $stageId = $this->request->get('stage_id', 1, 'intval');
        
        $stage = [
            'id' => $stageId,
            'chapter_id' => $chapterId,
            'title' => '寻找修炼之地',
            'description' => '你来到青云镇外，准备寻找一处适合修炼的地方。',
            'background' => 'qingyun_town_outside',
            'characters' => [
                ['id' => 'player', 'name' => '你', 'avatar' => '', 'is_narrator' => false],
                ['id' => 'village_head', 'name' => '老村长', 'avatar' => '', 'is_narrator' => false]
            ],
            'dialogues' => [
                [
                    'speaker' => 'narrator',
                    'text' => '青云镇外，山清水秀，灵气充沛。'
                ],
                [
                    'speaker' => 'player',
                    'text' => '这里就是牧羊山吗？感觉灵气确实比凡间浓郁。'
                ],
                [
                    'speaker' => 'village_head',
                    'text' => '年轻人，你也是来修仙的吧？我看你灵根资质不错。'
                ]
            ],
            'choices' => [
                [
                    'id' => 'choice_1',
                    'text' => '请问老丈，修仙应该从哪里开始？',
                    'effects' => ['npc_relation_village_head' => 5]
                ],
                [
                    'id' => 'choice_2',
                    'text' => '老人家，您知道哪里有好的修炼功法吗？',
                    'effects' => ['npc_relation_village_head' => 3, 'unlock_shop' => 'skill_shop']
                ],
                [
                    'id' => 'choice_3',
                    'text' => '多谢指点，我先告辞了。',
                    'effects' => ['npc_relation_village_head' => -5]
                ]
            ],
            'tasks' => [
                ['id' => 'task_1_1', 'name' => '寻找修炼之地', 'desc' => '在青云镇外找到一处适合修炼的场所']
            ],
            'type' => 'story' // story/dialogue/choice/battle
        ];
        
        $this->success('获取成功', $stage);
    }

    /**
     * 提交剧情选择
     */
    public function choice()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $chapterId = $this->request->post('chapter_id', 1, 'intval');
        $stageId = $this->request->post('stage_id', 1, 'intval');
        $choiceId = $this->request->post('choice_id', '', 'trim');
        
        if (empty($choiceId)) {
            $this->error('选择ID不能为空');
        }
        
        // 记录选择效果
        $effects = [
            'npc_relations' => ['village_head' => 5],
            'unlocks' => [],
            'flags' => ['first_meeting_done' => true]
        ];
        
        $result = [
            'chapter_id' => $chapterId,
            'stage_id' => $stageId,
            'choice_id' => $choiceId,
            'effects' => $effects,
            'next_stage_id' => $stageId + 1,
            'is_chapter_end' => false
        ];
        
        $this->success('选择成功', $result);
    }

    /**
     * 获取NPC列表
     */
    public function npcs()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $location = $this->request->get('location', 'all', 'trim');
        
        $npcs = [
            [
                'id' => 'village_head',
                'name' => '老村长',
                'avatar' => '',
                'level' => 3,
                'title' => '青云镇村长',
                'location' => 'qingyun_town',
                'type' => 'quest',
                'description' => '青云镇德高望重的老者，了解很多修仙界的知识。',
                'relationship' => 10,
                'dialogues' => [
                    ['id' => 'greet', 'text' => '是年轻人啊，有什么事吗？'],
                    ['id' => 'quest', 'text' => '我这里有一个任务，不知道你能否帮忙...']
                ]
            ],
            [
                'id' => 'blacksmith',
                'name' => '铁匠老张',
                'avatar' => '',
                'level' => 2,
                'title' => '青云镇铁匠',
                'location' => 'qingyun_town',
                'type' => 'shop',
                'description' => '青云镇唯一的铁匠，可以打造简单的武器和防具。',
                'relationship' => 0,
                'shop_type' => 'equipment'
            ],
            [
                'id' => 'alchemist',
                'name' => '神秘炼丹师',
                'avatar' => '',
                'level' => 5,
                'title' => '神秘炼丹师',
                'location' => 'muyao_mountain',
                'type' => 'shop',
                'description' => '隐居山林的神秘炼丹师，很少与人交流。',
                'relationship' => 0,
                'shop_type' => 'pills'
            ],
            [
                'id' => 'former_disciple',
                'name' => '前世道童',
                'avatar' => '',
                'level' => 4,
                'title' => '忠心道童',
                'location' => 'qingyun_town',
                'type' => 'guide',
                'description' => '前世收的道童，一直忠心耿耿地跟随你。',
                'relationship' => 50,
                'is_hidden' => true
            ]
        ];
        
        // 筛选位置
        if ($location != 'all') {
            $npcs = array_filter($npcs, function($npc) use ($location) {
                return $npc['location'] == $location;
            });
        }
        
        $this->success('获取成功', ['npcs' => array_values($npcs)]);
    }

    /**
     * NPC对话
     */
    public function dialogue()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $npcId = $this->request->post('npc_id', '', 'trim');
        $dialogueId = $this->request->post('dialogue_id', '', 'trim');
        
        $dialogue = [
            'speaker' => $npcId,
            'text' => '修仙一途，道心为本。只要你保持初心，必定能有所成就。',
            'responses' => [
                ['id' => 'resp_1', 'text' => '多谢前辈指点。', 'effect' => ['relationship' => 2]],
                ['id' => 'resp_2', 'text' => '请问有什么我可以帮忙的吗？', 'effect' => ['unlock_quest' => true]],
                ['id' => 'resp_3', 'text' => '告辞了。', 'effect' => ['end' => true]]
            ],
            'rewards' => ['daojin' => 10]
        ];
        
        $this->success('获取成功', $dialogue);
    }

    /**
     * 获取剧情回顾
     */
    public function history()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $page = $this->request->get('page', 1, 'intval');
        
        $history = [
            [
                'id' => 1,
                'chapter_id' => 1,
                'chapter_name' => '初入仙途',
                'stage_id' => 1,
                'stage_name' => '来到青云镇',
                'choice_made' => '询问修仙之道',
                'time' => time() - 3600
            ]
        ];
        
        $this->success('获取成功', [
            'history' => $history,
            'total' => count($history)
        ]);
    }

    /**
     * 重播剧情
     */
    public function replay()
    {
        $historyId = $this->request->get('history_id', 0, 'intval');
        
        $replay = [
            'chapter_id' => 1,
            'stage_id' => 1,
            'dialogues' => [],
            'choices_made' => []
        ];
        
        $this->success('获取成功', $replay);
    }
}
