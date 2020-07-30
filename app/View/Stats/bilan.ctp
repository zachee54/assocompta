<?php
$this->extend('bilan_core');

?>
<ul>
  <li>Afficher les opérations de l'exercice</li>
  <li>
    <?php
    echo $this->Html->link(
      "Afficher les opérations rattachées à l'exercice",
      array('action' => 'bilan_ajuste', $year));
    ?>
  </li>
</ul>
