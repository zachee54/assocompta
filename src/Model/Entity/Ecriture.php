<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Ecriture extends Entity {
  
  protected function _getDebit($debit) {
    return $this->_replaceZeroByNull($debit);
  }
  
  protected function _getCredit($credit) {
    return $this->_replaceZeroByNull($credit);
  }
  
  private function _replaceZeroByNull($amount) {
    return ((float) $amount) ? $amount : null;
  }
}
