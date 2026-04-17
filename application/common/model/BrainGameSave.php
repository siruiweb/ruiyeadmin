<?php

namespace app\common\model;

use think\Model;

/**
 * 脑力王者 - 玩家存档模型
 */
class BrainGameSave extends Model
{

    // 表名
    protected $name = 'brain_game_save';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    // 追加属性
    protected $append = [
    ];

    // 类型转换
    protected $type = [
        'game_data' => 'json',
    ];
}
