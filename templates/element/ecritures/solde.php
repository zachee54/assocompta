<?php
/**
 * Affiche une ligne de table indiquant le solde à la date indiquée.
 * 
 * @var $date La date.
 */
?>
<tr class="solde">
  <td colspan="7">Solde au <?= $date->toDateString() ?></td>
  <td>
    <?php
    if ($montant < 0) {
      echo $this->Number->currency(-$montant); 
    }
    ?>
  </td>
  <td>
    <?php
    if ($montant >= 0) {
      echo $this->Number->currency($montant); 
    }
    ?>
  </td>
</tr>
