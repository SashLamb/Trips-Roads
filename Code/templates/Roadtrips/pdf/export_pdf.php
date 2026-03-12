<?php
/**
 * Vue spécifique pour la génération de PDF
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Roadtrip $roadtrip
 */

$transportIcons = [
    'voiture' => '🚗', 'velo' => '🚴', 'vélo' => '🚴', 'marche' => '🚶', 'à pied' => '🚶',
    'train' => '🚂', 'bus' => '🚌', 'avion' => '✈️', 'moto' => '🏍️'
];
$getIcon = fn($m) => $transportIcons[strtolower($m ?? '')] ?? '🚗';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= h($roadtrip->title) ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #27ae60; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 28px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; color: #7f8c8d; font-size: 14px; }

        .description { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 30px; font-style: italic; color: #555; }

        .trip-container { margin-bottom: 25px; page-break-inside: avoid; }
        .trip-header { background-color: #2c3e50; color: white; padding: 12px 15px; border-radius: 6px; font-weight: bold; font-size: 16px; margin-bottom: 15px; }
        .trip-meta { float: right; font-weight: normal; font-size: 14px; }

        .step-list { margin: 0 0 0 20px; padding: 0; list-style-type: none; border-left: 2px dashed #bdc3c7; }
        .step-item { margin-bottom: 15px; padding-left: 20px; position: relative; }

        .step-icon { position: absolute; left: -12px; top: 0; background: white; font-size: 18px; }

        .step-title { font-weight: bold; color: #2980b9; font-size: 16px; margin: 0 0 5px 0; }
        .step-time { font-size: 12px; color: #e67e22; font-weight: bold; margin-bottom: 5px; display: block; }
        .step-desc { font-size: 13px; color: #666; margin-top: 5px; }

        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #aaa; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <h1><?= h($roadtrip->title) ?></h1>
    <p>Un road trip organisé par <?= h($roadtrip->user->username) ?></p>
</div>

<?php if (!empty($roadtrip->description)): ?>
    <div class="description">
        <?= nl2br(h($roadtrip->description)) ?>
    </div>
<?php endif; ?>

<?php foreach ($roadtrip->trips as $trip): ?>
    <div class="trip-container">
        <div class="trip-header">
            <?= h($trip->departure) ?> ➝ <?= h($trip->arrival) ?>
            <span class="trip-meta">
                    <?= $getIcon($trip->transport_mode) ?> <?= ucfirst($trip->transport_mode) ?>
                <?php if ($trip->date): ?> | 📅 <?= $trip->date->format('d/m/Y') ?><?php endif; ?>
                </span>
        </div>

        <ul class="step-list">
            <li class="step-item">
                <span class="step-icon">🟢</span>
                <h3 class="step-title">Départ : <?= h($trip->departure) ?></h3>
                <?php if ($trip->departure_time): ?>
                    <span class="step-time">Heure de départ : <?= $trip->departure_time->format('H:i') ?></span>
                <?php endif; ?>
            </li>

            <?php foreach ($trip->sub_steps as $step): ?>
                <li class="step-item">
                    <span class="step-icon">📍</span>
                    <h3 class="step-title"><?= h($step->city) ?></h3>
                    <?php
                    $pauseTime = is_object($step->duration) ? $step->duration->format('H:i') : substr((string)$step->duration, 0, 5);
                    if ($pauseTime && $pauseTime != '00:00'):
                        ?>
                        <span class="step-time">☕ Temps sur place : <?= $pauseTime ?></span>
                    <?php endif; ?>

                    <?php if (!empty($step->description)): ?>
                        <div class="step-desc">
                            <?= strip_tags($step->description, '<b><i><strong><em><br><p>') ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>

            <li class="step-item">
                <span class="step-icon">🏁</span>
                <h3 class="step-title">Arrivée : <?= h($trip->arrival) ?></h3>
            </li>
        </ul>
    </div>
<?php endforeach; ?>

<div class="footer">
    Carnet de route généré depuis votre application CakePHP - <?= date('d/m/Y') ?>
</div>

</body>
</html>
