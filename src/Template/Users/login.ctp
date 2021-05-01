<?php
$this->Html->css(
  array('users/login', 'button'),
  array('block' => true));

?>
<section id="UserLogin">
  <?php
  echo $this->Form->create();
    
    echo $this->Form->label('login',
      ['Identifiant&nbsp;:'],
      ['escape' => false]);
    echo $this->Form->text('login',
      ['autofocus' => true]);
    
    echo $this->Form->label('mdp',
      ['Mot de passe&nbsp;:'],
      ['escape' => false]);
    echo $this->Form->password('mdp');
  
    echo $this->Form->submit('Se connecter', array(
      'class' => 'button'));
    
  echo $this->Form->end();
  ?>
</section>
