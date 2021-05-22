<?php
$this->Html->css(
  array('ecritures/index', 'ecritures/common', 'button'),
  array('block' => true));

$this->element('ecritures/click2edit');

?>
<div id="content">
  <?php

  echo $this->element('ecritures/nav_months');
  
  ?>
  <section>
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
      'year' => $year,
      'month' => $month));
    echo $browse_months;
    ?>
    
    <h1>
      <div>Relevé bancaire de <?php
        echo strftime('%B %Y', date_timestamp_get($debut));
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
  </section>
</div>
