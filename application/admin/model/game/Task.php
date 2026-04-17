<?php

namespace app\admin\model\game;

use think\Model;

class Task extends Model
{
    protected $name = 'xt_task';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    protected $append = [
        'status_text',
        'type_text',
    ];

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getTypeList()
    {
        return ['daily' => __('Daily'), 'weekly' => __('Weekly'), 'achievement' => __('Achievement'), 'main' => __('Main')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        return $this->getStatusList()[$value] ?? $value;
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? 'daily');
        return $this->getTypeList()[$value] ?? $value;
    }
}
