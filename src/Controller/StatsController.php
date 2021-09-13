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
    
    $ecritures = $this->_getFlatEcritures($year);
    
    if ($ajuste) {
      $ecritures = array_merge(
        $ecritures,
        $this->_getFlatEcritures($year, self::ATTACHED),
        $this->_getFlatEcritures($year, self::DETACHED));
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
   * Renvoie des écritures sous forme de tableaux à 1 dimension.
   * 
   * @param int $year
   *    L'exercice souhaité.
   * 
   * @param string $attachment
   *    ATTACHED pour obtenir les écritures des autres exercices rattachées à
   *    l'exercice $year, DETACHED pour obtenir les écritures de l'exercice
   *    $year rattachées à un autre exercice, ou null pour obtenir les écritures
   *    de l'exercice sans rattachement différent.
   */
  private function _getFlatEcritures($year, $attachment = null) {
    $query = $this->_buildQueryFor($year, $attachment);
    return array_map(
      [$this, '_flatten'],
      $query->toArray());
  }
  
  /**
   * Renvoie l'écriture sous la forme d'un tableau dont les clés sont
   * user-friendly.
   */
  private static function _flatten($ecriture) {
    $flat = [
      'Poste' => $ecriture->poste->name ?? '',
      'Sens' => $ecriture->sens,
      'Activité' => $ecriture->activite->name,
      'Montant' => $ecriture->montant ];
    
    if ($ecriture->sens == self::DETACHED) {
      $flat['Montant'] = -$flat['Montant'];
    }
    
    return $flat;
  }
  
  /**
   * Construit une requête ciblant les écritures de l'exercice ou les écritures
   * rattachées ou détachées de l'exercice, selon le cas.
   * 
   * @param int $year
   *    L'exercice.
   * 
   * @param string $attachment
   *    L'une des constantes ATTACHED ou DETACHED, ou null pour obtenir les
   *    écritures de l'exercice.
   */
  private function _buildQueryFor($year, $attachment) {
    $query = $this->Ecritures->find()
      ->contain(['Activites'])
      ->select([
        'montant' => 'SUM(credit - debit)',
        'Activites.name'])
      ->group([
        'Activites.name']);
    
    $this->_setFields($query, $attachment);
    $this->_setAttachmentConditions($query, $year, $attachment);
    return $query;
  }
  
  /**
   * Définit les champs à requêter.
   * 
   * Les écritures rattachées ou détachées ne sont pas détaillées par postes. On
   * les affiche seulement dans une ligne contenant le texte des constantes
   * ATTACHED ou DETACHED, respectivement.
   * 
   * Ce texte est affiché au même niveau que le sens 'Recettes'/'Dépenses' des
   * écritures de l'exercice. Au niveau de la requête, cela signifie que
   * l'information est placée dans le même nom de champ, à savoir 'sens'.
   * 
   * @param $query      La requête à modifier.
   * @param $attachment ATTACHED, DETACHED, ou null pour le cas général.
   */
  private function _setFields($query, $attachment) {
    if ($attachment) {
      // Afficher le rattachement au même niveau que le sens Recettes/Dépenses
      $query->select(['sens' => "'$attachment'"]);
      
    } else {
      // Afficher le détail des postes
      $query
        ->contain(['Postes'])
        ->select([
          'Postes.name',
          'sens' => "IF (Postes.recettes, 'Recettes', 'Dépenses')"])
        ->group([
          'Postes.name',
          'Postes.recettes']);
    }
  }
  
  /**
   * Adapte les conditions d'une requête en fonction des rattachements attendus
   * (écritures détachées uniquement, écritures rattachées uniquement, ou cas
   * général).
   * 
   * @param $query      La requête à modifier.
   * @param $year       L'exercice.
   * @param $attachment ATTACHED, DETACHED, ou null pour le cas général.
   */
  private function _setAttachmentConditions($query, $year, $attachment) {
    if ($attachment) {
      
      // Écritures rattachées à un exercice différent de l'encaissement
      $query->where('rattachement != '.$this::EXERCICE);
      
      if ($attachment == self::ATTACHED) {
        // Rattachement à l'exercice
        $query->where(['rattachement' => $year]);
        
      } else if ($attachment == self::DETACHED) {
        // Encaissement dans l'exercice (= détachement)
        $query->where($this::EXERCICE." = $year");
      }
      
    } else {
      // Cas général : écritures de l'exercice
      $query->where($this::EXERCICE." = $year");
    }
  }
  
  /**
   * Affiche les écritures correspondant aux filtres postés.
   * 
   * @param $year:    L'année de clôture de l'exercice.
   * @param $ajuste:  true pour tenir compte du rattachement explicite
   *                  des écritures.
   */
  public function bilanDetail($year, $ajuste=false) {
    if ($this->request->is('post')) {
      $this->set('ecritures', $this->_getDetails($year, $ajuste));
    }
  }
  
  /**
   * Renvoie les écritures correspondants aux données postées.
   * 
   * @param $year:    L'année de clôture.
   * @param $ajuste:  true pour tenir compte du rattachement explicite
   *                  des écritures.
   * 
   * @return          Des entities Ecriture.
   */
  private function _getDetails($year, $ajuste) {
    $data = $this->request->getData();
    
    $query = $this->_buildExerciceQuery($year, $ajuste);
    
    // Limiter au poste et à l'activité spécifiés (ou pas, pour les totaux)
    if (!empty($data['Poste'])) {
      $query->where(['Postes.name' => $data['Poste']]);
    }
    
    if (!empty($data['Activité'])) {
      $query->where(['Activites.name' => $data['Activité']]);
    }
    
    return $query->all();
  }
  
  /**
   * Construit une requête qui sélectionne les écritures selon une condition de
   * temps dépendant du "sens" demandé.
   * 
   * Le sens peut être :
   * 
   * - 'Recettes' ou 'Dépenses', auquel cas la requête porte sur les écritures
   *   avec un encaissement dans l'exercice (cas général) ;
   * 
   * - ATTACHED ou DETACHED, auquel cas la requête porte sur les écritures
   *   respectivement attachées ou détachées explicitement de l'exercice ;
   * 
   * - rien du tout, si l'utilisateur a demandé le total d'une colonne. La
   *   requête porte soit sur les écritures avec un encaissement dans l'exercice
   *   si $ajuste est false, soit sur les écritures rattachées implicitement ou
   *   explicitement à l'exercice si $ajuste est true.
   * 
   * Si le sens est autre chose, c'est le cas général qui s'applique.
   * 
   * @param $year   L'exercice.
   * @param $ajuste true pour tenir compte du rattachement explicite des
   *                écritures.
   * 
   * @return        Un objet Query.
   */
  private function _buildExerciceQuery($year, $ajuste) {
    $query = $this->Ecritures->find()
      ->contain(['Postes', 'Activites']);
    $data = $this->request->getData();
    
    if ($ajuste) {
        
      if (empty($data['Sens'])) {
        // Total d'une colonne: rattachement implicite ou explicite à l'exercice
        return $query->where(
          "COALESCE(rattachement, ".$this::EXERCICE.") = $year");
        
      } else if (in_array($data['Sens'], [self::ATTACHED, self::DETACHED])) {
        // Rattachées ou détachées uniquement : conditions complexes spécifiques
        $this->_setAttachmentConditions($query, $year, $data['Sens']);
        return $query;
      }
    }
    
    // Par défaut : écritures de l'exercice
    return $query->where($this::EXERCICE." = $year");
  }
}
