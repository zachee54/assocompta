<?php
$this->layout = 'ajax';

echo $this->Form->create();

echo $this->Form->input('date_engagement', array(
  'type' => 'DATE',
  'label' => 'Date&nbsp;:'));

echo $this->Form->input('date_bancaire', array(
  'type' => 'DATE',
  'label' => 'Date banque&nbsp;:'));

echo $this->Form->input('poste_id', array(
  'label' => 'Poste&nbsp;:'));

echo $this->Form->input('activite_id', array(
  'label' => 'Activité&nbsp;:'));

echo $this->Form->input('description', array(
  'label' => 'Description&nbsp;:'));

echo $this->Form->input('personne', array(
  'label' => 'Personne'));

echo $this->Form->input('piece', array(
  'label' => 'N°&nbsp;pièce&nbsp;:'));

echo $this->Form->input('debit', array(
  'label' => 'Débit&nbsp;:'));

echo $this->Form->input('credit', array(
  'label' => 'Crédit&nbsp;:'));

echo $this->Form->submit('Valider');
echo $this->Form->button('Annuler', array(
  'type' => 'button',
  'onclick' => 'closePopup()'));

echo $this->Form->end();
