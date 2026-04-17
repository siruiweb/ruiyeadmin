<?php

namespace app\admin\model\game;

use think\Model;

class Skill extends Model
{
    protected $name = 'xt_skill';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    protected $append = [
        'status_text',
        'skill_type_text',
    ];

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getSkillTypeList()
    {
        return ['active' => __('Active'), 'passive' => __('Passive')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        return $this->getStatusList()[$value] ?? $value;
    }

    public function getSkillTypeTextAttr($value, $data)
    {
        $value = $value ?: ($data['skill_type'] ?? 'active');
        return $this->getSkillTypeList()[$value] ?? $value;
    }
}
