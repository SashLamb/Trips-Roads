<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Roadtrip> $roadtrips
 * @var string|null $shareUrl
 * @var string|null $showShare
 * @var \App\Model\Entity\User $user
 */

$this->assign('mainClass', '');
?>

    <div class="dashboard-container">

        <aside class="profil-sidebar">
            <div class="user-brief">
                <div class="avatar-circle small" style="background-image: url('<?= $this->Url->build($user->avatar_url) ?>');"></div>
                <h3><?= h($user->username) ?></h3>
            </div>

            <h1 class="sidebar-title">My Road Trips</h1>

            <a href="<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'add']) ?>" class="sidebar-create-btn">
                <i class="material-icons">add_circle</i> Create a Road Trip
            </a>

            <nav class="profil-nav">
                <ul>
                    <li><a href="<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'myRoadtrips']) ?>" class="active">My Road Trips</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'publicRoadtrips']) ?>">Public Road Trips</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'profile']) ?>">Settings</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>" class="logout">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">
            <?= $this->Flash->render() ?>

            <?php if ($roadtrips->isEmpty()) : ?>
                <div class="dashboard-empty-state">
                    <p class="empty-text-lead">You haven't created any road trips yet.</p>
                    <p>Use the button in the left menu to get started!</p>
                </div>
            <?php else : ?>
                <div class="roadtrip-grid">
                    <?php foreach ($roadtrips as $rt): ?>
                        <div class="roadtrip-card">

                            <div class="card-badges">
                                <?php
                                $cssClass = $rt->is_completed ? 'statut-termine' : 'statut-brouillon';
                                $statusText = $rt->is_completed ? 'Completed' : 'Draft';
                                ?>
                                <span class="badge-statut <?= $cssClass ?>"><?= $statusText ?></span>
                            </div>

                            <?= $this->Html->image($rt->cover_image, ['alt' => 'Road trip cover photo', 'class' => 'roadtrip-photo']) ?>

                            <div class="card-body">
                                <h3><?= h($rt->title) ?></h3>
                                <p><?= h($this->Text->truncate($rt->description, 80, ['ellipsis' => '...'])) ?></p>
                            </div>

                            <div class="roadtrip-actions">
                                <?= $this->Html->link(
                                    '<i class="material-icons">visibility</i>',
                                    ['controller' => 'Roadtrips', 'action' => 'view', $rt->id],
                                    ['escape' => false, 'class' => 'action-btn view', 'title' => 'View']
                                ) ?>

                                <?= $this->Html->link(
                                    '<i class="material-icons">edit</i>',
                                    ['action' => 'edit', $rt->id],
                                    ['escape' => false, 'class' => 'action-btn edit', 'title' => 'Edit']
                                ) ?>

                                <a href="<?= $this->Url->build(['action' => 'share', $rt->id]) ?>" class="action-btn share" title="Share">
                                    <i class="material-icons">share</i>
                                </a>

                                <?= $this->Form->postLink(
                                    '<i class="material-icons">delete</i>',
                                    ['action' => 'delete', $rt->id],
                                    [
                                        'escape' => false,
                                        'class' => 'action-btn delete',
                                        'title' => 'Delete',
                                        'confirm' => 'Do you really want to delete this road trip?'
                                    ]
                                ) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php if ($showShare && $shareUrl): ?>
    <div class="share-modal active" id="shareModal">
        <div class="share-modal-content">
            <span class="share-modal-close" onclick="closeShareModal()">&times;</span>
            <h2>Share your road trip</h2>
            <p>Copy this link to share your road trip:</p>
            <div class="share-url-container">
                <input type="text" class="share-url-input" id="shareUrl" value="<?= h($shareUrl) ?>" readonly>
                <button class="copy-btn" onclick="copyShareUrl()">Copy</button>
            </div>
            <div class="copy-success" id="copySuccess">Link copied!</div>
        </div>
    </div>
<?php endif; ?>
