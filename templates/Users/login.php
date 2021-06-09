<?php
$this->Html->css(
  array('users/login', 'button'),
  array('block' => true));

?>
<section id="UserLogin">
  <?php
  echo $this->Form->create(null, [
    'valueSources' => ['query'] ]);
    
    echo $this->Form->label('login', 'Identifiant');
    echo $this->Form->control('login', [
      'label' => false,
      'autofocus' => true]);
    
    echo $this->Form->label('mdp', 'Mot de passe');
    echo $this->Form->control('mdp', [
      'label' => false,
      'type' => 'password']);
  
    echo $this->Form->submit('Se connecter', array(
      'class' => 'button'));
    
  echo $this->Form->end();
  ?>
</section>
