<?php

namespace app\api\controller;

/**
 * 交易市场接口 X305
 */
class Market extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     * 获取市场商品列表
     */
    public function list()
    {
        $type = $this->request->get('type', 'all');
        $page = $this->request->get('page', 1);
        $pageSize = $this->request->get('pageSize', 20);
        $sort = $this->request->get('sort', 'newest');
        $keyword = $this->request->get('keyword', '');
        
        $where = ['status' => 'onsale'];
        
        if ($type !== 'all') {
            $where['item_type'] = $type;
        }
        
        if ($keyword) {
            $where[] = ['name', 'like', "%{$keyword}%"];
        }
        
        $query = \app\common\model\MarketItem::where($where);
        
        // 排序
        switch ($sort) {
            case 'price_asc':
                $query->order('price', 'asc');
                break;
            case 'price_desc':
                $query->order('price', 'desc');
                break;
            case 'hot':
                $query->order('sales_count', 'desc');
                break;
            default:
                $query->order('createtime', 'desc');
        }
        
        $list = $query->page($page, $pageSize)->select();
        $total = \app\common\model\MarketItem::where($where)->count();
        
        $data = [];
        foreach ($list as $item) {
            $data[] = [
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->item_type,
                'rarity' => $item->rarity,
                'description' => $item->description,
                'price' => $item->price,
                'stock' => $item->stock,
                'sellerId' => $item->seller_id,
                'sellerName' => $item->seller_name,
                'createTime' => $item->createtime * 1000,
                'isHot' => $item->sales_count > 10,
                'isNew' => (time() - $item->createtime) < 86400,
                'properties' => json_decode($item->properties, true)
            ];
        }
        
        $this->success('ok', [
            'list' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize
        ]);
    }

    /**
     * 购买商品
     */
    public function buy()
    {
        $itemId = $this->request->post('item_id');
        
        if (!$itemId) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        
        // 获取商品
        $item = \app\common\model\MarketItem::get($itemId);
        
        if (!$item) {
            $this->error('商品不存在');
        }
        
        if ($item->status !== 'onsale') {
            $this->error('商品已下架');
        }
        
        if ($item->seller_id == $playerId) {
            $this->error('不能购买自己的商品');
        }
        
        if ($item->stock < 1) {
            $this->error('库存不足');
        }
        
        // 获取玩家
        $player = \app\common\model\Player::getByUserId($playerId);
        
        if ($player->lingshi < $item->price) {
            $this->error('灵石不足');
        }
        
        // 扣除灵石
        $player->lingshi -= $item->price;
        $player->save();
        
        // 添加到背包
        $inventory = json_decode($player->inventory, true) ?: [];
        $inventory[] = [
            'itemId' => $item->item_id,
            'name' => $item->name,
            'type' => $item->item_type,
            'rarity' => $item->rarity,
            'count' => 1,
            'properties' => json_decode($item->properties, true)
        ];
        $player->inventory = json_encode($inventory);
        $player->save();
        
        // 减少库存
        $item->stock -= 1;
        if ($item->stock <= 0) {
            $item->status = 'soldout';
        }
        $item->sales_count += 1;
        $item->save();
        
        // 给卖家加灵石
        $seller = \app\common\model\Player::get($item->seller_id);
        if ($seller) {
            $fee = floor($item->price * 0.05); // 5%手续费
            $seller->lingshi += ($item->price - $fee);
            $seller->save();
        }
        
        $this->success('购买成功');
    }

    /**
     * 发布商品
     */
    public function sell()
    {
        $data = $this->request->post();
        
        if (empty($data['name']) || empty($data['price'])) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        if (!$player) {
            $this->error('玩家不存在');
        }
        
        $item = new \app\common\model\MarketItem();
        $item->seller_id = $player->id;
        $item->seller_name = $player->player_name;
        $item->name = $data['name'];
        $item->item_type = $data['type'] ?? 'equipment';
        $item->rarity = $data['rarity'] ?? 'common';
        $item->description = $data['description'] ?? '';
        $item->price = $data['price'];
        $item->stock = $data['stock'] ?? 1;
        $item->status = 'onsale';
        $item->properties = json_encode($data['properties'] ?? []);
        $item->createtime = time();
        
        $item->save();
        
        $this->success('发布成功', ['id' => $item->id]);
    }

    /**
     * 获取我的商品
     */
    public function my()
    {
        $playerId = $this->auth->id;
        
        $list = \app\common\model\MarketItem::where('seller_id', $playerId)
            ->order('createtime', 'desc')
            ->select();
        
        $data = [];
        foreach ($list as $item) {
            $data[] = [
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->item_type,
                'rarity' => $item->rarity,
                'price' => $item->price,
                'stock' => $item->stock,
                'status' => $item->status,
                'salesCount' => $item->sales_count,
                'createTime' => $item->createtime * 1000
            ];
        }
        
        $this->success('ok', $data);
    }

    /**
     * 下架商品
     */
    public function remove()
    {
        $itemId = $this->request->post('item_id');
        
        if (!$itemId) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        
        $item = \app\common\model\MarketItem::get($itemId);
        
        if (!$item) {
            $this->error('商品不存在');
        }
        
        if ($item->seller_id != $playerId) {
            $this->error('无权操作');
        }
        
        $item->status = 'removed';
        $item->save();
        
        $this->success('下架成功');
    }
}
