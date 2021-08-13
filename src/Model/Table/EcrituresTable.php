<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class EcrituresTable extends Table {
  
  public function initialize(array $config): void {
    $this->belongsTo('Postes');
    $this->belongsTo('Activites');
  }
  
  public function validationDefault(Validator $validator): Validator {
    $validator
      ->add('debit', 'amountsNotEmpty', [
        'rule' => function ($value, $context) {
            $data = $context['data'];
            return !empty($data['debit']) || !empty($data['credit']);
          },
        'message' => 'Indiquez un montant en débit ou en crédit'])
      ->notEmptyDate('date_engagement', 'La date est obligatoire')
      ->date('date_bancaire', ['ymd'], 'Date incorrecte');
    
    return $validator;
  }
}
