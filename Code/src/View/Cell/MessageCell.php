<?php
namespace App\View\Cell;

use Cake\View\Cell;

class MessageCell extends Cell
{
    public function display($userId, $amiId = null)
    {
        $messagesTable = $this->fetchTable('Messages');

        $lastMessages = $messagesTable->find()
            ->contain(['Senders', 'Recipients'])
            ->where(['OR' => [['sender_id' => $userId], ['recipient_id' => $userId]]])
            ->orderDesc('Messages.created')
            ->all();

        $conversations = [];
        foreach ($lastMessages as $msg) {
            $isSender = ($msg->sender_id == $userId);
            $ami = $isSender ? $msg->recipient : $msg->sender;
            if (!$ami) continue;

            $idAmi = $ami->id;
            if (isset($conversations[$idAmi])) continue;

            $unreadCount = $messagesTable->find()
                ->where(['sender_id' => $idAmi, 'recipient_id' => $userId, 'is_read' => 0])
                ->count();

            $conversations[$idAmi] = (object)[
                'id' => $idAmi,
                'ami' => $ami,
                'last_message_entity' => $msg, // On passe l'entité pour accéder au ->content
                'unread_count' => $unreadCount
            ];
        }

        $this->set('enriched', array_values($conversations));
        $this->set('activeAmiId', $amiId);
    }
}
