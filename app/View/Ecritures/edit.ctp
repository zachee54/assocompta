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

echo $this->Form->end();
