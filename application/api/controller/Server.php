<?php

namespace app\api\controller;

/**
 * 诸天仙途 - 服务器API
 * 处理服务器状态、公告、维护等
 */
class Server extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 获取服务器列表
     */
    public function list()
    {
        $servers = [
            [
                'id' => 1,
                'name' => '诸天仙途·一区',
                'ip' => 'zhutian01.shengame.net',
                'port' => 8080,
                'status' => 'online',
                'online_players' => 156,
                'max_players' => 1000,
                'is_new' => true,
                'is_recommend' => true,
                'open_time' => strtotime('2026-03-01')
            ],
            [
                'id' => 2,
                'name' => '诸天仙途·二区',
                'ip' => 'zhutian02.shengame.net',
                'port' => 8080,
                'status' => 'online',
                'online_players' => 89,
                'max_players' => 1000,
                'is_new' => false,
                'is_recommend' => false,
                'open_time' => strtotime('2026-03-10')
            ],
            [
                'id' => 3,
                'name' => '诸天仙途·三区',
                'ip' => 'zhutian03.shengame.net',
                'port' => 8080,
                'status' => 'online',
                'online_players' => 234,
                'max_players' => 1000,
                'is_new' => true,
                'is_recommend' => true,
                'open_time' => strtotime('2026-03-20')
            ]
        ];
        
        $this->success('获取成功', [
            'servers' => $servers,
            'total_players' => array_sum(array_column($servers, 'online_players'))
        ]);
    }

    /**
     * 获取服务器状态
     */
    public function status()
    {
        $serverId = $this->request->get('server_id', 1, 'intval');
        
        $status = [
            'server_id' => $serverId,
            'server_name' => '诸天仙途·一区',
            'status' => 'online',
            'online_players' => 156,
            'max_players' => 1000,
            'peak_today' => 300,
            'cpu_usage' => 45,
            'memory_usage' => 60,
            'network_status' => 'good',
            'last_update' => time()
        ];
        
        $this->success('获取成功', $status);
    }

    /**
     * 获取公告
     */
    public function announcement()
    {
        $type = $this->request->get('type', 'all', 'trim');
        
        $announcements = [
            [
                'id' => 1,
                'type' => 'important',
                'title' => '【重要】游戏正式上线！',
                'content' => '欢迎各位修士来到诸天仙途！修仙路漫漫，道心永不改。',
                'time' => strtotime('2026-03-01'),
                'is_top' => true
            ],
            [
                'id' => 2,
                'type' => 'activity',
                'title' => '【活动】开服七日乐',
                'content' => '开服前七日，每日登录可领取好礼！',
                'time' => strtotime('2026-03-15'),
                'is_top' => false
            ],
            [
                'id' => 3,
                'type' => 'update',
                'title' => '【更新】v1.0.1版本更新',
                'content' => '1. 优化了修炼界面\n2. 修复了若干BUG\n3. 新增了部分功法',
                'time' => strtotime('2026-03-20'),
                'is_top' => false
            ]
        ];
        
        $this->success('获取成功', [
            'announcements' => $announcements,
            'popup' => [
                'id' => 1,
                'title' => '欢迎来到诸天仙途',
                'content' => '祝您修仙愉快！'
            ]
        ]);
    }

    /**
     * 获取维护信息
     */
    public function maintenance()
    {
        $maintenance = [
            'is_in_maintenance' => false,
            'start_time' => null,
            'end_time' => null,
            'reason' => '',
            'next_scheduled' => null
        ];
        
        $this->success('获取成功', $maintenance);
    }

    /**
     * 获取游戏版本
     */
    public function version()
    {
        $version = [
            'version' => '1.0.1',
            'build' => 20260325,
            'min_version' => '1.0.0',
            'update_url' => '',
            'update_content' => '1. 优化了修炼界面\n2. 修复了若干BUG',
            'force_update' => false
        ];
        
        $this->success('获取成功', $version);
    }

    /**
     * 获取服务器时间
     */
    public function time()
    {
        $this->success('获取成功', [
            'server_time' => time(),
            'server_time_str' => date('Y-m-d H:i:s'),
            'timezone' => 'Asia/Shanghai',
            'timestamp_ms' => round(microtime(true) * 1000)
        ]);
    }

    /**
     * 获取配置信息
     */
    public function config()
    {
        $config = [
            'game' => [
                'max_level' => 100,
                'max_age' => 120,
                'pvp_enabled' => true,
                'red_name_threshold' => 10,
                'pk_protect_level' => 10
            ],
            'battle' => [
                'pvp_enabled' => true,
                'pvp_cooltime' => 60,
                'pvp_reward_multiplier' => 1.5,
                'boss_respawn_time' => 3600
            ],
            'shop' => [
                'daily_refresh_hour' => 0,
                'vip_discount' => 0.9
            ],
            'chat' => [
                'send_interval' => 3,
                'max_length' => 200,
                'mute_words' => []
            ],
            'activity' => [
                'daily_reset_hour' => 0,
                'weekly_reset_day' => 1
            ]
        ];
        
        $this->success('获取成功', $config);
    }

    /**
     * 获取服务器负载
     */
    public function load()
    {
        $load = [
            'server_id' => 1,
            'cpu_usage' => 45,
            'memory_usage' => 60,
            'disk_usage' => 35,
            'network_in' => 1024,
            'network_out' => 2048,
            'database_connections' => 50,
            'api_qps' => 100,
            'cache_hit_rate' => 0.95,
            'status' => 'good'
        ];
        
        $this->success('获取成功', $load);
    }

    /**
     * 获取推荐服务器
     */
    public function recommend()
    {
        $servers = [
            [
                'id' => 3,
                'name' => '诸天仙途·三区',
                'online_players' => 234,
                'max_players' => 1000,
                'is_new' => true,
                'reason' => '新服开启，人气火爆'
            ],
            [
                'id' => 1,
                'name' => '诸天仙途·一区',
                'online_players' => 156,
                'max_players' => 1000,
                'is_new' => false,
                'reason' => '老牌服务器，稳定可靠'
            ]
        ];
        
        $this->success('获取成功', ['servers' => $servers]);
    }

    /**
     * 玩家数量统计
     */
    public function statistics()
    {
        $stats = [
            'total_registered' => 50000,
            'total_online' => 479,
            'today_active' => 2000,
            'today_new' => 150,
            'peak_online' => 600,
            'peak_time' => strtotime('2026-03-24 20:00')
        ];
        
        $this->success('获取成功', $stats);
    }
}
