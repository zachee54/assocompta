<?php
$this->Html->css(
  array('ecritures/index', 'ecritures/common', 'button'),
  array('inline' => false));
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
<div id="content">
  <?php

  echo $this->element('ecritures/nav_months');
  
  ?>
  <section>
    <?php
    // Sur cette page, afficher les flashs ici plutôt que dans le layout principal
    echo $this->Flash->render();
    
    ?>
    <fieldset>
      <legend>Ajouter une écriture</legend>
      <?php echo $this->element('ecritures/edit_form'); ?>
    </fieldset>
    
    <h1>
      <div>Relevé bancaire de <?php
        echo strftime('%B %Y', date_timestamp_get($debut));
      ?></div>
    </h1>
    
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
    
    <div class="center">
      <?php
      echo $this->Html->link(
        'Nouvelle écriture',
        array('action' => 'edit'),
        array('class' => 'button addButton addEcriture'));
      ?>
    </div>
    
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
    
    <h1>
      <div>Opérations en attente</div>
    </h1>
    
    <table class="ecritures">
      <thead>
        <tr>
          <th>Date</th>
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
        foreach ($enAttente as $ecriture) {
        ?>
        <tr ref="<?php echo $ecriture['Ecriture']['id']; ?>">
          <td><?php echo $ecriture['Ecriture']['engagement']; ?></td>
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
        ?>
      </tbody>
    </table>
  </section>
</div>
<?php

// Scripts JS de fin de page
$this->element('jquery');

$this->append('scriptBottom');
?>
<script type="text/javascript">
  $('tr[ref]').click(function(event) {
    window.location = '<?php
      echo $this->Html->url(array('action' => 'edit'));
    ?>/' + $(event.currentTarget).attr('ref');
  });
</script>
<?php
$this->end();
