<?php

namespace app\admin\model\game;

use think\Model;

class Tiandao extends Model
{
    protected $name = 'xt_tiandao';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    protected $append = [
        'status_text',
        'difficulty_text',
    ];

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        return $this->getStatusList()[$value] ?? $value;
    }

    public function getDifficultyTextAttr($value, $data)
    {
        $value = $value ?: ($data['difficulty'] ?? 1);
        $list = [1 => '★☆☆☆☆', 2 => '★★☆☆☆', 3 => '★★★☆☆', 4 => '★★★★☆', 5 => '★★★★★'];
        return $list[$value] ?? $value;
    }
}
