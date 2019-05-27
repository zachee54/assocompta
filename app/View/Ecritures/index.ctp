<?php
$this->Html->css('ecritures', array('inline' => false));
$this->element('currency');

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
  ?>
  </tbody>
</table>
