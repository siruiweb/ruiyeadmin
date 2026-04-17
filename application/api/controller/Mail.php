<?php

namespace app\api\controller;

/**
 * 邮件系统接口 X306
 */
class Mail extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     * 获取邮件列表
     */
    public function list()
    {
        $page = $this->request->get('page', 1);
        $limit = $this->request->get('limit', 20);
        
        $playerId = $this->auth->id;
        
        $list = \app\common\model\Mail::where('receiver_id', $playerId)
            ->where('is_deleted', 0)
            ->order('createtime', 'desc')
            ->page($page, $limit)
            ->select();
        
        $total = \app\common\model\Mail::where('receiver_id', $playerId)
            ->where('is_deleted', 0)
            ->count();
        
        $data = [];
        foreach ($list as $mail) {
            $data[] = [
                'id' => $mail->id,
                'title' => $mail->title,
                'content' => $mail->content,
                'senderName' => $mail->sender_name,
                'senderId' => $mail->sender_id,
                'createTime' => $mail->createtime * 1000,
                'isRead' => $mail->is_read,
                'isImportant' => $mail->is_important,
                'hasAttachment' => !empty($mail->attachments),
                'isSystem' => $mail->is_system,
                'canReply' => !$mail->is_system && $mail->sender_id > 0,
                'attachments' => json_decode($mail->attachments, true) ?: []
            ];
        }
        
        $unread = \app\common\model\Mail::where('receiver_id', $playerId)
            ->where('is_read', 0)
            ->where('is_deleted', 0)
            ->count();
        
        $this->success('ok', [
            'list' => $data,
            'total' => $total,
            'unread' => $unread
        ]);
    }

    /**
     * 获取邮件详情
     */
    public function detail()
    {
        $mailId = $this->request->get('id');
        
        if (!$mailId) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        
        $mail = \app\common\model\Mail::get($mailId);
        
        if (!$mail || $mail->receiver_id != $playerId) {
            $this->error('邮件不存在');
        }
        
        // 标记已读
        if (!$mail->is_read) {
            $mail->is_read = 1;
            $mail->read_time = time();
            $mail->save();
        }
        
        $data = [
            'id' => $mail->id,
            'title' => $mail->title,
            'content' => $mail->content,
            'senderName' => $mail->sender_name,
            'senderId' => $mail->sender_id,
            'createTime' => $mail->createtime * 1000,
            'isRead' => $mail->is_read,
            'isImportant' => $mail->is_important,
            'hasAttachment' => !empty($mail->attachments),
            'attachments' => json_decode($mail->attachments, true) ?: []
        ];
        
        $this->success('ok', $data);
    }

    /**
     * 标记邮件已读
     */
    public function read()
    {
        $mailId = $this->request->post('mail_id');
        
        if (!$mailId) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        
        $mail = \app\common\model\Mail::get($mailId);
        
        if ($mail && $mail->receiver_id == $playerId) {
            $mail->is_read = 1;
            $mail->read_time = time();
            $mail->save();
        }
        
        $this->success('ok');
    }

    /**
     * 全部标记已读
     */
    public function readAll()
    {
        $playerId = $this->auth->id;
        
        \app\common\model\Mail::where('receiver_id', $playerId)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_time' => time()
            ]);
        
        $this->success('ok');
    }

    /**
     * 领取附件
     */
    public function claimAttachment()
    {
        $mailId = $this->request->post('mail_id');
        $attachmentId = $this->request->post('attachment_id');
        
        if (!$mailId || !$attachmentId) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        
        $mail = \app\common\model\Mail::get($mailId);
        
        if (!$mail || $mail->receiver_id != $playerId) {
            $this->error('邮件不存在');
        }
        
        $attachments = json_decode($mail->attachments, true) ?: [];
        
        foreach ($attachments as &$att) {
            if ($att['id'] == $attachmentId && !($att['claimed'] ?? false)) {
                $att['claimed'] = true;
                
                // 发放奖励
                $player = \app\common\model\Player::getByUserId($playerId);
                
                switch ($att['type']) {
                    case 'lingshi':
                        $player->lingshi += $att['amount'];
                        break;
                    case 'daojin':
                        $player->daojin += $att['amount'];
                        break;
                    case 'exp':
                        $player->exp += $att['amount'];
                        break;
                    case 'item':
                        // 添加物品到背包
                        $inventory = json_decode($player->inventory, true) ?: [];
                        $inventory[] = [
                            'itemId' => $att['itemId'] ?? 0,
                            'name' => $att['name'],
                            'type' => 'item',
                            'count' => 1
                        ];
                        $player->inventory = json_encode($inventory);
                        break;
                }
                $player->save();
            }
        }
        
        $mail->attachments = json_encode($attachments);
        $mail->save();
        
        $this->success('领取成功');
    }

    /**
     * 删除邮件
     */
    public function delete()
    {
        $mailId = $this->request->post('mail_id');
        
        if (!$mailId) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        
        $mail = \app\common\model\Mail::get($mailId);
        
        if ($mail && $mail->receiver_id == $playerId) {
            $mail->is_deleted = 1;
            $mail->delete_time = time();
            $mail->save();
        }
        
        $this->success('删除成功');
    }

    /**
     * 发送邮件
     */
    public function send()
    {
        $data = $this->request->post();
        
        if (empty($data['receiverName']) || empty($data['title']) || empty($data['content'])) {
            $this->error('参数错误');
        }
        
        $playerId = $this->auth->id;
        $player = \app\common\model\Player::getByUserId($playerId);
        
        // 查找收件人
        $receiver = \app\common\model\Player::where('player_name', $data['receiverName'])->find();
        
        if (!$receiver) {
            $this->error('收件人不存在');
        }
        
        // 发送费灵石
        $cost = 10;
        if (isset($data['attachLingshi'])) {
            $cost += $data['attachLingshi'];
        }
        
        if ($player->lingshi < $cost) {
            $this->error('灵石不足');
        }
        
        $player->lingshi -= $cost;
        $player->save();
        
        $mail = new \app\common\model\Mail();
        $mail->sender_id = $player->id;
        $mail->sender_name = $player->player_name;
        $mail->receiver_id = $receiver->id;
        $mail->receiver_name = $receiver->player_name;
        $mail->title = $data['title'];
        $mail->content = $data['content'];
        $mail->is_system = 0;
        $mail->is_read = 0;
        $mail->is_important = 0;
        $mail->is_deleted = 0;
        $mail->attachments = json_encode([]);
        $mail->createtime = time();
        
        // 如果有附带灵石
        if (isset($data['attachLingshi']) && $data['attachLingshi'] > 0) {
            $attachments = json_decode($mail->attachments, true) ?: [];
            $attachments[] = [
                'id' => 1,
                'type' => 'lingshi',
                'name' => '灵石',
                'amount' => $data['attachLingshi'],
                'claimed' => false
            ];
            $mail->attachments = json_encode($attachments);
        }
        
        $mail->save();
        
        $this->success('发送成功');
    }

    /**
     * 获取未读数量
     */
    public function unreadCount()
    {
        $playerId = $this->auth->id;
        
        $count = \app\common\model\Mail::where('receiver_id', $playerId)
            ->where('is_read', 0)
            ->where('is_deleted', 0)
            ->count();
        
        $this->success('ok', ['count' => $count]);
    }

    /**
     * 获取发件箱
     */
    public function sent()
    {
        $page = $this->request->get('page', 1);
        $limit = $this->request->get('limit', 20);
        
        $playerId = $this->auth->id;
        
        $list = \app\common\model\Mail::where('sender_id', $playerId)
            ->where('is_system', 0)
            ->order('createtime', 'desc')
            ->page($page, $limit)
            ->select();
        
        $data = [];
        foreach ($list as $mail) {
            $data[] = [
                'id' => $mail->id,
                'title' => $mail->title,
                'content' => $mail->content,
                'receiverName' => $mail->receiver_name,
                'createTime' => $mail->createtime * 1000
            ];
        }
        
        $this->success('ok', ['list' => $data]);
    }

    /**
     * 获取系统邮件
     */
    public function system()
    {
        $page = $this->request->get('page', 1);
        $limit = $this->request->get('limit', 20);
        
        $playerId = $this->auth->id;
        
        $list = \app\common\model\Mail::where('receiver_id', $playerId)
            ->where('is_system', 1)
            ->where('is_deleted', 0)
            ->order('createtime', 'desc')
            ->page($page, $limit)
            ->select();
        
        $data = [];
        foreach ($list as $mail) {
            $data[] = [
                'id' => $mail->id,
                'title' => $mail->title,
                'content' => $mail->content,
                'createTime' => $mail->createtime * 1000,
                'isRead' => $mail->is_read,
                'hasAttachment' => !empty($mail->attachments),
                'attachments' => json_decode($mail->attachments, true) ?: []
            ];
        }
        
        $this->success('ok', ['list' => $data]);
    }

    /**
     * 获取附件邮件
     */
    public function attachments()
    {
        $page = $this->request->get('page', 1);
        $limit = $this->request->get('limit', 20);
        
        $playerId = $this->auth->id;
        
        // 查找有未领取附件的邮件
        $list = \app\common\model\Mail::where('receiver_id', $playerId)
            ->where('is_deleted', 0)
            ->where('attachments', '<>', '')
            ->order('createtime', 'desc')
            ->page($page, $limit)
            ->select();
        
        $data = [];
        foreach ($list as $mail) {
            $attachments = json_decode($mail->attachments, true) ?: [];
            $hasUnclaimed = false;
            foreach ($attachments as $att) {
                if (!($att['claimed'] ?? false)) {
                    $hasUnclaimed = true;
                    break;
                }
            }
            
            if ($hasUnclaimed) {
                $data[] = [
                    'id' => $mail->id,
                    'title' => $mail->title,
                    'content' => $mail->content,
                    'senderName' => $mail->sender_name,
                    'createTime' => $mail->createtime * 1000,
                    'attachments' => $attachments
                ];
            }
        }
        
        $this->success('ok', ['list' => $data]);
    }
}
