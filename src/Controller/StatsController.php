<?php
namespace App\Controller;

class StatsController extends AppController {
  
  /**
   * Fonction SQL donnant l'année de clôture à partir d'une date.
   * Le principe est celui d'un exercice clôturé au 30/09, donc les
   * dates d'octobre à décembre renvoient l'année N+1.
   */
  const EXERCICE = 'YEAR(ADDDATE(Ecriture.date_bancaire, INTERVAL 3 MONTH))';
  
  /** Libellé des opérations à ajouter pour affichage utilisateur. */
  const ATTACHED = 'À ajouter';
  
  /** Libellé des opérations à enlever pour affichage utilisateur. */
  const DETACHED = 'À enlever';
  
  public $uses = array('Ecriture');
  
  /**
   * Affiche un bilan de l'exercice par postes et par activités.
   * 
   * @param $year: Année de clôture de l'exercice à afficher.
   */
  public function bilan($year=null) {
    $this->_buildBilan($year, false);
  }
  
  /**
   * Affiche un bilan de l'exercice par postes et par activités.
   * Le bilan est ajusté en fonction du rattachement explicite des
   * écritures.
   * 
   * @param $year:  Année de clôture de l'exercice à afficher.
   */
  public function bilan_ajuste($year=null) {
    $this->_buildBilan($year, true);
  }
  
  /**
   * Affiche un bilan de l'exercice par postes et par activités.
   * 
   * @param $year:    Année de clôture de l'exercice à afficher.
   * @param $ajuste:  true pour ajuster le bilan en fonction du
   *                  rattachement explicite des écritures.
   */
  private function _buildBilan($year, $ajuste=false) {
    if (!$year) {
      $year = date_format(date_create(), 'Y');
    }
    
    $findOptions = array(
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
        $this::EXERCICE." = $year"));
    
    // Écritures normales
    $ecritures = $this->Ecriture->find('all', $findOptions);
    $ecritures = array_map(
      array('StatsController', '_flatten'),
      $ecritures);
    
    if ($ajuste) {
      // Écritures à attacher
      $attachedEcritures = $this->Ecriture->find('all',
        array_merge($findOptions, array(
          'conditions' => array(
            'Ecriture.rattachement' => $year,
            'Ecriture.rattachement != '.$this::EXERCICE))));
      $ecritures = array_merge($ecritures, array_map(
        array('StatsController', '_flattenAttached'),
        $attachedEcritures));
      
      // Écritures à détacher
      $detachedEcritures = $this->Ecriture->find('all',
        array_merge($findOptions, array(
          'conditions' => array(
            $this::EXERCICE." = $year",
            'Ecriture.rattachement != '.$this::EXERCICE))));
      $ecritures = array_merge($ecritures, array_map(
        array('StatsController', '_flattenDetached'),
        $detachedEcritures));
    }
    
    $this->set('ecritures', $ecritures);
    
