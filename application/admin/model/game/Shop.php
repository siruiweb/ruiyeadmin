<?php

namespace app\admin\model\game;

use think\Model;

class Shop extends Model
{
    protected $name = 'xt_shop';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    protected $append = [
        'status_text',
        'currency_text',
    ];

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden'), 'soldout' => __('Soldout')];
    }

    public function getCurrencyList()
    {
        return ['gold' => __('Gold'), 'diamond' => __('Diamond'), 'rmb' => __('Rmb')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ?: ($data['status'] ?? '');
        return $this->getStatusList()[$value] ?? $value;
    }

    public function getCurrencyTextAttr($value, $data)
    {
        $value = $value ?: ($data['currency'] ?? 'gold');
        return $this->getCurrencyList()[$value] ?? $value;
    }
}
