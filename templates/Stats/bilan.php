<?php
$this->extend('bilan_core');

?>
<ul>
  <li>
    <span>Opérations de l'exercice</span>
  </li>
  <li>
    <?= $this->Html->link(
      "Opérations rattachées à l'exercice",
      ['action' => 'bilan_ajuste', $year]) ?>
  </li>
</ul>
