<?php
$this->layout = 'ajax';

echo $this->Form->create();
  ?>
  <div>
    <?php
    echo $this->Form->input('date_engagement', array(
      'type' => 'DATE',
      'label' => 'Date&nbsp;:'));
    
    echo $this->Form->input('date_bancaire', array(
      'type' => 'DATE',
      'label' => 'Date banque&nbsp;:'));
    ?>
  </div>
  <div>
    <?php
    echo $this->Form->input('poste_id', array(
      'label' => 'Poste&nbsp;:'));
    
    echo $this->Form->input('activite_id', array(
      'label' => 'Activité&nbsp;:'));
    ?>
  </div>
  <div>
    <?php
    echo $this->Form->input('description', array(
      'label' => 'Description&nbsp;:'));
    
    echo $this->Form->input('personne', array(
      'label' => 'Personne&nbsp;:'));
    
    echo $this->Form->input('piece', array(
      'class' => 'piece',
      'label' => 'N°&nbsp;pièce&nbsp;:'));
    ?>
  </div>
  <div class="numberInputs">
    <?php
    echo $this->Form->input('debit', array(
      'label' => 'Débit&nbsp;:'));
    
    echo $this->Form->input('credit', array(
      'label' => 'Crédit&nbsp;:'));
    
    ?>
  </div>
  <?php

  echo $this->Flash->render();
  
  // Boutons Valider et Annuler dans la même div
  echo $this->Form->submit('Valider', array(
    'class' => 'button',
    'after' => $this->Form->button('Fermer', array(
      'class' => 'button',
      'type' => 'button',
      'onclick' => 'closePopup()'))
  ));
  
echo $this->Form->end();
