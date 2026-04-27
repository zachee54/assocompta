<?php
namespace App\View\Helper;

use Cake\View\Helper;

class YearHelper extends Helper {
  
  /**
   * Renvoie le nom de l'exercice sous la forme "2024-2025".
   * 
   * @param int $year L'année de clôture de l'exercice.
   * @return string
   */
  public function getExerciceName($year) {
    return ($year-1).'-'.$year;
  }
}
