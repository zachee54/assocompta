<?php
$this->Html->css(
  array('users/login', 'button'),
  array('block' => true));

?>
<!-- Utiliser la même mise en forme que le formulaire de connexion -->
<section id="UserLogin">
  <?php
  echo $this->Form->create(null);
    
    echo $this->Form->label('old_password', 'Mot de passe actuel');
    echo $this->Form->password('old_password');
    
    echo $this->Form->label('new_password', 'Nouveau mot de passe');
    echo $this->Form->password('new_password');
    
    echo $this->Form->label('password_confirm',
      'Confirmez le mot de passe');
    echo $this->Form->password('password_confirm');
    
    echo $this->Form->submit('Valider', array(
      'class' => 'button'));
    
  echo $this->Form->end();
  ?>
</section>
