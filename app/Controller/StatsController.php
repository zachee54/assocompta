<?php
class StatsController extends AppController {
  
  /**
   * Fonction SQL donnant l'année de clôture à partir d'une date.
   * Le principe est celui d'un exercice clôturé au 30/09, donc les
   * dates d'octobre à décembre renvoient l'année N+1.
   */
  const EXERCICE = 'YEAR(ADDDATE(Ecriture.date_bancaire, INTERVAL 3 MONTH))';
  
  public $uses = array('Ecriture');
  
  /**
   * Affiche un bilan de l'exercice par postes et par activités.
   * 
   * @param year: Année de clôture de l'exercice à afficher.
   */
  public function bilan($year=null) {
    if (!$year) {
      $year = date_format(date_create(), 'Y');
    }
    
    $ecritures = $this->Ecriture->find('all', array(
      'fields' => array(
        'SUM(Ecriture.credit - Ecriture.debit) AS montant',
        $this::EXERCICE.' AS exercice',
        'Ecriture.rattachement',
        'Poste.name',
        "IF (Poste.recettes, 'Recettes', 'Dépenses') AS sens",
        'Activite.name'),
      'group' => array(
        'exercice',
        'Poste.name',
        'Poste.recettes',
        'Activite.name'),
      'conditions' => array(
        'OR' => array(
          array(
            'Ecriture.rattachement IS NULL',
            $this::EXERCICE." = $year"),
          array(
            'Ecriture.rattachement' => $year)))));
    
    $this->set('ecritures', array_map(
      array('StatsController', '_flatten'),
      $ecritures));
    
    $this->set('years', $this->Ecriture->find('all', array(
      'fields' => array('DISTINCT '.$this::EXERCICE.' AS years'),
      'conditions' => array('date_bancaire IS NOT NULL'),
      'order' => 'years DESC')));
    $this->set('year', $year);
  }
  
  /**
   * Renvoie l'écriture sous la forme d'un tableau dont les clés sont
   * user-friendly.
   */
  private static function _flatten($ecriture) {
    return array(
      'Exercice' => $ecriture['0']['exercice'],
      'Poste' => $ecriture['Ecriture']['rattachement'] ? '' : $ecriture['Poste']['name'],
      'Sens' => self::_getSens($ecriture),
      'Activité' => $ecriture['Activite']['name'],
      'Montant' => $ecriture['0']['montant']);
  }
  
  private static function _getSens($ecriture) {
    $rattachement = $ecriture['Ecriture']['rattachement'];
    if ($rattachement) {
      return $rattachement < $ecriture['0']['exercice']
        ? 'Opérations en avance' : 'Exercices antérieurs';
    }
    return $ecriture['0']['sens'];
  }
  
  /**
   * Affiche les écritures correspondant aux filtres postés.
   */
  public function bilan_detail($year) {
    if ($this->request->is('post')) {
      $this->set('ecritures', $this->Ecriture->find('all', array(
        'conditions' => $this->_getDetailConditions($year))));
    }
  }
  
  /**
   * Renvoie les conditions de la requête en fonction des données
   * postées.
   * 
   * @param year  L'année de clôture.
   * 
   * @return      Un tableau de conditions au format attendu par find().
   */
  private function _getDetailConditions($year) {
    $data = $this->request->data;
    
    $conditions = array($this::EXERCICE." = $year");
    if (isset($data['Activité'])) {
      $conditions['Activite.name'] = $data['Activité'];
    }
    if (isset($data['Poste'])) {
      $conditions['Poste.name'] = $data['Poste'];
    }
    return $conditions;
  }
}
