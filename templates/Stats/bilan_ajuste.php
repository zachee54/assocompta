<?php
$this->extend('bilan_core');
$this->assign('ajuste', 1);

?>
<ul>
  <li>
    <?php
    echo $this->Html->link(
      "Opérations de l'exercice",
      array('action' => 'bilan', $year));
    ?>
  </li>
  <li>
    <span>Opérations rattachées à l'exercice</span>
  </li>
</ul>
