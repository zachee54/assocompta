<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table {
  
  public function validationDefault(Validator $validator): Validator {
    $validator
      ->requirePresence('nom', 'create')
      ->requirePresence('login', 'create')
      ->requirePresence('mdp', 'create',  'Un mot de passe est nécessaire')
      ->notEmpty('login')
      ->alphaNumeric('login', 'Caractères spéciaux interdits');
    return $validator;
  }
}
