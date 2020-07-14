<?php
class StatsController extends AppController {
  
  public $uses = array('Ecriture');
  
  public function bilan() {
    $ecritures = $this->Ecriture->find('all', array(
      'fields' => array(
        'SUM(Ecriture.credit - Ecriture.debit) AS montant',
        'Poste.name',
        'Poste.recettes',
        'Activite.name'),
      'group' => array(
        'Poste.name',
        'Poste.recettes',
        'Activite.name')));
    $this->set('ecritures', array_map(
      array('StatsController', '_flatten'),
      $ecritures));
  }
  
  /**
   * Renvoie l'écriture sous la forme d'un tableau dont les clés sont
   * user-friendly.
   */
  private static function _flatten($ecriture) {
    return array(
      'Poste' => $ecriture['Poste']['name'],
      'Sens' => $ecriture['Poste']['recettes'] ? 'Recettes' : 'Dépenses',
      'Activité' => $ecriture['Activite']['name'],
      'Montant' => $ecriture['0']['montant']);
  }
}
