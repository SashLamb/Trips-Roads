<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Roadtrip> $roadtrips
 * @var array $favorisIds
 * @var string|null $userId
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Comment $newComment
 */

$this->assign('mainClass', 'dashboard-page');
$currentUser = $this->request->getAttribute('identity');
$isAdmin = $currentUser && isset($currentUser->role) && $currentUser->role === 'admin';
?>

<div class="dashboard-container <?= $isAdmin ? 'admin-mode' : '' ?>">

    <aside class="profil-sidebar">
        <div class="user-brief">
            <?php
            if (isset($user) && !empty($user->profile_picture)) {
                $avatarUrl = $this->Url->webroot('uploads/pp/' . $user->profile_picture);
                $userName = h($user->username);
            } else {
                $avatarUrl = $this->Url->webroot('img/User.png');
                $userName = "Visitor";
            }
            ?>
            <div class="avatar-circle small avatar-fallback">
                <?= $this->Html->image($avatarUrl, ['alt' => 'Avatar', 'style' => 'width:100%; height:100%; border-radius:50%; object-fit:cover;']) ?>
            </div>
            <h3>
                <?= $userName ?>
                <?php if ($isAdmin): ?>
                    <br><span class="admin-badge">Admin</span>
                <?php endif; ?>
            </h3>
        </div>

        <h1 class="sidebar-title">Public Road Trips</h1>

        <?php if (isset($userId)): ?>
            <?= $this->Html->link(
                '<i class="material-icons">add_circle</i> Create a Road Trip',
                ['controller' => 'Roadtrips', 'action' => 'add'],
                ['escape' => false, 'class' => 'sidebar-create-btn']
            ) ?>
        <?php endif; ?>

        <nav class="profil-nav">
            <ul>
                <?php if (isset($userId)): ?>
                    <li><?= $this->Html->link('My Road Trips', ['controller' => 'Roadtrips', 'action' => 'myRoadtrips']) ?></li>
                <?php endif; ?>
                <li><?= $this->Html->link('Public Road Trips', ['controller' => 'Roadtrips', 'action' => 'publicRoadtrips'], ['class' => 'active']) ?></li>
                <?php if (isset($userId)): ?>
                    <li><?= $this->Html->link('Settings', ['controller' => 'Users', 'action' => 'profile']) ?></li>
                    <li><?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout'], ['class' => 'logout']) ?></li>
                <?php else: ?>
                    <li><?= $this->Html->link('Login', ['controller' => 'Users', 'action' => 'login']) ?></li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>

    <div class="main-content">
        <?= $this->Flash->render() ?>

        <?php if ($roadtrips->isEmpty()) : ?>
            <p class="text-center-empty">No public road trips at the moment.</p>
        <?php else : ?>

            <div class="roadtrip-grid">
                <?php foreach ($roadtrips as $rt): ?>

                    <?php
                    $cssClass = $rt->is_completed ? 'statut-termine' : 'statut-brouillon';
                    $statusLabel = $rt->is_completed ? 'Completed' : 'In progress';
                    $isOwner = $currentUser && $currentUser->getIdentifier() === $rt->user_id;
                    $commentsCount = !empty($rt->comments) ? count($rt->comments) : 0;
                    ?>

                    <div class="roadtrip-card <?= $isAdmin ? 'admin-card' : '' ?>">

                        <div class="card-badges">
                            <span class="badge-statut <?= $cssClass ?>"><?= $statusLabel ?></span>
                        </div>

                        <?= $this->Html->image($rt->cover_image, ['alt' => 'Roadtrip cover photo', 'class' => 'roadtrip-photo']) ?>

                        <div class="card-body">
                            <h3><?= h($rt->title) ?></h3>
                            <p class="card-description"><?= h($this->Text->truncate($rt->description, 100)) ?></p>
                            <div class="creator-info">
                                Shared by: <strong><?= h($rt->user->username ?? 'Anonymous') ?></strong>
                            </div>
                        </div>

                        <div class="roadtrip-actions">
                            <?= $this->Html->link(
                                '<i class="material-icons">visibility</i>',
                                ['action' => 'view', $rt->id],
                                ['escape' => false, 'class' => 'action-btn view', 'title' => 'View']
                            ) ?>

                            <?php if ($currentUser): ?>
                                <?php if (in_array($rt->id, $favoriteIds ?? [])): ?>
                                    <?= $this->Form->postLink(
                                        '<i class="material-icons">favorite</i>',
                                        ['controller' => 'Favorites', 'action' => 'delete', $rt->id],
                                        ['escape' => false, 'title' => 'Remove from favorites']
                                    ) ?>
                                <?php else: ?>
                                    <?= $this->Form->postLink(
                                        '<i class="material-icons">favorite_border</i>',
                                        ['controller' => 'Favorites', 'action' => 'add', '?' => ['roadtrip_id' => $rt->id]],
                                        ['escape' => false, 'title' => 'Add to favorites']
                                    ) ?>
                                <?php endif; ?>
                            <?php endif; ?>

                            <button type="button" class="action-btn" onclick="openRoadtripModal('modalAvis-<?= $rt->id ?>')" title="View reviews">
                                <i class="material-icons">rate_review</i>
                                <?php if ($commentsCount > 0): ?>
                                    <span style="font-size: 0.9em; font-weight: bold; margin-left: 3px;"><?= $commentsCount ?></span>
                                <?php endif; ?>
                            </button>

                            <?php if ($currentUser): ?>
                                <button type="button" class="action-btn" onclick="openRoadtripModal('modalComment-<?= $rt->id ?>')" title="Leave a review">
                                    <i class="material-icons">add_comment</i>
                                </button>
                            <?php endif; ?>

                            <button type="button" class="action-btn js-export-btn"
                                    data-gpx="<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'exportGpx', $rt->id]) ?>"
                                    data-pdf="<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'exportPdf', $rt->id]) ?>"
                                    title="Export to GPX and PDF">
                                <i class="material-icons">file_download</i>
                            </button>

                            <?php if ($isOwner || $isAdmin): ?>
                                <?= $this->Form->postLink(
                                    '<i class="material-icons">delete</i>',
                                    ['controller' => 'Roadtrips', 'action' => 'delete', $rt->id],
                                    ['confirm' => 'Delete this road trip?', 'escape' => false, 'class' => 'action-btn btn-delete', 'title' => 'Delete']
                                ) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php foreach ($roadtrips as $rt): ?>

                <div id="modalAvis-<?= $rt->id ?>" class="custom-modal">
                    <div class="modal-content modal-avis">
                        <div class="modal-header">
                            <h3><i class="material-icons">rate_review</i> Reviews — <?= h($rt->title) ?></h3>
                            <button type="button" class="modal-close" onclick="closeRoadtripModal('modalAvis-<?= $rt->id ?>')" aria-label="Close">&times;</button>
                        </div>

                        <div class="modal-body">
                            <?php if (empty($rt->comments)): ?>
                                <div class="no-comments">
                                    <i class="material-icons">chat_bubble_outline</i>
                                    <p>No reviews yet.</p>
                                    <?php if ($currentUser): ?>
                                        <button type="button" class="btn-switch-to-comment" onclick="closeRoadtripModal('modalAvis-<?= $rt->id ?>'); openRoadtripModal('modalComment-<?= $rt->id ?>')">
                                            Be the first to leave a review!
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="comments-list">
                                    <?php foreach ($rt->comments as $comment): ?>
                                        <?php $isCommentOwner = $currentUser && $currentUser->getIdentifier() === $comment->user_id; ?>
                                        <div class="comment-item">
                                            <div class="comment-meta">
                                                <span class="comment-author">
                                                    <i class="material-icons">account_circle</i>
                                                    <?= h($comment->user->username ?? 'Anonymous') ?>
                                                </span>
                                                <?php if (!empty($comment->rating)): ?>
                                                    <span class="comment-rating">
                                                        <?= str_repeat('⭐', (int)$comment->rating) ?>
                                                    </span>
                                                <?php endif; ?>

                                                <?php if ($isCommentOwner || $isAdmin): ?>
                                                    <?= $this->Form->postLink(
                                                        '<i class="material-icons">delete</i>',
                                                        ['controller' => 'Comments', 'action' => 'delete', $comment->id],
                                                        ['confirm' => 'Delete this review?', 'escape' => false, 'class' => 'comment-delete-btn', 'title' => 'Delete review']
                                                    ) ?>
                                                <?php endif; ?>
                                            </div>
                                            <p class="comment-body"><?= h($comment->body) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($currentUser): ?>
                                    <div class="modal-footer-action">
                                        <button type="button" class="btn-switch-to-comment" onclick="closeRoadtripModal('modalAvis-<?= $rt->id ?>'); openRoadtripModal('modalComment-<?= $rt->id ?>')">
                                            <i class="material-icons">add_comment</i> Add my review
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($currentUser): ?>
                    <div id="modalComment-<?= $rt->id ?>" class="custom-modal">
                        <div class="modal-content modal-comment-form">
                            <div class="modal-header">
                                <h3><i class="material-icons">add_comment</i> Leave a review</h3>
                                <button type="button" class="modal-close" onclick="closeRoadtripModal('modalComment-<?= $rt->id ?>')" aria-label="Close">&times;</button>
                            </div>

                            <div class="modal-body">
                                <p class="modal-trip-title">
                                    Road trip: <strong><?= h($rt->title) ?></strong>
                                </p>

                                <?= $this->Form->create($newComment, ['url' => ['controller' => 'Comments', 'action' => 'add']]) ?>
                                <?= $this->Form->hidden('roadtrip_id', ['value' => $rt->id]) ?>

                                <div class="form-group rating-group">
                                    <label class="form-label">Your rating</label>
                                    <div class="modern-star-rating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" id="star<?= $i ?>-<?= $rt->id ?>" name="rating" value="<?= $i ?>" required/>
                                            <label for="star<?= $i ?>-<?= $rt->id ?>" title="<?= $i ?> stars">★</label>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <?= $this->Form->control('body', [
                                    'type' => 'textarea',
                                    'label' => ['text' => 'Comment', 'class' => 'form-label'],
                                    'class' => 'form-textarea',
                                    'placeholder' => 'Share your experience...',
                                    'rows' => 4
                                ]) ?>

                                <div class="form-actions">
                                    <button type="button" class="btn-cancel-modal" onclick="closeRoadtripModal('modalComment-<?= $rt->id ?>')">Cancel</button>

                                    <?= $this->Form->button('<i class="material-icons">send</i> Publish', ['type' => 'submit', 'class' => 'btn-submit-comment', 'escapeTitle' => false]) ?>
                                </div>
                                <?= $this->Form->end() ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>
