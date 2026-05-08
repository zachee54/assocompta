<div class="d-flex justify-content-center">
  <?= $this->Form->create($user, [
    'class' => 'd-flex flex-column gap-3' ]) ?>

    <?= $this->Form->control('nom', [
      'label' => [
        'text' => 'Nom',
        'class' => 'form-label text-primary fs-small' ],
      'class' => 'form-control' ]) ?>

    <?= $this->Form->control('login', [
      'label' => [
        'text' => 'Identifiant',
        'class' => 'form-label text-primary fs-small' ],
      'class' => 'form-control' ]) ?>

    <?= $this->Form->control('mdp', [
      'type' => 'password',
      'label' => [
        'text' => 'Mot de passe',
        'class' => 'form-label text-primary fs-small' ],
      'placeholder' => ($user->isNew() ? '' : "garder l'existant"),
      'required' => false,
      'class' => 'form-control' ]) ?>

    <div class="form-check form-switch mt-2 mb-4">
      <?= $this->Form->control('admin', [
        'type' => 'checkbox',
        'label' => [
          'text' => 'Administrateur',
          'class' => 'form-check-label' ],
        'class' => 'form-check-input' ]) ?>
    </div>

    <div class="d-flex justify-content-center gap-3">
      <?= $this->Form->submit('Valider', [
        'class' => 'btn btn-success' ]) ?>
      <?= $this->Html->link('Annuler',
        ['action' => 'index'],
        ['class' => 'btn btn-gray'] ) ?>
    </div>

  <?= $this->Form->end() ?>
</div>
