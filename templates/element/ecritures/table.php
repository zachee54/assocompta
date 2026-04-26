<table class="table table-custom-light table-hover fs-7">
  <thead class="table-primary text-center">
    <tr>
      <th>Date</th>
      <?php if (empty($no_bancaire)): ?>
        <th>Date banque</th>
      <?php endif ?>
      <th>Poste</th>
      <th>Activité</th>
      <th>Description</th>
      <th>Personne</th>
      <th>N° pièce</th>
      <th>Débit</th>
      <th>Crédit</th>
    </tr>
  </thead>
  <tbody class="table-group-divider text-custom-light">
    <?php if (!empty($displaySoldes)): ?>
      <?= $this->element('ecritures/solde', [
        'date '=> $date->firstOfMonth(),
        'montant' => $a_nouveau ]) ?>
    <?php endif ?>

    <?php foreach ($ecritures as $ecriture): ?>
      <tr class="position-relative">
        <td><?= $ecriture->date_engagement ?></td>
        <?php if (empty($no_bancaire)): ?>
          <td><?= $ecriture->date_bancaire ?></td>
        <?php endif ?>
        <td><?= $ecriture->poste->name ?></td>
        <td><?= $ecriture->activite->name ?></td>
        <td><?= $ecriture->description ?></td>
        <td><?= $ecriture->personne ?></td>
        <td><?= $ecriture->piece ?></td>
        <td class="table-secondary text-end">
          <?php if ($ecriture->debit != 0): ?>
            <?= $this->Number->currency($ecriture->debit) ?>
          <?php endif ?>
        </td>
        <td class="table-secondary text-end">
          <?php if ($ecriture->credit != 0): ?>
            <?= $this->Number->currency($ecriture->credit) ?>
          <?php endif ?>

          <?= $this->Html->link('',
            ['action' => 'edit', $ecriture->id],
            ['class' => 'stretched-link'] ) ?>
        </td>
      </tr>
    <?php endforeach ?>

    <?php if (!empty($displaySoldes)): ?>
      <?= $this->element('ecritures/solde', [
        'date' => $date->endOfMonth(),
        'montant' => $solde ]) ?>
    <?php endif ?>
  </tbody>
</table>
