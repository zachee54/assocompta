<?php
$readonly = $this->Identity->get('readonly');

?>
<?= $this->Form->create($ecriture, [
  'class' => 'container-fluid p-0 m-0' ]) ?>
  <div class="row align-items-end gy-3 mb-4">
    <div class="col-auto d-flex gap-2">
      <?= $this->Form->control('date_engagement', [
        'default' => $date,
        'label' => [
          'text' => 'Date',
          'class' => 'fs-small text-primary' ],
        'class' => 'form-control w-initial' ]) ?>

      <?= $this->Form->control('date_bancaire', [
        'label' => [
          'text' => 'Date banque',
          'class' => 'fs-small text-primary' ],
        'class' => 'form-control w-initial' ]) ?>

      <?= $this->Form->control('rattachement', [
        'label' => [
          'text' => 'Rattachement (facultatif)',
          'class' => 'fs-small text-primary' ],
        'type' => 'select',
        'options' => $rattachement,
        'empty' => true,
        'class' => 'form-select w-initial' ]) ?>
    </div>

    <div class="col-auto d-flex gap-2">
      <?= $this->Form->control('poste_id', [
        'default' => 8,
        'label' => [
          'text' => 'Poste',
          'class' => 'fs-small text-primary' ],
        'class' => 'form-select w-initial' ]) ?>

      <?= $this->Form->control('activite_id', [
        'default' => 2,
        'label' => [
          'text' => 'Activité',
          'class' => 'fs-small text-primary' ],
        'class' => 'form-select w-initial' ]) ?>
    </div>
    
    <div class="col-auto d-flex gap-2">
      <?= $this->Form->control('description', [
        'label' => [
          'text' => 'Description',
          'class' => 'fs-small text-primary' ],
        'class' => 'form-control w-initial' ]) ?>

      <?= $this->Form->control('personne', [
        'label' => [
          'text' => 'Personne',
          'class' => 'fs-small text-primary' ],
        'class' => 'form-control w-initial' ]) ?>

      <?= $this->Form->control('piece', [
        'label' => [
          'text' => 'N° pièce',
          'class' => 'fs-small text-primary' ],
        'size' => 4,
        'class' => 'form-control w-initial' ]) ?>
    </div>

    <div class="col-auto d-flex gap-2">
      <?= $this->Form->control('debit', [
        'default' => '',
        'required' => false,
        'label' => [
          'text' => 'Débit',
          'class' => 'fs-small text-primary' ],
        'size' => 6,
        'class' => 'form-control w-initial' ]) ?>
      <?= $this->Form->control('credit', [
        'default' => '',
        'label' => [
          'text' => 'Crédit',
          'class' => 'fs-small text-primary' ],
        'size' => 6,
        'class' => 'form-control w-initial' ]) ?>
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

// La saisie d'une date d'écriture se reporte automatiquement en date banque
$this->append('scriptBottom');
  ?>
  <script type="text/javascript">
    $(function() {
      var dateEngagement = $('#date-engagement');
      var dateBancaire = $('#date-bancaire');
      
      // Réinitialiser l'animation après qu'elle aura eu lieu
      dateBancaire.get(0).addEventListener('animationend', function() {
        $(this).removeClass('autoUpdated');
      });
      
      // Recopie de la valeur + animation
      dateEngagement.change(function(event) {
        dateBancaire.val(this.value);
        dateBancaire.addClass('autoUpdated');
      });
    });
  </script>
  <?php
$this->end();
