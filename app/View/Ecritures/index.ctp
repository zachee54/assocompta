<?php
$this->Html->css('ecritures', array('inline' => false));
$this->element('currency');

/**
 * Affiche une de tableau ligne indiquant le solde à une date donnée.
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
    <tr onclick="window.location='<?php
      echo $this->Html->url(array('action' => 'edit', $ecriture['Ecriture']['id']));
      ?>'">
      <td><?php echo $ecriture['Ecriture']['engagement']; ?></td>
      <td><?php echo $ecriture['Ecriture']['bancaire']; ?></td>
      <td><?php echo $ecriture['Poste']['name']; ?></td>
      <td><?php echo $ecriture['Activite']['name']; ?></td>
      <td><?php echo $ecriture['Ecriture']['description']; ?></td>
      <td><?php echo $ecriture['Ecriture']['personne']; ?></td>
      <td><?php echo $ecriture['Ecriture']['piece']; ?></td>
      <td>
        <?php
        if ($ecriture['Ecriture']['debit']) {
          echo $this->Number->currency($ecriture['Ecriture']['debit']);
        }
        ?>
      </td>
      <td>
        <?php
        if ($ecriture['Ecriture']['credit']) {
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
