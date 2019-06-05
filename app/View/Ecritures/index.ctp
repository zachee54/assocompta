<?php
$this->Html->css('ecritures', array('inline' => false));
$this->Html->css('button', array('inline' => false));
$this->element('currency');

/**
 * Affiche une ligne de tableau indiquant le solde à une date donnée.
 * 
 * @param object $self    L'objet dans lequel s'exécute la vue.
 * @param string $date    La date.
 * @param float $montant  Le montant, négatif pour le débit ou positif pour le
 *                        crédit.
 */
function displaySolde($self, $date, $montant) {
  ?>
  <tr class="solde">
    <td colspan="7">Solde au <?php echo date_format($date, 'd/m/Y'); ?></td>
    <td>
      <?php
      if ($montant < 0) {
        echo $self->Number->currency(-$montant); 
      }
      ?>
    </td>
    <td>
      <?php
      if ($montant >= 0) {
        echo $self->Number->currency($montant); 
      }
      ?>
    </td>
  </tr>
  <?php
}
?>
<div id="ajax">
  <div id="ajaxBackground"></div>
  <div id="popup">
    <div id="close">
      <?php
      echo $this->Html->image('close.png', array('class' => 'close'));
      ?>
    </div>
    <div id="popupContent">
    </div>
  </div>
</div>

<table class="ecritures">
  <thead>
    <tr>
      <th>Date</th>
      <th>Date banque</th>
      <th>Poste</th>
      <th>Activité</th>
      <th>Description</th>
      <th>Personne</th>
      <th>N° pièce</th>
      <th>Débit</th>
      <th>Crédit</th>
    </tr>
  </thead>
  <tbody>
    <?php
    displaySolde($this, $debut, $a_nouveau);
    
    foreach ($ecritures as $ecriture) {
    ?>
    <tr ref="<?php echo $ecriture['Ecriture']['id']; ?>">
      <td><?php echo $ecriture['Ecriture']['engagement']; ?></td>
      <td><?php echo $ecriture['Ecriture']['bancaire']; ?></td>
      <td><?php echo $ecriture['Poste']['name']; ?></td>
      <td><?php echo $ecriture['Activite']['name']; ?></td>
      <td><?php echo $ecriture['Ecriture']['description']; ?></td>
      <td><?php echo $ecriture['Ecriture']['personne']; ?></td>
      <td><?php echo $ecriture['Ecriture']['piece']; ?></td>
      <td>
        <?php
        if ($ecriture['Ecriture']['debit'] != 0) {
          echo $this->Number->currency($ecriture['Ecriture']['debit']);
        }
        ?>
      </td>
      <td>
        <?php
        if ($ecriture['Ecriture']['credit'] != 0) {
          echo $this->Number->currency($ecriture['Ecriture']['credit']);
        }
        ?>
      </td>
    </tr>
    <?php
    }
    
    displaySolde($this, $fin, $solde);
    ?>
  </tbody>
</table>

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
<?php

// Scripts JS de fin de page
$this->element('jquery');

$this->append('scriptBottom');
?>
<script type="text/javascript">
  function closePopup() {
    $('#ajax').fadeOut();
  }

  $('tr').click(function(event) {
    $('#close').click(closePopup);
  
    var popupContent = $('#popupContent');
    popupContent.html('<?php echo $this->Html->image('ajax-loader.gif'); ?>');
    $('#ajax').fadeIn();
    
    var id = event.currentTarget.attributes.ref.value;
    $.get(
      '<?php echo $this->Html->url(array('action' => 'edit')); ?>/' + id,
      '')
    .done(function(data) {
      popupContent.html(data);
    });
  });
</script>
<?php
$this->end();