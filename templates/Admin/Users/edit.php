<?php
$this->Html->css(
  ['users/login', 'button'],
  ['block' => true] );

?>
<section id="UserLogin">
  <?php
  echo $this->Form->create($user);
    
    echo $this->Form->label('nom', 'Nom');
    echo $this->Form->control('nom', [
      'label' => false]);
    
    echo $this->Form->label('login', 'Login');
    echo $this->Form->control('login', [
      'label' => false]);
    
    echo $this->Form->label('mdp', 'Mot de passe');
    echo $this->Form->control('mdp', [
      'type' => 'password',
      'label' => false,
      'placeholder' => ($user->isNew() ? '' : "garder l'existant"),
      'required' => false ]);
    
    echo $this->Form->control('admin', [
      'label' => 'Administrateur',
      'type' => 'checkbox' ]);
    
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
