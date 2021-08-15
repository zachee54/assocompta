<?php
namespace App\Controller;

use Cake\I18n\Date;

class StatsController extends AppController {
  
  /**
   * Fonction SQL donnant l'année de clôture à partir d'une date.
   * Le principe est celui d'un exercice clôturé au 30/09, donc les
   * dates d'octobre à décembre renvoient l'année N+1.
   */
  const EXERCICE = 'YEAR(ADDDATE(date_bancaire, INTERVAL 3 MONTH))';
  
  /** Libellé des opérations à ajouter pour affichage utilisateur. */
  const ATTACHED = 'À ajouter';
  
  /** Libellé des opérations à enlever pour affichage utilisateur. */
  const DETACHED = 'À enlever';
  
  public function initialize(): void {
    $this->loadModel('Ecritures');
  }
  
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
  public function bilanAjuste($year=null) {
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
      $year = (new Date())->year;
    }
    
    $query = $this->_getBaseQuery();
    
    // Écritures de l'exercice
    $ecritures = array_map(
      [$this, '_flatten'],
      $query
        ->cleanCopy()
        ->where([$this::EXERCICE." = $year"])
        ->toArray());
    
    if ($ajuste) {
      
      // Écritures à attacher
      $ecritures = array_merge($ecritures, array_map(
        [$this, '_flattenAttached'],
        $query
          ->cleanCopy()
          ->where([
            'rattachement' => $year,
            'rattachement != '.$this::EXERCICE])
          ->toArray()));
      
      // Écritures à détacher
      $ecritures = array_merge($ecritures, array_map(
        [$this, '_flattenDetached'],
        $query
          ->cleanCopy()
          ->where([
            $this::EXERCICE." = $year",
            'rattachement != '.$this::EXERCICE])
          ->toArray()));
    }
    
    $this->set('ecritures', $ecritures);
    
    $this->set('activites', $this->Ecritures->Activites->find('list',
      ['order' => 'id'])
      ->toArray());
    $this->set('postes', $this->Ecritures->Postes->find('list',
      ['order' => 'id'])
      ->toArray());
    $this->set('years', $this->Ecritures->find()
      ->select(['year' => $this::EXERCICE])
      ->distinct()
      ->whereNotNull('date_bancaire')
      ->order(['year' => 'DESC'])
      ->all());
    $this->set('year', $year);
  }
  
  /**
   * Crée une requête de base pour récupérer les écritures à afficher au bilan.
   */
  private function _getBaseQuery() {
    return $this->Ecritures->find()
      ->contain(['Postes', 'Activites'])
      ->select([
        'montant' => 'SUM(credit - debit)',
        'exercice' => $this::EXERCICE,
        'rattachement',
        'Postes.name',
        'sens' => "IF (Postes.recettes, 'Recettes', 'Dépenses')",
        'Activites.name'])
      ->group([
        'exercice',
        'Postes.name',
        'Postes.recettes',
        'Activites.name']);
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
    return [
      'Exercice' => $ecriture->exercice,
      'Poste' => $ecriture->poste->name,
      'Sens' => $ecriture->sens,
      'Activité' => $ecriture->activite->name,
      'Montant' => $ecriture->montant ];
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
