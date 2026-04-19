<?php
$this->element('ecritures/click2edit');

?>
<div class="d-flex gap-4">

  <div style="min-width:7rem">
    <?= $this->element('ecritures/nav_months') ?>
  </div>

  <div>
    <?php
    // Sur cette page, afficher les flashs ici plutôt que dans le layout principal
    echo $this->Flash->render();
    
    if (!$this->Identity->get('readonly')) {
      ?>
      <article class="align_left">
        <fieldset>
          <legend>Ajouter une écriture</legend>
          <?php echo $this->element('ecritures/edit_form'); ?>
        </fieldset>
      </article>
      <?php
    }
    
    $browse_months = $this->element('ecritures/browse_months', array(
      'year' => $date->year,
      'month' => $date->month));
    echo $browse_months;
    ?>
    
    <h1>
      <div>Relevé bancaire <?php
        // Cf. http://userguide.icu-project.org/formatparse/datetime
        $monthName = $this->Time->format($date, 'MMMM yyyy');
        
        echo in_array(substr($monthName, 0, 1), ['a', 'o']) ? "d'" : "de ";
        echo $monthName;
      ?></div>
    </h1>
    
    <article>
      <?php
      echo $this->element('ecritures/table', array(
        'displaySoldes' => true));
      
      echo $browse_months;
      ?>
    </article>
    
    <h1>
      <div>Opérations en attente</div>
    </h1>
   
    <article>
      <?php
      echo $this->element('ecritures/table', array(
        'ecritures' => $enAttente,
        'no_bancaire' => true));
      ?>
    </article>
  </div>
</div>
