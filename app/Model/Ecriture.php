<?php
class Ecriture extends AppModel {
  
  public $virtualFields = array(
    'engagement' => "DATE_FORMAT(date_engagement, '%d/%m/%Y')",
    'bancaire' => "DATE_FORMAT(date_bancaire, '%d/%m/%Y')");
  
  public $belongsTo = array('Poste', 'Activite');
  
  public $hasAndBelongsToMany = array('Frere');
  
  public $validate = array(
    'debit' => array(
      'rule' => 'amountsNotEmpty',
      'message' => 'Indiquez un montant en débit ou en crédit'));
  
  public function amountsNotEmpty($check) {
    $data = $this->data['Ecriture'];
    return !empty($data['debit']) || !empty($data['credit']);
  }
  
  public function beforeSave($options = array()) {
    $this->_zeroIfEmpty('debit');
    $this->_zeroIfEmpty('credit');
    return true;
  }
  
  /**
   * Remplace une chaîne vide par un zéro dans le champ spécifié, s'il existe.
   * 
   * @param string $field Nom du champ.
   */
  private function _zeroIfEmpty($field) {
    if (isset($this->data['Ecriture'][$field])
      && $this->data['Ecriture'][$field] === '') {
        
      $this->data['Ecriture'][$field] = 0;
    }
  }
}