<?php
$this->extend('bilan_core');

?>
<ul>
  <li>
    <span>Afficher les opérations de l'exercice</span>
  </li>
  <li>
    <?php
    echo $this->Html->link(
      "Afficher les opérations rattachées à l'exercice",
      array('action' => 'bilan_ajuste', $year));
    ?>
  </li>
</ul>
