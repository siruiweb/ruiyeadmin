<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 诸天仙途 - 套装系统API
 * 处理套装数据、激活、强化等功能
 */
class Suit extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 获取套装列表
     */
    public function data()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        $suits = [
            [
                'id' => 'suit_001',
                'code' => 'qiankun_yinying',
                'name' => '乾坤阴阳套装',
                'description' => '蕴含乾坤之力的阴阳套装，攻防兼备',
                'pieces' => 4,
                'rarity' => 'legend',
                'bonus' => [
                    ['pieces' => 2, 'effect' => '生命+500', 'stats' => ['hp' => 500]],
                    ['pieces' => 4, 'effect' => '全属性+20%', 'stats' => ['atk_pct' => 20, 'def_pct' => 20]]
                ]
            ],
            [
                'id' => 'suit_002',
                'code' => 'wuxing_tianluo',
                'name' => '五行天罗套装',
                'description' => '五行之力汇聚的套装，攻守均衡',
                'pieces' => 3,
                'rarity' => 'epic',
                'bonus' => [
                    ['pieces' => 2, 'effect' => '攻击+200', 'stats' => ['attack' => 200]],
                    ['pieces' => 3, 'effect' => '暴击率+15%', 'stats' => ['crit_rate' => 15]]
                ]
            ],
            [
                'id' => 'suit_003',
                'code' => 'tiandao_baiqi',
                'name' => '天道白起套装',
                'description' => '上古战神白起的遗留套装',
                'pieces' => 5,
                'rarity' => 'legend',
                'bonus' => [
                    ['pieces' => 3, 'effect' => '生命+1000', 'stats' => ['hp' => 1000]],
                    ['pieces' => 5, 'effect' => '绝境伤害+50%', 'stats' => ['desperation_damage' => 50]]
                ]
            ]
        ];
        
        $this->success('获取成功', [
            'suits' => $suits,
            'equipped_suit' => null
        ]);
    }

    /**
     * 激活套装
     */
    public function active()
    {
        $playerId = $this->auth->id ?? 0;
        $suitId = $this->request->post('suit_id', '', 'trim');
        
        if (empty($suitId)) {
            $this->error('套装ID不能为空');
        }
        
        $this->success('激活成功', [
            'suit_id' => $suitId,
            'equipped' => true
        ]);
    }

    /**
     * 强化套装
     */
    public function upgrade()
    {
        $playerId = $this->auth->id ?? 0;
        $suitId = $this->request->post('suit_id', '', 'trim');
        $itemId = $this->request->post('item_id', '', 'trim');
        
        if (empty($suitId)) {
            $this->error('套装ID不能为空');
        }
        
        $this->success('强化成功', [
            'suit_id' => $suitId,
            'level' => 2
        ]);
    }

    /**
     * 获取已激活的套装
     */
    public function equipped()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        $this->success('获取成功', [
            'equipped' => null
        ]);
    }
}
