<?php

namespace app\api\controller;

/**
 * 脑力王者 - 玩家存档API
 * 处理玩家游戏数据的保存与加载
 */
class PlayerData extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     * 保存玩家游戏数据
     * 
     * @ApiMethod (POST)
     * @ApiParams (name="player_id", type="integer", required=true, description="玩家ID")
     * @ApiParams (name="game_data", type="string", required=true, description="游戏数据JSON字符串")
     */
    public function savePlayer()
    {
        $playerId = $this->request->post('player_id', 0, 'intval');
        $gameData = $this->request->post('game_data', '', 'trim');
        
        // 参数验证
        if ($playerId <= 0) {
            $this->error('玩家ID无效', null, 400);
        }
        
        if (empty($gameData)) {
            $this->error('游戏数据不能为空', null, 400);
        }
        
        // 验证JSON格式
        $decodedData = json_decode($gameData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('游戏数据格式错误，请发送有效的JSON', null, 400);
        }
        
        try {
            // 查询是否已存在存档
            $saveRecord = \app\common\model\BrainGameSave::where('player_id', $playerId)->find();
            
            $time = time();
            
            if ($saveRecord) {
                // 更新现有存档
                $saveRecord->game_data = $gameData;
                $saveRecord->updated_at = $time;
                $saveRecord->save();
                $saveId = $saveRecord->id;
            } else {
                // 创建新存档
                $model = new \app\common\model\BrainGameSave();
                $model->player_id = $playerId;
                $model->game_data = $gameData;
                $model->created_at = $time;
                $model->updated_at = $time;
                $model->save();
                $saveId = $model->id;
            }
            
            $this->success('保存成功', [
                'id' => $saveId,
                'player_id' => $playerId,
                'saved_at' => date('Y-m-d H:i:s', $time)
            ]);
            
        } catch (\Exception $e) {
            $this->error('保存失败：' . $e->getMessage(), null, 500);
        }
    }

    /**
     * 加载玩家游戏数据
     * 
     * @ApiMethod (GET)
     * @ApiParams (name="player_id", type="integer", required=true, description="玩家ID")
     */
    public function loadPlayer()
    {
        $playerId = $this->request->get('player_id', 0, 'intval');
        
        // 参数验证
        if ($playerId <= 0) {
            $this->error('玩家ID无效', null, 400);
        }
        
        try {
            // 查询存档
            $saveRecord = \app\common\model\BrainGameSave::where('player_id', $playerId)->find();
            
            if (!$saveRecord) {
                $this->error('存档不存在', null, 404);
            }
            
            // 解析游戏数据
            $gameData = json_decode($saveRecord['game_data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // 如果解析失败，返回原始数据
                $gameData = $saveRecord['game_data'];
            }
            
            $this->success('加载成功', [
                'id' => $saveRecord['id'],
                'player_id' => $saveRecord['player_id'],
                'game_data' => $gameData,
                'created_at' => date('Y-m-d H:i:s', $saveRecord['created_at']),
                'updated_at' => date('Y-m-d H:i:s', $saveRecord['updated_at'])
            ]);
            
        } catch (\Exception $e) {
            $this->error('加载失败：' . $e->getMessage(), null, 500);
        }
    }
}
