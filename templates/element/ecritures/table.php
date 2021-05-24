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
    <tr ref="<?= $ecriture->id ?>">
      <td><?= $ecriture->date_engagement ?></td>
      <?php
      if (empty($no_bancaire)) {
        ?>
        <td><?= $ecriture->date_bancaire ?></td>
        <?php
      }
      ?>
      <td><?= $ecriture->poste->name ?></td>
      <td><?= $ecriture->activite->name ?></td>
      <td><?= $ecriture->description ?></td>
      <td><?= $ecriture->personne ?></td>
      <td><?= $ecriture->piece ?></td>
      <td>
        <?php
        if ($ecriture->debit != 0) {
          echo $this->Number->currency($ecriture->debit);
        }
        ?>
      </td>
      <td>
        <?php
        if ($ecriture->credit != 0) {
          echo $this->Number->currency($ecriture->credit);
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
