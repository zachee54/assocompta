<?php
$this->Html->css(
  array('users/login', 'button'),
  array('block' => true));

?>
<!-- Utiliser la même mise en forme que le formulaire de connexion -->
<section id="UserLogin">
  <?php
  echo $this->Form->create(false, array(
      'inputDefaults' => array(
        'div' => false,
        'type' => 'password')));
    
    echo $this->Form->control('old_password', array(
      'label' => 'Mot de passe actuel&nbsp;:'));
    
    echo $this->Form->control('new_password', array(
      'label' => 'Nouveau mot de passe&nbsp;:'));
    
    echo $this->Form->control('password_confirm', array(
      'label' => 'Confirmez le mot de passe&nbsp;:'));
    
    echo $this->Form->submit('Valider', array(
      'class' => 'button'));
    
  echo $this->Form->end();
  ?>
</section>
