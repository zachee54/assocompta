<?php
$this->Html->css(
  array('users/login', 'button'),
  array('block' => true));

?>
<section id="UserLogin">
  <?php
  echo $this->Form->create();
  
    echo $this->Form->control('login', array(
      'div' => false,
      'autofocus' => true,
      'label' => 'Identifiant&nbsp;:'));
    
    echo $this->Form->control('mdp', array(
      'div' => false,
      'label' => 'Mot de passe&nbsp;:',
      'type' => 'password'));
  
    echo $this->Form->submit('Se connecter', array(
      'class' => 'button'));
    
  echo $this->Form->end();
  ?>
</section>