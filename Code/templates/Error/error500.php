<?php
/**
 * @var \App\View\AppView $this
 * @var string $message
 * @var string $url
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->setLayout('error');

if (Configure::read('debug')) :
    $this->setLayout('error');
    $this->assign('title', $message);
    $this->assign('templateName', 'error500.php');

    $this->start('file');
    ?>
    <?php if (isset($error) && $error instanceof \Error) : ?>
    <?php $file = $error->getFile() ?>
    <?php $line = $error->getLine() ?>
    <strong>Error in: </strong>
    <?= $this->Html->link(sprintf('%s, line %s', Debugger::trimPath($file), $line), Debugger::editorUrl($file, $line)); ?>
<?php endif; ?>
    <?php
    echo $this->element('auto_table_warning');
    $this->end();
endif;
?>

<div class="error-container">
    <div class="error-icon">
        <i class="material-icons" style="font-size: 70px; color: var(--rouge);">build_circle</i>
    </div>

    <h2>Erreur Interne Serveur</h2>

    <p class="error">
        <strong>Erreur 500 : </strong>
        <?= h($message) ?>
        <br><br>
        Nos développeurs ont été informés du problème. Veuillez réessayer dans quelques instants.
    </p>

    <a href="<?= $this->Url->build('/') ?>" class="btn-back-home">
        <i class="material-icons">arrow_back</i> Retour à l'accueil
    </a>
</div>