    $this->set('activites', $this->Ecriture->Activite->find('list',
      array('order' => 'id')));
    $this->set('postes', $this->Ecriture->Poste->find('list',
      array('order' => 'id')));
    $this->set('years', $this->Ecriture->find('all', array(
      'fields' => array('DISTINCT '.$this::EXERCICE.' AS years'),
      'conditions' => array('date_bancaire IS NOT NULL'),
      'order' => 'years DESC')));
    $this->set('year', $year);
  }
  
  private function _getAttachedEcrituresConditions($year) {
    return array(
      'Ecriture.rattachement' => $year,
      'Ecriture.rattachement != '.$this::EXERCICE);
  }
  
  /**
   * Renvoie l'écriture sous la forme d'un tableau dont les clés sont
   * user-friendly.
   * Le sens (recettes/dépenses) est modifié pour que les écritures
   * apparaissent comme provenant se rapportant à un exercice différent.
   * Le poste est supprimé, ce qui permet que ces écritures seront
   * toutes affichées sur la même ligne, tous postes confondus.
   */
  private static function _flattenDetached($ecriture) {
    $flat = self::_flatten($ecriture);
    $flat['Poste'] = '';
    $flat['Montant'] = -$flat['Montant'];
    $flat['Sens'] = self::DETACHED;
    return $flat;
  }
  
  /**
   * Renvoie l'écriture sous la forme d'un tableau dont les clés sont
   * user-friendly.
   * Le sens (recettes/dépenses) est modifié pour que les écritures
   * apparaissent à part, comme provenant d'un exercice différent.
   * Le poste est supprimé, ce qui permet que ces écritures seront
   * toutes affichées sur la même ligne, tous postes confondus.
   */
  private static function _flattenAttached($ecriture) {
    $flat = self::_flatten($ecriture);
    $flat['Poste'] = '';
    $flat['Sens'] = self::ATTACHED;
    return $flat;
  }
  
  /**
   * Renvoie l'écriture sous la forme d'un tableau dont les clés sont
   * user-friendly.
   */
  private static function _flatten($ecriture) {
    return array(
      'Exercice' => $ecriture['0']['exercice'],
      'Poste' => $ecriture['Poste']['name'],
      'Sens' => $ecriture['0']['sens'],
      'Activité' => $ecriture['Activite']['name'],
      'Montant' => $ecriture['0']['montant']);
  }
  
  /**
   * Affiche les écritures correspondant aux filtres postés.
   * 
   * @param $year:    L'année de clôture de l'exercice.
   * @param $ajuste:  true pour tenir compte du rattachement explicite
   *                  des écritures.
   */
  public function bilan_detail($year, $ajuste=false) {
    if ($this->request->is('post')) {
      $this->set('ecritures', $this->Ecriture->find('all', array(
        'conditions' => $this->_getDetailConditions($year, $ajuste))));
    }
  }
  
  /**
   * Renvoie les conditions de la requête en fonction des données
   * postées.
   * 
   * @param $year:    L'année de clôture.
   * @param $ajuste:  true pour tenir compte du rattachement explicite
   *                  des écritures.
   * 
   * @return          Un tableau de conditions au format attendu par
   *                  find().
   */
  private function _getDetailConditions($year, $ajuste) {
    $data = $this->request->data;
    
    $conditions = $this->_getDetailTimeConditions($year, $ajuste);
    
    if (!empty($data['Poste'])) {
      $conditions['Poste.name'] = $data['Poste'];
    }
    
    if (!empty($data['Activité'])) {
      $conditions['Activite.name'] = $data['Activité'];
    }
    
    return $conditions;
  }
  
  /**
   * Renvoie les conditions de temps pour le détail des écritures.
   * 
   * @param $year:    L'année de clôture.
   * @param $ajuste:  true pour tenir compte du rattachement explicite
   *                  des écritures.
   * 
   * @return          Un tableau de conditions au format attendu par
   *                  find().
   */
  private function _getDetailTimeConditions($year, $ajuste) {
    $data = $this->request->data;
    
    if (isset($data['Sens'])) {
      
      // Lignes d'écritures attachées/détachées : conditions spécifiques
      if ($data['Sens'] == $this::ATTACHED) {
        return array(
          'Ecriture.rattachement' => $year,
          'Ecriture.rattachement != '.$this::EXERCICE);
          
      } else if ($data['Sens'] == $this::DETACHED) {
        return array(
          $this::EXERCICE." = $year",
          'Ecriture.rattachement != '.$this::EXERCICE);
      }
      
    } else if ($ajuste) {
      
      /*
       * Pas de sens => total des colonnes.
       * $ajuste => tenir compte en priorité du rattachement pour
       *  prendre les écritures attachées et ne pas prendre les
       *  écritures détachées.
       */
      return array(
        "COALESCE(Ecriture.rattachement, ".$this::EXERCICE.") = $year");
    }
    
    // Par défaut : écritures de l'exercice
    return array($this::EXERCICE." = $year");
  }
}
