<?php
$this->Html->css(
  ['users/login', 'button'],
  ['block' => true] );

?>
<section id="UserLogin">
  <?php
  echo $this->Form->create($user);
    
    echo $this->Form->label('nom', 'Nom');
    echo $this->Form->text('nom');
    
    echo $this->Form->label('login', 'Login');
    echo $this->Form->text('login');
    
    echo $this->Form->label('mdp', 'Mot de passe');
    echo $this->Form->password('mdp', [
      'placeholder' => "garder l'existant",
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
