<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Message;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Messages Model
 */
class MessagesTable extends Table
{
    /**
     * @param array<string, mixed> $config Table configuration.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('messages');
        $this->setEntityClass(Message::class);
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Conversations', [
            'foreignKey' => 'conversation_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Senders', [
            'foreignKey' => 'sender_id',
            'className' => 'Users',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Recipients', [
            'foreignKey' => 'recipient_id',
            'className' => 'Users',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('conversation_id')
            ->notEmptyString('conversation_id');

        $validator
            ->integer('sender_id')
            ->notEmptyString('sender_id');

        $validator
            ->integer('recipient_id')
            ->notEmptyString('recipient_id');

        $validator
            ->maxLength('body', 4294967295)
            ->requirePresence('body', 'create')
            ->allowEmptyString('body');

        $validator
            ->boolean('is_read')
            ->allowEmptyString('is_read');

        $validator
            ->scalar('nonce')
            ->maxLength('nonce', 50)
            ->allowEmptyString('nonce');

        return $validator;
    }

    /**
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['conversation_id'], 'Conversations'));
        $rules->add($rules->existsIn(['sender_id'], 'Senders'));
        $rules->add($rules->existsIn(['recipient_id'], 'Recipients'));

        return $rules;
    }

    /**
     * @param \Cake\Event\EventInterface $event The event.
     * @param \Cake\Datasource\EntityInterface $entity The entity being saved.
     * @param \ArrayObject $options Save options.
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
    }
}
