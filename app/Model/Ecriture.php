<?php
class Ecriture extends AppModel {
  
  public $belongsTo = array('Poste', 'Activite');
  
  public $hasAndBelongsToMany = array('Frere');
  
}