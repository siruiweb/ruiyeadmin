<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 天道系统API
 * X302-X303: 天道问答系统
 * 处理天道对话、问答题目、答案提交等
 */
class Tiandao extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 天道问答题目库 - 按境界分级
     */
    private $questionsByLevel = [
        // 凡人阶段 (level 1)
        1 => [
            [
                'id' => 101,
                'text' => '你为何踏上修仙之路？',
                'hint' => '请仔细思考后作答',
                'options' => ['追求力量', '逆天改命', '顺应天道', '长生不老'],
                'answer' => 2,
                'type' => 'choice'
            ],
            [
                'id' => 102,
                'text' => '以下哪个不是修仙所需的？',
                'hint' => '修仙的基础是什么？',
                'options' => ['灵根', '灵气', '功法', '金钱'],
                'answer' => 3,
                'type' => 'choice'
            ],
            [
                'id' => 103,
                'text' => '灵根的属性包括？',
                'hint' => '五行属性是哪些？',
                'options' => ['金木水火土', '天地人和', '日月星', '风雷雨电'],
                'answer' => 0,
                'type' => 'choice'
            ],
            [
                'id' => 104,
                'text' => '筑基期修士最主要的修炼方式是？',
                'hint' => '基础修炼方法',
                'options' => ['服用丹药', '打坐修炼', '战斗历练', '沉睡'],
                'answer' => 1,
                'type' => 'choice'
            ],
            [
                'id' => 105,
                'text' => '修仙界最看重的是什么？',
                'hint' => '决定修仙潜力的关键',
                'options' => ['实力', '资质', '气运', '资源'],
                'answer' => 1,
                'type' => 'choice'
            ],
            [
                'id' => 106,
                'text' => '灵石的主要用途是？',
                'hint' => '修仙界的通用货币',
                'options' => ['购买装备', '辅助修炼', '交易媒介', '以上都是'],
                'answer' => 3,
                'type' => 'choice'
            ]
        ],
        // 筑基阶段 (level 2)
        2 => [
            [
                'id' => 201,
                'text' => '修仙之根本在于？',
                'hint' => '天道的本质是什么？',
                'options' => ['追求力量', '逆天改命', '顺应天道', '长生不老'],
                'answer' => 2,
                'type' => 'choice'
            ],
            [
                'id' => 202,
                'text' => '以下哪个是修仙大忌？',
                'hint' => '应避免的行为',
                'options' => ['修炼', '杀戮', '炼丹', '阵法'],
                'answer' => 1,
                'type' => 'choice'
            ],
            [
                'id' => 203,
                'text' => '修仙者突破境界需要？',
                'hint' => '突破的关键因素',
                'options' => ['大量灵石', '感悟天道', '战斗经验', '灵丹妙药'],
                'answer' => 1,
                'type' => 'choice'
            ],
            [
                'id' => 204,
                'text' => '灵宠对修仙者的帮助是？',
                'hint' => '灵宠的作用',
                'options' => ['增加战力', '探测敌情', '代步工具', '以上都是'],
                'answer' => 3,
                'type' => 'choice'
            ],
            [
                'id' => 205,
                'text' => '丹药服用的禁忌是？',
                'hint' => '炼丹修仙知识',
                'options' => ['空腹服用', '过量服用', '心有杂念', '以上皆是'],
                'answer' => 3,
                'type' => 'choice'
            ]
        ],
        // 结丹及以上 (level 3+)
        3 => [
            [
                'id' => 301,
                'text' => '何为道？',
                'hint' => '请阐述你对道的理解',
                'options' => ['天地法则', '万物本源', '心中的信念', '以上皆是'],
                'answer' => 3,
                'type' => 'choice'
            ],
            [
                'id' => 302,
                'text' => '渡劫时最重要的是？',
                'hint' => '雷劫考验的关键',
                'options' => ['法器防护', '心性坚定', '丹药辅助', '同伴护法'],
                'answer' => 1,
                'type' => 'choice'
            ],
            [
                'id' => 303,
                'text' => '成仙的关键是什么？',
                'hint' => '超脱凡俗的要素',
                'options' => ['实力强横', '功德圆满', '气运加身', '三者缺一不可'],
                'answer' => 3,
                'type' => 'choice'
            ],
            [
                'id' => 304,
                'text' => '天道轮回的规律是？',
                'hint' => '宇宙运行的法则',
                'options' => ['因果报应', '生死循环', '阴阳平衡', '以上皆是'],
                'answer' => 3,
                'type' => 'choice'
            ]
        ]
    ];

    /**
     * 自由问答题目
     */
    private $freeQuestions = [
        [
            'id' => 901,
            'text' => '你修行的初心是什么？',
            'placeholder' => '请写下你的初心...',
            'type' => 'free'
        ],
        [
            'id' => 902,
            'text' => '修仙路上，你最珍视的是什么？',
            'placeholder' => '请自由回答...',
            'type' => 'free'
        ],
        [
            'id' => 903,
            'text' => '如果你遇到不公，你会如何抉择？',
            'placeholder' => '请阐述你的立场...',
            'type' => 'free'
        ]
    ];

    /**
     * 获取天道问答题目 X303
     * GET /api/achievement/tiandaoQuestions
     */
    public function questions()
    {
        $level = $this->request->get('level', 1, 'intval');
        
        // 根据等级获取对应题目
        $questions = [];
        if ($level <= 1 && isset($this->questionsByLevel[1])) {
            $questions = $this->questionsByLevel[1];
        } elseif ($level == 2 && isset($this->questionsByLevel[2])) {
            $questions = $this->questionsByLevel[2];
        } elseif ($level >= 3 && isset($this->questionsByLevel[3])) {
            $questions = $this->questionsByLevel[3];
        }
        
        // 合并自由问答题目
        $questions = array_merge($questions, $this->freeQuestions);
        
        // 打乱顺序
        shuffle($questions);
        
        // 只返回5道题目
        $selectedQuestions = array_slice($questions, 0, 5);
        
        // 不返回正确答案
        foreach ($selectedQuestions as &$q) {
            unset($q['answer']);
        }
        
        $this->success('获取成功', [
            'level' => $level,
            'questions' => $selectedQuestions,
            'total' => count($selectedQuestions)
        ]);
    }

    /**
     * 提交天道问答答案 X303
     * POST /api/achievement/tiandaoSubmit
     */
    public function submit()
    {
        $questionId = $this->request->post('question_id', 0, 'intval');
        $answerIndex = $this->request->post('answer_index', -1, 'intval');
        $answerText = $this->request->post('answer_text', '', 'trim');
        
        // 查找题目
        $question = null;
        foreach ($this->questionsByLevel as $levelQuestions) {
            foreach ($levelQuestions as $q) {
                if ($q['id'] == $questionId) {
                    $question = $q;
                    break 2;
                }
            }
        }
        
        // 检查是否是自由问答
        foreach ($this->freeQuestions as $fq) {
            if ($fq['id'] == $questionId) {
                $question = $fq;
                break;
            }
        }
        
        if (!$question) {
            $this->error('题目不存在');
        }
        
        $isCorrect = false;
        $reward = [];
        
        // 判断答案是否正确
        if ($question['type'] === 'choice' && isset($question['answer'])) {
            $isCorrect = ($answerIndex == $question['answer']);
        } else {
            // 自由问答根据内容长度判断
            $isCorrect = (mb_strlen($answerText) >= 5);
        }
        
        if ($isCorrect) {
            $reward = [
                'dao_xin' => rand(10, 20),  // 悟道点
                'daojin' => rand(10, 30),   // 道金
                'exp' => rand(50, 150)      // 修为
            ];
        }
        
        $result = [
            'correct' => $isCorrect,
            'reward' => $reward,
            'dao_xin_gained' => $reward['dao_xin'] ?? 0
        ];
        
        $this->success('提交成功', $result);
    }

    /**
     * 获取天道问答记录 X303
     * GET /api/achievement/tiandaoRecords
     */
    public function records()
    {
        $playerId = $this->auth->id ?? 0;
        
        // 模拟记录数据
        $records = [];
        
        // 如果有数据库，从数据库读取
        // $records = \app\common\model\TiandaoRecord::where('player_id', $playerId)
        //     ->order('create_time', 'desc')
        //     ->limit(50)
        //     ->select();
        
        $this->success('获取成功', [
            'records' => $records,
            'total' => count($records)
        ]);
    }

    /**
     * 获取天道问答统计 X303
     * GET /api/achievement/tiandaoStats
     */
    public function stats()
    {
        $playerId = $this->auth->id ?? 0;
        
        // 模拟统计数据
        $stats = [
            'total_answered' => 0,
            'correct_count' => 0,
            'wrong_count' => 0,
            'accuracy' => 0,
            'dao_xin' => 0,
            'streak' => 0  // 连续答对
        ];
        
        $this->success('获取成功', $stats);
    }

    /**
     * 获取天道信息 X302
     * GET /api/achievement/tiandaoInfo
     */
    public function info()
    {
        $level = $this->request->get('level', 1, 'intval');
        
        $phases = ['凡尘', '筑基', '结丹', '元婴', '化神', '大乘', '渡劫', '真仙'];
        $phaseName = $phases[min($level - 1, count($phases) - 1)];
        
        $greetings = [
            1 => '凡尘小儿，汝为何求道？',
            2 => '筑基修士，汝之道心如何？',
            3 => '金丹真人，汝可悟透天道？',
            4 => '元婴老怪，汝离真仙还有多远？',
            5 => '化神大能，汝可窥见天道全貌？',
            6 => '大乘尊者，汝距飞升仅一步之遥！',
            7 => '渡劫准仙，汝可承受天道之怒？',
            8 => '真仙降世，与天道同寿！'
        ];
        
        $this->success('获取成功', [
            'level' => $level,
            'phase' => $phaseName,
            'greeting' => $greetings[min($level, count($greetings))] ?? $greetings[1],
            'daily_questions' => 5,
            'available' => true
        ]);
    }

    /**
     * 获取悟道商城商品 X302
     * GET /api/achievement/tiandaoShop
     */
    public function shop()
    {
        $goods = [
            ['id' => 1, 'name' => '天道感悟', 'desc' => '随机领悟一项天道法则', 'price' => 100, 'icon' => '🌟'],
            ['id' => 2, 'name' => '悟道丹', 'desc' => '服用后修炼速度+20%', 'price' => 200, 'icon' => '💊'],
            ['id' => 3, 'name' => '天眼符', 'desc' => '窥探敌人弱点', 'price' => 150, 'icon' => '👁️'],
            ['id' => 4, 'name' => '命运石', 'desc' => '改变一次命运轨迹', 'price' => 500, 'icon' => '💎']
        ];
        
        $this->success('获取成功', [
            'goods' => $goods
        ]);
    }
}
