<?php $this->assign('mainClass', ''); ?>

<div class="main">
    <h1>Identification</h1>
    <div class="formulaire">
        <div class="in_form">
            <div class="toggle-box">
                <button class="toggle-btn active" disabled>Se connecter</button>
                <?= $this->Html->link('S\'inscrire', ['action' => 'add'], ['class' => 'toggle-btn']) ?>
            </div>

            <?= $this->Form->create(null, ['class' => 'form-box', 'id' => 'loginForm']) ?>
            <h2 id="register-title">Connexion</h2>

            <?= $this->Form->control('email', ['label' => 'Adresse email', 'required' => true]) ?>
            <?= $this->Form->control('password', ['label' => 'Mot de passe', 'required' => true]) ?>

            <label>
                <?= $this->Form->checkbox('remember_me', ['value' => '1']) ?> Se souvenir de moi
            </label>

            <div class="forgot-password">
                <?= $this->Html->link('Mot de passe oublié ?', ['action' => 'forgotPassword']) ?>
            </div>

            <?= $this->Form->button('Se connecter', ['type' => 'submit']) ?>
            <?= $this->Form->end() ?>
        </div>

        <div class="google-connect-wrapper"> <p>Ou connectez-vous avec :</p>
            <?= $this->Html->link(
                $this->Html->image('https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg', ['alt' => 'G', 'class' => 'google-icon']) . ' Continuer avec Google',
                ['controller' => 'Users', 'action' => 'loginGoogle'],
                ['escape' => false, 'class' => 'btn-google']
            ) ?>
        </div>
    </div>
</div>
