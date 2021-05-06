<?php
use Authentication\PasswordHasher\LegacyPasswordHasher;

class User extends Entity {
  
  // Automatically hash passwords when they are changed.
  protected function _setPassword(string $password) {
    $hasher = new LegacyPasswordHasher([
      'hashType' => 'sha256']);
    return $hasher->hash($password);
  }
}
