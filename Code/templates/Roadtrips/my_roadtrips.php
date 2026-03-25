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

            <h1 class="sidebar-title">Mes Road Trips</h1>

            <a href="<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'add']) ?>" class="sidebar-create-btn">
                <i class="material-icons">add_circle</i> Créer un Road Trip
            </a>

            <nav class="profil-nav">
                <ul>
                    <li><a href="<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'myRoadtrips']) ?>" class="active">Mes Road-Trips</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Roadtrips', 'action' => 'publicRoadtrips']) ?>">Road-Trips publics</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'profile']) ?>">Paramètres du compte</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>" class="logout">Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">
            <?= $this->Flash->render() ?>

            <?php if ($roadtrips->isEmpty()) : ?>
                <div class="dashboard-empty-state">
                    <p class="empty-text-lead">Vous n'avez pas encore créé de road trip.</p>
                    <p>Utilisez le bouton dans le menu de gauche pour commencer !</p>
                </div>
            <?php else : ?>
                <div class="roadtrip-grid">
                    <?php foreach ($roadtrips as $rt): ?>
                        <div class="roadtrip-card">

                            <div class="card-badges">
                                <?php
                                $cssClass = $rt->is_completed ? 'statut-termine' : 'statut-brouillon';
                                $statusText = $rt->is_completed ? 'Terminé' : 'Brouillon';
                                ?>
                                <span class="badge-statut <?= $cssClass ?>"><?= $statusText ?></span>
                            </div>

                            <?= $this->Html->image($rt->cover_image, ['alt' => 'Photo du road trip', 'class' => 'roadtrip-photo']) ?>

                            <div class="card-body">
                                <h3><?= h($rt->title) ?></h3>
                                <p><?= h($this->Text->truncate($rt->description, 80, ['ellipsis' => '...'])) ?></p>
                            </div>

                            <div class="roadtrip-actions">
                                <?= $this->Html->link(
                                    '<i class="material-icons">visibility</i>',
                                    ['controller' => 'Roadtrips', 'action' => 'view', $rt->id],
                                    ['escape' => false, 'class' => 'action-btn view', 'title' => 'Voir']
                                ) ?>

                                <?= $this->Html->link(
                                    '<i class="material-icons">edit</i>',
                                    ['action' => 'edit', $rt->id],
                                    ['escape' => false, 'class' => 'action-btn edit', 'title' => 'Modifier']
                                ) ?>

                                <a href="<?= $this->Url->build(['action' => 'share', $rt->id]) ?>" class="action-btn share" title="Partager">
                                    <i class="material-icons">share</i>
                                </a>

                                <?= $this->Form->postLink(
                                    '<i class="material-icons">delete</i>',
                                    ['action' => 'delete', $rt->id],
                                    [
                                        'escape' => false,
                                        'class' => 'action-btn delete',
                                        'title' => 'Supprimer',
                                        'confirm' => 'Voulez-vous vraiment supprimer ce road trip ?'
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
            <h2>Partager votre road trip</h2>
            <p>Copiez ce lien pour partager votre road trip :</p>
            <div class="share-url-container">
                <input type="text" class="share-url-input" id="shareUrl" value="<?= h($shareUrl) ?>" readonly>
                <button class="copy-btn" onclick="copyShareUrl()">Copier</button>
            </div>
            <div class="copy-success" id="copySuccess">Lien copié !</div>
        </div>
    </div>
<?php endif; ?>
