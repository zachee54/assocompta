<?php
$this->extend('main');

?>
<div class="container">
  <div class="vstack align-items-center">
    <div>
      <h2 class="h2 text-primary">
        <div>Modification d'une écriture</div>
      </h2>
      <?= $this->element('ecritures/edit_form', [
        'showCancel' => true ]) ?>
    </div>
  </div>
</div>
