<div class="message-sidebar">
    <?php if (empty($enriched)): ?>
        <p class="no-conversations">Aucune conversation</p>
    <?php else: ?>
        <?php foreach ($enriched as $conv): ?>
            <?php $isActive = (isset($activeAmiId) && $activeAmiId == $conv->id) ? 'active' : ''; ?>
            <a href="<?= $this->Url->build(['controller' => 'Messages', 'action' => 'view', $conv->id]) ?>"
               class="conversation-item <?= $isActive ?>">
                <div class="conv-header">
                    <span class="conv-name"><?= h($conv->ami->prenom . ' ' . $conv->ami->nom) ?></span>
                    <?php if ($conv->unread_count > 0): ?>
                        <span class="badge-unread"><?= $conv->unread_count ?></span>
                    <?php endif; ?>
                </div>
                <p class="conv-preview">
                    <?= h(mb_substr((string)$conv->last_message_entity->content, 0, 50)) ?>
                </p>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
