<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 聊天系统API
 * 处理世界聊天、私聊、宗门聊天等
 */
class Chat extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 获取聊天消息
     */
    public function messages()
    {
        $channel = $this->request->get('channel', 'world', 'trim');
        $lastId = $this->request->get('last_id', 0, 'intval');
        $limit = $this->request->get('limit', 20, 'intval');
        
        $messages = [];
        
        // 模拟消息数据
        $sampleMessages = [
            [
                'id' => 1001,
                'player_id' => 10001,
                'player_name' => '太上长老',
                'player_avatar' => '',
                'content' => '新来的修士们，修仙路漫漫，道心为本。',
                'type' => 'text',
                'channel' => 'world',
                'time' => time() - 300
            ],
            [
                'id' => 1002,
                'player_id' => 10002,
                'player_name' => '瑶池仙子',
                'player_avatar' => '',
                'content' => '有没有人一起组队刷副本？',
                'type' => 'text',
                'channel' => 'world',
                'time' => time() - 200
            ],
            [
                'id' => 1003,
                'player_id' => 10003,
                'player_name' => '剑圣',
                'player_avatar' => '',
                'content' => '出售极品功法，价高者得！',
                'type' => 'text',
                'channel' => 'world',
                'time' => time() - 100
            ]
        ];
        
        $this->success('获取成功', [
            'messages' => $sampleMessages,
            'has_more' => false,
            'last_id' => 1003
        ]);
    }

    /**
     * 发送消息
     */
    public function send()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $channel = $this->request->post('channel', 'world', 'trim');
        $content = $this->request->post('content', '', 'trim');
        $receiverId = $this->request->post('receiver_id', 0, 'intval');
        
        if (empty($content)) {
            $this->error('消息内容不能为空');
        }
        
        // 检查禁言状态
        // 检查发送间隔
        // 检查敏感词
        
        $message = [
            'id' => time(),
            'player_id' => $playerId,
            'player_name' => '玩家',
            'player_avatar' => '',
            'content' => $content,
            'type' => 'text',
            'channel' => $channel,
            'time' => time()
        ];
        
        $this->success('发送成功', $message);
    }

    /**
     * 私聊消息
     */
    public function privateMessages()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        $targetId = $this->request->get('target_id', 0, 'intval');
        $lastId = $this->request->get('last_id', 0, 'intval');
        
        $messages = [];
        
        $this->success('获取成功', [
            'messages' => $messages,
            'has_more' => false
        ]);
    }

    /**
     * 发送私聊
     */
    public function sendPrivate()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $receiverId = $this->request->post('receiver_id', 0, 'intval');
        $content = $this->request->post('content', '', 'trim');
        
        if (empty($content)) {
            $this->error('消息内容不能为空');
        }
        
        if (empty($receiverId)) {
            $this->error('接收者ID不能为空');
        }
        
        $message = [
            'id' => time(),
            'player_id' => $playerId,
            'receiver_id' => $receiverId,
            'content' => $content,
            'type' => 'text',
            'time' => time(),
            'read' => false
        ];
        
        $this->success('发送成功', $message);
    }

    /**
     * 获取聊天频道列表
     */
    public function channels()
    {
        $channels = [
            [
                'id' => 'world',
                'name' => '世界',
                'icon' => '🌍',
                'level_required' => 1,
                'description' => '全服玩家可见'
            ],
            [
                'id' => 'guild',
                'name' => '宗门',
                'icon' => '🏯',
                'level_required' => 10,
                'description' => '宗门成员聊天',
                'need_guild' => true
            ],
            [
                'id' => 'team',
                'name' => '队伍',
                'icon' => '👥',
                'level_required' => 1,
                'description' => '队伍成员聊天',
                'need_team' => true
            ],
            [
                'id' => 'system',
                'name' => '系统',
                'icon' => '📢',
                'level_required' => 1,
                'description' => '系统公告',
                'readonly' => true
            ]
        ];
        
        $this->success('获取成功', ['channels' => $channels]);
    }

    /**
     * 获取黑名单
     */
    public function blacklist()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        $blacklist = [];
        
        $this->success('获取成功', [
            'blacklist' => $blacklist,
            'total' => count($blacklist)
        ]);
    }

    /**
     * 添加黑名单
     */
    public function addBlacklist()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $targetId = $this->request->post('target_id', 0, 'intval');
        
        $result = [
            'target_id' => $targetId,
            'added' => true,
            'time' => time()
        ];
        
        $this->success('添加成功', $result);
    }

    /**
     * 移除黑名单
     */
    public function removeBlacklist()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $targetId = $this->request->post('target_id', 0, 'intval');
        
        $result = [
            'target_id' => $targetId,
            'removed' => true,
            'time' => time()
        ];
        
        $this->success('移除成功', $result);
    }

    /**
     * 禁言检查
     */
    public function muteCheck()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        $muteInfo = [
            'is_muted' => false,
            'mute_end_time' => null,
            'mute_reason' => ''
        ];
        
        $this->success('获取成功', $muteInfo);
    }

    /**
     * 获取表情包列表
     */
    public function expressions()
    {
        $expressions = [
            ['id' => 1, 'name' => '微笑', 'icon' => '😊'],
            ['id' => 2, 'name' => '大笑', 'icon' => '😂'],
            ['id' => 3, 'name' => '震惊', 'icon' => '😮'],
            ['id' => 4, 'name' => '流泪', 'icon' => '😭'],
            ['id' => 5, 'name' => '愤怒', 'icon' => '😡'],
            ['id' => 6, 'name' => '得意', 'icon' => '😏'],
            ['id' => 7, 'name' => '可爱', 'icon' => '🥰'],
            ['id' => 8, 'name' => '修仙', 'icon' => '🧘'],
            ['id' => 9, 'name' => '飞升', 'icon' => '🚀'],
            ['id' => 10, 'name' => '法宝', 'icon' => '⚔️']
        ];
        
        $this->success('获取成功', ['expressions' => $expressions]);
    }

    /**
     * 举报玩家
     */
    public function report()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $targetId = $this->request->post('target_id', 0, 'intval');
        $reason = $this->request->post('reason', '', 'trim');
        $evidence = $this->request->post('evidence', '', 'trim');
        
        $result = [
            'target_id' => $targetId,
            'reported' => true,
            'report_id' => uniqid('report_'),
            'time' => time()
        ];
        
        $this->success('举报成功', $result);
    }
}
