<?php

namespace app\admin\model\game;

use think\Model;

class Story extends Model
{
    protected $name = 'xt_story';
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
        return ['main' => __('Main'), 'branch' => __('Branch'), 'event' => __('Event')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        return $this->getStatusList()[$value] ?? $value;
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['type'] ?? 'main');
        return $this->getTypeList()[$value] ?? $value;
    }
}
