<tr class="solde">
  <td colspan="7">Solde au <?php echo date_format($date, 'd/m/Y'); ?></td>
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
