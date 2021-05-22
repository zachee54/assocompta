<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class EcrituresTable extends Table {
  
  public function initialize(array $config): void {
    $this->belongsTo('Postes');
    $this->belongsTo('Activites');
  }
  
  public $validate = array(
    'debit' => array(
      'rule' => 'amountsNotEmpty',
      'message' => 'Indiquez un montant en débit ou en crédit'),
    'date_engagement' => array(
      'notBlank' => array(
        'rule' => 'notBlank',
        'message' => 'La date est obligatoire'),
      'date' => array(
        'rule' => array('date', 'ymd'),
        'message' => 'Date incorrecte')),
    'date_bancaire' => array(
      'rule' => array('date', 'ymd'),
      'message' => 'Date incorrecte'));
  
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