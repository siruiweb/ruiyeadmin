<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 初心碑API
 */
class Initial extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 初心题目列表
     */
    private $questions = [
        [
            'id' => 1,
            'text' => '你为何要踏上修仙之路？',
            'placeholder' => '例如：为了长生不老，为了守护所爱之人，为了探索天地奥秘...'
        ],
        [
            'id' => 2,
            'text' => '修仙途中遇到挫折，你会如何抉择？',
            'placeholder' => '例如：坚持不懈，迎难而上；调整心态，顺势而为...'
        ],
        [
            'id' => 3,
            'text' => '你认为修仙最重要的是什么？',
            'placeholder' => '例如：心性、资质、资源、机缘、还是气运...'
        ]
    ];

    /**
     * 获取初心题目
     */
    public function questions()
    {
        $this->success('获取成功', [
            'questions' => $this->questions
        ]);
    }

    /**
     * 提交初心回答
     */
    public function submit()
    {
        $questionId = $this->request->post('question_id', 0, 'intval');
        $answer = $this->request->post('answer', '', 'trim');
        
        if (empty($answer)) {
            $this->error('回答不能为空');
        }
        
        // 保存回答逻辑
        
        $this->success('提交成功', [
            'question_id' => $questionId,
            'answer' => $answer
        ]);
    }

    /**
     * 获取我的初心回答
     */
    public function my()
    {
        $answers = [];
        
        $this->success('获取成功', [
            'answers' => $answers
        ]);
    }

    /**
     * 获取初心碑铭文
     */
    public function inscriptions()
    {
        $inscriptions = [
            [
                'id' => 1,
                'avatar' => '🧙‍♂️',
                'name' => '太上长老',
                'inscription' => '修仙一途，道心为本。初心不忘，方得始终。',
                'realm' => '大乘期',
                'time' => time() - 8640000
            ],
            [
                'id' => 2,
                'avatar' => '🧝‍♀️',
                'name' => '瑶池仙子',
                'inscription' => '愿以一生修仙，换取长生不老，与有情人共赴永恒。',
                'realm' => '渡劫期',
                'time' => time() - 17280000
            ],
            [
                'id' => 3,
                'avatar' => '🦸',
                'name' => '剑圣',
                'inscription' => '一剑破万法，一心证大道。剑在手，天下我有。',
                'realm' => '合体期',
                'time' => time() - 25920000
            ]
        ];
        
        $this->success('获取成功', [
            'inscriptions' => $inscriptions
        ]);
    }

    /**
     * 保存初心碑铭文
     */
    public function save()
    {
        $inscription = $this->request->post('inscription', '', 'trim');
        
        if (empty($inscription)) {
            $this->error('铭文内容不能为空');
        }
        
        // 保存铭文逻辑
        
        $this->success('保存成功', [
            'inscription' => $inscription,
            'time' => time()
        ]);
    }
}
