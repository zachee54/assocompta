<?php
$this->extend('bilan_core');

?>
<ul>
  <li>
    <span>Opérations de l'exercice</span>
  </li>
  <li>
    <?php
    echo $this->Html->link(
      "Opérations rattachées à l'exercice",
      array('action' => 'bilan_ajuste', $year));
    ?>
  </li>
</ul>
