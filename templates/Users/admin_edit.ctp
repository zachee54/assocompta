<?php
$this->Html->css(
  array('users/login', 'button'),
  array('inline' => false));

?>
<section id="UserLogin">
  <?php
  echo $this->Form->create(null, array(
    'inputDefaults' => array(
      'div' => false)));
    
    echo $this->Form->input('nom', array(
      'label' => 'Nom&nbsp;:'));
    
    echo $this->Form->input('login', array(
      'label' => 'Login&nbsp;:'));
    
    echo $this->Form->input('mdp', array(
      'label' => 'Mot de passe&nbsp;:',
      'type' => 'password',
      'placeholder' => "garder l'existant",
      'required' => false));
    
    echo $this->Form->input('admin', array(
      'div' => true,
      'label' => 'Administrateur'));
    
    echo $this->Form->submit('Valider', array(
      'class' => 'button',
      'after' => $this->Html->link('Annuler',
        array('action' => 'index'),
        array(
          'class' => 'button',
          'escape' => false))));
  
  echo $this->Form->end();
  ?>
</section>