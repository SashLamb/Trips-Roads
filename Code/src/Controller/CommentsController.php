<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Message;
use Cake\Http\Exception\ForbiddenException;

/**
 * Messages Controller
 *
 * Gère la messagerie interne entre les utilisateurs (amis).
 * * @property \App\Model\Table\MessagesTable $Messages
 */
class MessagesController extends AppController
{
    /**
     * Index method
     * * Affiche la page principale de la messagerie.
     * La récupération des conversations pour la barre latérale est gérée directement par une Cell dans la vue.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        $this->set([
            'userId' => $userId
        ]);
    }

    /**
     * Start method
     * * Initialise ou vérifie l'existence d'une conversation avec un ami avant d'y accéder.
     * Applique une sécurité pour empêcher de discuter avec soi-même ou avec un non-ami.
     *
     * @param string|int|null $amiId L'identifiant de l'utilisateur avec qui démarrer la conversation.
     * @return \Cake\Http\Response|null Redirige vers l'action view de la conversation.
     * @throws \Cake\Http\Exception\ForbiddenException Si l'utilisateur tente de se parler à lui-même ou s'ils ne sont pas amis.
     */
    public function start($amiId = null)
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();
        $amiId = (int)$amiId;

        if ($userId === $amiId) {
            throw new ForbiddenException("Vous ne pouvez pas discuter avec vous-même.");
        }

        $friendshipsTable = $this->getTableLocator()->get('Friendships');
        $isFriend = $friendshipsTable->find()
            ->where([
                'status' => 'accepted',
                'OR' => [
                    ['user_id' => $userId, 'friend_id' => $amiId],
                    ['user_id' => $amiId, 'friend_id' => $userId],
                ]
            ])
            ->first();

        if (!$isFriend) {
            throw new ForbiddenException("Vous n'êtes pas ami avec cet utilisateur.");
        }

        return $this->redirect(['action' => 'view', $amiId]);
    }

    /**
     * View method
     * * Récupère et affiche l'historique des messages d'une conversation spécifique.
     * Marque automatiquement tous les messages non lus de cet expéditeur comme "lus".
     *
     * @param string|int|null $amiId L'identifiant de l'ami avec qui on discute.
     * @return \Cake\Http\Response|null|void Renders view ou redirige vers l'index si l'ID est manquant.
     */
    public function view($amiId = null)
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();
        $amiId = (int)$amiId;

        if (!$amiId) {
            return $this->redirect(['action' => 'index']);
        }

        $ami = $this->Messages->Recipients->get($amiId);

        $messages = $this->Messages->find()
            ->where([
                'OR' => [
                    ['sender_id' => $userId, 'recipient_id' => $amiId],
                    ['sender_id' => $amiId, 'recipient_id' => $userId],
                ]
            ])
            ->orderAsc('created')
            ->all();

        $this->Messages->updateAll(
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            ['sender_id' => $amiId, 'recipient_id' => $userId, 'is_read' => 0]
        );

        $conversation_id = $amiId;

        $this->set(compact(
            'messages',
            'ami',
            'userId',
            'amiId',
            'conversation_id'
        ));
    }

    /**
     * SendMessage method
     * * Traite la soumission du formulaire d'envoi de message (requête POST).
     * Crée l'entité de conversation si c'est le tout premier message entre ces deux utilisateurs,
     * puis sauvegarde le message en base de données.
     *
     * @return \Cake\Http\Response|null Redirige l'utilisateur vers la vue de la conversation en cours.
     */
    public function sendMessage()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->Authentication->getIdentity()->getIdentifier();
        $amiId = (int)$this->request->getData('ami_id');
        $body = trim($this->request->getData('body'));

        $convTable = $this->Messages->Conversations;

        $conversation = $convTable->find()
            ->where(['OR' => [
                ['user_one_id' => $userId, 'user_two_id' => $amiId],
                ['user_one_id' => $amiId, 'user_two_id' => $userId],
            ]])->first();

        if (!$conversation) {
            $conversation = $convTable->newEmptyEntity();
            $conversation = $convTable->patchEntity($conversation, [
                'user_one_id' => $userId,
                'user_two_id' => $amiId
            ]);
            $convTable->save($conversation);
        }

        $message = $this->Messages->newEmptyEntity();

        $message = $this->Messages->patchEntity($message, [
            'sender_id' => $userId,
            'recipient_id' => $amiId,
            'conversation_id' => $conversation->id,
            'is_read' => 0
        ]);

        $message->body = $body;

        if ($this->Messages->save($message)) {
            return $this->redirect(['action' => 'view', $amiId]);
        }

        return $this->redirect(['action' => 'view', $amiId]);
    }
}
