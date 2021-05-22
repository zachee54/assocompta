<table class="ecritures">
  <thead>
    <tr>
      <th>Date</th>
      <?php
      if (empty($no_bancaire)) {
        ?>
        <th>Date banque</th>
        <?php
      }
      ?>
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
    if (!empty($displaySoldes)) {
      echo $this->element('ecritures/solde', array(
      'date' => $debut,
      'montant' => $a_nouveau));
    }
    
    foreach ($ecritures as $ecriture) {
    ?>
    <tr ref="<?php echo $ecriture['Ecriture']['id']; ?>">
      <td><?php echo $ecriture['Ecriture']['engagement']; ?></td>
      <?php
      if (empty($no_bancaire)) {
        ?>
        <td><?php echo $ecriture['Ecriture']['bancaire']; ?></td>
        <?php
      }
      ?>
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
    
    if (!empty($displaySoldes)) {
      echo $this->element('ecritures/solde', array(
        'date' => $fin,
        'montant' => $solde));
    }
    ?>
  </tbody>
</table>
