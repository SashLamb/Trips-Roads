<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\History> $historyRecords
 */

$this->assign('title', '🕓 My History');
$this->assign('mainClass', 'history-page');
?>

<div>
    <div class="flex-header-tools">
        <h1>🕓 My History</h1>

        <?php if (!$historyRecords->isEmpty()): ?>
            <?= $this->Form->postLink(
                '<i class="material-icons icon-align-middle">delete_sweep</i> Clear all',
                ['action' => 'deleteHistory'],
                [
                    'escape' => false,
                    'class' => 'btn-clear-history btn-danger-custom',
                    'confirm' => 'Do you really want to clear your entire history?'
                ]
            ) ?>
        <?php endif; ?>
    </div>

    <?= $this->Flash->render() ?>

    <?php if ($historyRecords->isEmpty()): ?>
        <p class="text-center-empty">
            You haven't viewed any road trips recently.
        </p>
        <div style="text-align: center;"> <?= $this->Html->link(
                'Explore road trips',
                ['controller' => 'Roadtrips', 'action' => 'publicRoadtrips'],
                ['class' => 'btn-view btn-padded']
            ) ?>
        </div>
    <?php else: ?>

        <div class="roadtrip-grid">
            <?php foreach ($historyRecords as $item): ?>
                <?php
                $rt = $item->roadtrip;
                if (!$rt) continue;
                ?>

                <div class="roadtrip-card">
                    <?= $this->Html->image($rt->cover_image, [
                        'alt' => 'Road trip cover photo',
                        'class' => 'roadtrip-photo',
                        'url' => ['action' => 'view', $rt->id]
                    ]) ?>

                    <h3><?= h($rt->title) ?></h3>

                    <span class="status-badge badge-dark">
                        👁️ Viewed on <?= $item->created->format('Y-m-d') ?>
                    </span>

                    <p><?= h($this->Text->truncate($rt->description, 100)) ?></p>

                    <p class="creator-info">
                        Shared by:
                        <strong><?= h($rt->user->username ?? 'Unknown User') ?></strong>
                    </p>

                    <div class="roadtrip-buttons">
                        <?= $this->Html->link(
                            '<i class="material-icons">visibility</i>',
                            ['controller' => 'Roadtrips', 'action' => 'view', $rt->id],
                            ['escape' => false, 'class' => 'btn-view', 'title' => 'View this road trip']
                        ) ?>

                        <?= $this->Html->link(
                            '<i class="material-icons">favorite_border</i>',
                            ['controller' => 'Favorites', 'action' => 'add', $rt->id, '?' => ['redirect' => 'history']],
                            ['escape' => false, 'class' => 'btn-favori', 'title' => 'Add to favorites']
                        ) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
