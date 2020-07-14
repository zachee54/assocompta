<?php
class StatsController extends AppController {
  
  public $uses = array('Ecriture');
  
  /**
   * Affiche un bilan de l'exercice par postes et par activités.
   * 
   * @param year: Année de clôture de l'exercice à afficher. La période
   *              retenue court du 01/10/N-1 au 30/09/N.
   */
  public function bilan($year=null) {
    if (!$year) {
      $year = date_format(date_create(), 'Y');
    }
    
    $ecritures = $this->Ecriture->find('all', array(
      'fields' => array(
        'SUM(Ecriture.credit - Ecriture.debit) AS montant',
        'YEAR(ADDDATE(Ecriture.date_bancaire, INTERVAL 3 MONTH)) AS exercice',
        'Poste.name',
        'Poste.recettes',
        'Activite.name'),
      'group' => array(
        'Poste.name',
        'Poste.recettes',
        'Activite.name'),
      'conditions' => array(
        "YEAR(ADDDATE(Ecriture.date_bancaire, INTERVAL 3 MONTH)) = $year")));
    
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
      'Exercice' => $ecriture['0']['exercice'],
      'Poste' => $ecriture['Poste']['name'],
      'Sens' => $ecriture['Poste']['recettes'] ? 'Recettes' : 'Dépenses',
      'Activité' => $ecriture['Activite']['name'],
      'Montant' => $ecriture['0']['montant']);
  }
}
