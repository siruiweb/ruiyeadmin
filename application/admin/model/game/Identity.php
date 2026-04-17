<?php

namespace app\admin\model\game;

use think\Model;

class Identity extends Model
{
    protected $name = 'xt_identity';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    protected $append = [
        'status_text',
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
}
