<?php
class Ecriture extends AppModel {
  
  public $virtualFields = array(
    'engagement' => "DATE_FORMAT(date_engagement, '%d/%m/%Y')",
    'bancaire' => "DATE_FORMAT(date_bancaire, '%d/%m/%Y')");
  
  public $belongsTo = array('Poste', 'Activite');
  
  public $hasAndBelongsToMany = array('Frere');
  
}