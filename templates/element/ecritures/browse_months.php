<?php
$previousMonth = $date->subMonths(1);
$nextMonth = $date->addMonths(1);

?>
<div class="d-flex justify-content-between my-5">
  <?= $this->Html->link(
    '<i class="bi bi-chevron-left me-2"></i>Mois précédent',
    [$previousMonth->year, $previousMonth->month],
    [ 'escape' => false,
      'class' => 'text-decoration-none' ]) ?>
  
  <?= $this->Html->link(
    'Mois suivant<i class="bi bi-chevron-right ms-2"></i>',
    [$nextMonth->year, $nextMonth->month],
    [ 'escape' => false,
      'class' => 'text-decoration-none' ]) ?>
</div>
