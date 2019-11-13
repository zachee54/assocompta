<?php
$this->Html->css(
  array('ecritures/edit', 'button'),
  array('inline' => false));

echo $this->Form->create(null, array('class' => 'EcritureEditForm'));
?>
  <article>
    <div>
      <?php
      echo $this->Form->input('date_engagement', array(
        'type' => 'DATE',
        'default' => date_format(
            date_modify(date_create(), 'first day of -1 month'),
            'Y-m-d'),
        'label' => 'Date&nbsp;:'));
      
      echo $this->Form->input('date_bancaire', array(
        'type' => 'DATE',
        'label' => 'Date banque&nbsp;:'));
      
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
      
      echo $this->Form->input('debit', array(
        'required' => false,
        'label' => 'Débit&nbsp;:'));
      
      echo $this->Form->input('credit', array(
        'required' => false,
        'label' => 'Crédit&nbsp;:'));
      
      ?>
    </div>
    <div class="submit">
      <?php
      echo $this->Form->submit('Valider', array(
        'class' => 'button',
        'div' => false));
      
      if (!empty($showCancel)) {
        echo $this->Html->link('Annuler',
          array('action' => 'index'),
          array('class' => 'button cancelButton'));
      }
      ?>
    </div>
  </article>
  <?php
  
echo $this->Form->end();
