<?php
$this->Html->css('users/edit', array('inline' => false));

echo $this->Form->create();
  
  echo $this->Html->link(
    '&#8617; Retour à la liste',
    array('action' => 'index'),
    array('escape' => false));

  echo $this->Form->input('nom', array(
    'label' => 'Nom&nbsp;:'));
  
  echo $this->Form->input('login', array(
    'label' => 'Login&nbsp;:'));
  
  echo $this->Form->input('admin', array(
    'label' => 'Administrateur'));
  
  echo $this->Form->input('mdp', array(
    'label' => 'Mot de passe&nbsp;:',
    'type' => 'password'));
  
  echo $this->Form->submit('Valider');
  
echo $this->Form->end();