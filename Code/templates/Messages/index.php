<main class="main-index">
    <div class="messagerie-container">
        <div class="conversations-list">
            <h2>Mes messages</h2>
            <?= $this->cell('Message', [$userId]) ?>
        </div>
        <div class="chat-area">
            <div class="no-chat-selected">
                <i class="material-icons">chat_bubble</i>
                <p>Sélectionnez une conversation</p>
            </div>
        </div>
    </div>
</main>
