<div class="months">
  <?php
  $previousMonthUrl = ($month == 1)
    ? array($year - 1, 12)
    : array($year, $month -1);
  echo $this->Html->link('<< Mois précédent', $previousMonthUrl);
  
  $nextMonthUrl = ($month == 12)
    ? array($year + 1, 1)
    : array($year, $month + 1);
  echo $this->Html->link('Mois suivant >>', $nextMonthUrl);
  ?>
</div>