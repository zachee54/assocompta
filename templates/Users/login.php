<?php
$this->Html->css(
  array('users/login', 'button'),
  array('block' => true));

?>
<section id="UserLogin">
  <?php
  echo $this->Form->create();
    
    echo $this->Form->label('login', 'Identifiant');
    echo $this->Form->text('login', [
      'autofocus' => true]);
    
    echo $this->Form->label('mdp', 'Mot de passe');
    echo $this->Form->password('mdp', [
      'type' => 'password']);
  
    echo $this->Form->submit('Se connecter', array(
      'class' => 'button'));
    
  echo $this->Form->end();
  ?>
</section>
