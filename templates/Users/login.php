<?php
$this->layout = 'root';

?>
<div class="container">
  <div class="row justify-content-center">
    <?= $this->Form->create(null, [
      'valueSources' => ['query'],
      'class' => 'col-auto d-flex flex-column align-items-stretch gap-4 py-4 my-4' ]) ?>

      <h2 class="h4 text-primary text-center mb-0">Connexion</h2>

      <?= $this->Form->control('login', [
        'label' => false,
        'placeholder' => 'Identifiant',
        'autofocus' => true,
        'class' => 'form-control w-initial' ]) ?>

      <?= $this->Form->control('mdp', [
        'type' => 'password',
        'label' => false,
        'placeholder' => 'Mot de passe',
        'class' => 'form-control w-initial' ]) ?>

      <?= $this->Form->submit('OK', [
        'class' => 'btn btn-primary text-secondary w-100' ]) ?>

    <?= $this->Form->end() ?>
  </div>
</div>
