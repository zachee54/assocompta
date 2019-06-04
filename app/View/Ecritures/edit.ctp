<?php
$this->layout = 'ajax';

echo $this->Form->create();

echo $this->Form->input('poste_id', array(
  'label' => 'Poste&nbsp;:'));

echo $this->Form->input('activite_id', array(
  'label' => 'Activité&nbsp;:'));

echo $this->Form->end();
