<?php

namespace app\admin\model\game;

use think\Model;

class Player extends Model
{
    protected $name = 'xt_player';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    protected $append = [
        'status_text',
        'last_login_time_text',
    ];

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'banned' => __('Banned')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        return $this->getStatusList()[$value] ?? $value;
    }

    public function getLastLoginTimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['last_login_time'] ?? 0);
        return is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
    }
}
