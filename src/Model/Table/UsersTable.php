<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
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
  
  public function buildRules(RulesChecker $rules): RulesChecker {
    $rules->add($rules->isUnique(
      ['login'],
      'Ce login est déjà utilisé'));
    
    // Vérifier qu'on ne déshabilite pas le dernier admin
    $rules->addUpdate(function ($entity, $options) {

      if ($entity->get('admin')) {
        return true;
      }
      
      $count = $this->find()
        ->where([
          'admin' => true,
          'id !=' => $entity->get('id')])
        ->count();
    
      return $count > 0;
      
    },
    'adminsUpdate',
    [ 'errorField' => 'admin',
      'message' => 'Il s\'agit du seul administrateur']);
    
    return $rules;
  }
  
  /**
   * Vérifie qu'il reste un autre administrateur
   * 
   * @param $entity L'entité à exclure de la recherche.
   */
  private function _hasOtherAdmin(\App\Model\Entity\User $entity) {
  }
}
