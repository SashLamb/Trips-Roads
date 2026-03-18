<main class="main-index">
    <div class="messagerie-container">
        <div class="conversations-list">
            <h2>Mes messages</h2>
            <?= $this->cell('Message', [$userId, $amiId]) ?>
        </div>

        <div class="chat-area">
            <?php if (!empty($ami)): ?>
                <div class="chat-header">
                    <div class="chat-user-info">
                        <?php if (!empty($ami->profile_picture)): ?>
                            <img src="/uploads/pp/<?= h($ami->profile_picture) ?>">
                        <?php else: ?>
                            <div class="avatar-placeholder"><?= strtoupper(substr($ami->prenom, 0, 1)) ?></div>
                        <?php endif; ?>
                        <span><?= h($ami->prenom . ' ' . $ami->nom) ?></span>
                    </div>
                </div>

                <div class="messages-container" id="messagesContainer">
                    <?php foreach ($messages as $msg): ?>
                        <div class="message <?= ($msg->sender_id == $userId) ? 'sent' : 'received' ?>">
                            <div class="message-content">
                                <p><?= nl2br(h($msg->content)) ?></p>
                                <span class="message-time"><?= $msg->created->format('H:i') ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?= $this->Form->create(null, ['url' => ['action' => 'sendMessage'], 'class' => 'message-form']) ?>
                <?= $this->Form->hidden('ami_id', ['value' => $amiId]) ?>
                <?= $this->Form->control('body', ['type' => 'textarea', 'label' => false, 'placeholder' => 'Écrivez...', 'required' => true]) ?>
                <button type="submit"><i class="material-icons">send</i></button>
                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</main>
