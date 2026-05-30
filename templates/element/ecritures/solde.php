<?php
/**
 * Affiche une ligne de table indiquant le solde à la date indiquée.
 * 
 * @var $date     La date.
 * @var $montant  Le montant.
 */
?>
<tr class="table-primary fw-bold">
  <td colspan="7" class="fst-italic">
    Solde au <?= $date ?>
  </td>
  <td class="text-end">
    <?php if ($montant < 0): ?>
      <?= $this->Number->currency(-$montant) ?>
    <?php endif ?>
  </td>
  <td class="text-end">
    <?php if ($montant >= 0): ?>
      <?= $this->Number->currency($montant) ?>
    <?php endif ?>
  </td>
</tr>
