<?php
$this->Html->script('ecritures/date', ['block' => 'scriptBottom']);
$readonly = $this->Identity->get('readonly');

?>
<?= $this->Form->create($ecriture, [
  'class' => 'container-fluid p-0 m-0' ]) ?>
  <div class="row gy-3 mb-4">
    <div class="col-auto">
       <div class="d-flex align-items-end gap-2">
        <?= $this->Form->control('date_engagement', [
          'default' => $date,
          'label' => [
            'text' => 'Date',
            'class' => 'fs-small text-primary' ],
          'error' => false,
          'class' => 'form-control w-initial' ]) ?>

        <?php if (!$ecriture->date_bancaire): ?>
          <button id="pointage-button" type="button"
            class="btn btn-secondary fs-xsmall p-1 my-2">
            pointer
          </button>
        <?php endif ?>
        <div id="pointage" style="<?= $ecriture->date_bancaire ? null : 'display:none' ?>">
          <?= $this->Form->control('date_bancaire', [
            'label' => [
              'text' => 'Date banque',
              'class' => 'fs-small text-primary' ],
            'error' => false,
            'class' => 'form-control w-initial' ]) ?>
        </div>

        <?= $this->Form->control('rattachement', [
          'label' => [
            'text' => 'Rattachement (facultatif)',
            'class' => 'fs-small text-primary' ],
          'type' => 'select',
          'options' => $rattachement,
          'empty' => true,
          'error' => false,
          'class' => 'form-select w-initial' ]) ?>
      </div>
      <div class="vstack align-items-start">
        <?= $this->Form->error('date_engagement') ?>
        <?= $this->Form->error('date_bancaire') ?>
        <?= $this->Form->error('rattachement') ?>
      </div>
    </div>

    <div class="col-auto">
      <div class="d-flex align-items-end gap-2">
        <?= $this->Form->control('poste_id', [
          'default' => 8,
          'label' => [
            'text' => 'Poste',
            'class' => 'fs-small text-primary' ],
          'error' => false,
          'class' => 'form-select w-initial' ]) ?>

        <?= $this->Form->control('activite_id', [
          'default' => 2,
          'label' => [
            'text' => 'Activité',
            'class' => 'fs-small text-primary' ],
          'error' => false,
          'class' => 'form-select w-initial' ]) ?>
      </div>
      <div class="vstack align-items-start">
        <?= $this->Form->error('poste_id') ?>
        <?= $this->Form->error('activite_id') ?>
      </div>
    </div>
    
    <div class="col-auto">
      <div class="d-flex align-items-end gap-2">
        <?= $this->Form->control('description', [
          'label' => [
            'text' => 'Description',
            'class' => 'fs-small text-primary' ],
          'error' => false,
          'class' => 'form-control w-initial' ]) ?>

        <?= $this->Form->control('personne', [
          'label' => [
            'text' => 'Personne',
            'class' => 'fs-small text-primary' ],
          'error' => false,
          'class' => 'form-control w-initial' ]) ?>

        <?= $this->Form->control('piece', [
          'label' => [
            'text' => 'N° pièce',
            'class' => 'fs-small text-primary' ],
          'error' => false,
          'size' => 4,
          'class' => 'form-control w-initial' ]) ?>
      </div>
      <div class="vstack align-items-start">
        <?= $this->Form->error('description') ?>
        <?= $this->Form->error('personne') ?>
        <?= $this->Form->error('piece') ?>
      </div>
    </div>

    <div class="col-auto">
      <div class="d-flex align-items-end gap-2">
        <?= $this->Form->control('debit', [
          'default' => '',
          'required' => false,
          'label' => [
            'text' => 'Débit',
            'class' => 'fs-small text-primary' ],
          'error' => false,
          'size' => 6,
          'class' => 'form-control w-initial' ]) ?>
        <?= $this->Form->control('credit', [
          'default' => '',
          'label' => [
            'text' => 'Crédit',
            'class' => 'fs-small text-primary' ],
          'error' => false,
          'size' => 6,
          'class' => 'form-control w-initial' ]) ?>
      </div>
      <div class="vstack align-items-start">
        <?= $this->Form->error('debit') ?>
        <?= $this->Form->error('credit') ?>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-4 offset-4 d-flex justify-content-center gap-2">
      <?php if (!$readonly): ?>
        <?= $this->Form->button('Valider', [
          'class' => 'btn btn-success' ]) ?>
      <?php endif ?>
      <?php if (!empty($showCancel)): ?>
        <?= $this->Html->link('Annuler',
          $this->request->referer() ?? '/',
          ['class' => 'btn btn-gray'] ) ?>
      <?php endif ?>
    </div>

    <div class="col-4 text-end">
      <?php if (!$readonly && !$ecriture->isNew()): ?>
        <?= $this->Form->button('Supprimer', [
          'formaction' => $this->Url->build([
            'action' => 'delete',
            $ecriture->id ]),
          'formmethod' => 'post',
          'class' => 'btn btn-danger' ]) ?>
      <?php endif ?>
    </div>
  </div>
<?= $this->Form->end();

