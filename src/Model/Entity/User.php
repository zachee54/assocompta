<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;

class User extends Entity {
  
  // Automatically hash passwords when they are changed.
  protected function _setMdp(string $password) {
    $hasher = new DefaultPasswordHasher();
    return $hasher->hash($password);
  }
}
