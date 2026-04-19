<?php
namespace App\Controller;

class EcrituresController extends AppController {
  
  /**
   * Affiche la liste des écritures d'un mois, la liste des écritures en attente
   * et un formulaire de saisie d'une nouvelle écriture.
   * 
   * @param $year int   L'année à afficher.
   * @param $month int  Le numéro du mois à afficher.
   */
  public function index($year = null, $month = null) {
    
    // Inclure le traitement du formulaire d'ajout d'une écriture
    $this->edit();
    
    $maxDate = $this->Ecritures->find()
      ->select('date_bancaire')
      ->orderDesc('date_bancaire')
      ->first()
      ->date_bancaire;
    
    $minDate = $this->Ecritures->find()
      ->select('date_bancaire')
      ->orderAsc('date_bancaire')
      ->first()
      ->date_bancaire;
    
    // Année et mois par défaut : les plus récents saisis
    $date = (!$year || !$month)
      ? $maxDate
      : new \Cake\I18n\Date("$year-$month-1");
    
    $this->set(compact('date', 'minDate', 'maxDate'));
    
    $this->_setSoldesDebutFin($date);
    
    $query = $this->Ecritures->find();
    $this->set('ecritures', $query
      ->contain(['Postes', 'Activites'])
      ->where(
        $query->expr()->between(
          'date_bancaire',
          $date->firstOfMonth(),
          $date->endOfMonth() ))
      ->order(['date_bancaire', 'created']));
    
    $this->set('enAttente',
      $this->Ecritures->find()
      ->contain(['Postes', 'Activites'])
      ->whereNull('date_bancaire')
      ->order(['created']));
  }
  
  /**
   * Met à disposition de la vue les soldes bancaires de début et de fin du
   * mois.
   * 
   * @param $date Une date du mois.
   */
  private function _setSoldesDebutFin($date) {
    $this->set('a_nouveau', $this->_getSoldeAt($date->firstOfMonth()));
    $this->set('solde', $this->_getSoldeAt($date->endOfMonth()));
  }
  
  /**
   * Renvoie le solde à la date spécifiée.
   * 
   * @param $date La date.
   */
  private function _getSoldeAt($date) {
    $query = $this->Ecritures->find();
    return $query
      ->select(['solde' => $query->func()->sum('credit-debit')])
      ->where(
        $query->expr()->lte('date_bancaire', $date) )
      ->first()
      ->solde;
  }
  
  /**
   * Met à disposition de la vue la liste des mois contenant des écritures.
   */
  private function _setMonths() {
    $query = $this->Ecritures->find();
    $month = $query->func()->month([
      'date_bancaire' => 'identifier']);
    $year = $query->func()->year([
      'date_bancaire' => 'identifier']);
    
    $yearsMonths = $query
      ->select([
        'month' => $month,
        'year' => $year])
      ->distinct(['month', 'year'])
      ->whereNotNull('date_bancaire')
      ->order(['date_bancaire' => 'DESC'])
      ->all();
      
    $this->set('months', $this->_groupMonthsByYear($yearsMonths));
  }
  
  /**
   * Réorganise une liste d'années et mois en tableau associatif à deux niveaux
   * contenant les années puis les numéros des mois.
   * 
   * @param array $yearsMonths
   *                ResultSet contenant les champs 'year' et 'month'.
   * 
   * @return array  Un tableau à double entrée contenant les années et les
   *                numéros des mois
   */
  private function _groupMonthsByYear($yearsMonths) {
    $monthsByYear = [];
    foreach ($yearsMonths as $yearMonth) {
      $year = $yearMonth->year;
      if (!array_key_exists($year, $monthsByYear)) {
        $monthsByYear[$year] = [];
      }
      $monthsByYear[$year][] = $yearMonth->month;
    }
    return $monthsByYear;
  }
  
  /**
   * Édite ou ajoute une écriture.
   * 
   * @param int $id L'identifiant de l'écriture.
   */
  public function edit($id = null) {
    if ($id === null) {
      $ecriture = $this->Ecritures->newEmptyEntity();
    } else {
      $ecriture = $this->Ecritures->get($id);
    }
    
    if ($this->request->is(array('put', 'post')) && $this->_checkReadOnly()) {
      $data = $this->request->getData();
      $this->Ecritures->patchEntity($ecriture, $data);
      
      if ($this->Ecritures->save($ecriture)) {
        $this->Flash->success("L'écriture a été sauvegardée");
        $yearMonth = $this->_getYearMonth($ecriture);
        $ecriture = $this->Ecritures->newEmptyEntity();
        $this->redirect([
          'action' => 'index',
          $yearMonth['year'],
          $yearMonth['month'] ]);
      } else {
        $this->Flash->error('Erreur pendant la sauvegarde');
      }
    }
    
    $this->set('ecriture', $ecriture);
    $this->_setMonths();
    $this->set('postes', $this->Ecritures->Postes->find('list'));
    $this->set('activites', $this->Ecritures->Activites->find('list'));
    $this->set('rattachement', $this->_buildRattachementOptions($ecriture));
  }
  
  /**
   * Renvoie le mois de la date bancaire de l'écriture.
   * 
   * @param $ecriture Ecriture 
   *                L'écriture à examiner.
   * 
   * @return array  Un tableau contenant les clés 'year' et 'month' avec valeurs
   *                numériques, ou avec des chaînes vides si la date bancaire
   *                n'est pas fournie.
   */
  private function _getYearMonth($ecriture) {
    $dateBancaire = $ecriture->date_bancaire;
    if (!$dateBancaire) {
      return [
        'year' => '',
        'month' => ''];
    }

    return [
      'year' => $dateBancaire->format('Y'),    // Année sur 4 chiffres
      'month' => $dateBancaire->format('n')];  // Mois sur 1 ou 2 chiffres
  }
  
  /**
   * Renvoie des options de rattachement appropriées pour l'édition de
   * l'écriture spécifiée.
   * Les rattachements proposés sont affichés sous la forme "N-1/N".
   * 
   * Les dates de référence sont la date actuelle, la date de l'écriture, la
   * date d'encaissement et la date de rattachement actuelle. Les rattachements
   * proposés s'étendent de 1 an avant à 1 an après les dates de référence.
   * 
   * @param $ecriture Une entité Ecriture.
   * @return  array   Des options pour un input select.
   */ 
  private function _buildRattachementOptions($ecriture) {
    $refDates = [new \Cake\I18n\Date()];
    if ($ecriture->date_engagement) {
      $refDates[] = $ecriture->date_engagement;
    }
    if ($ecriture->date_bancaire) {
      $refDates[] = $ecriture->date_bancaire;
    }
    if ($ecriture->rattachement) {
      $refDates[] = \Cake\I18n\Date::create($ecriture->rattachement, 1, 1, 0, 0, 0);
    }
    
    // Garder une année de marge avant et après
    $max = max($refDates)->year + 1;
    $min = min($refDates)->year - 1;
    
    $result = [];
    for ($year = $max; $year >= $min; $year--) {  // Ordre chronologique inverse
      $result[$year] = ($year-1)."/".$year;
    }
    return $result;
  }
  
  /**
   * Efface une écriture.
   * 
   * @param int id  L'identifiant de l'écriture à supprimer.
   */
  public function delete($id) {
    if ($id && $this->request->is('put', 'post') && $this->_checkReadOnly()) {
      if ($this->Ecritures->deleteAll(['id' => $id])) {
        $this->Flash->success("L'écriture a été supprimée");
      } else {
        $this->Flash->error("Erreur lors de la suppression de l'écriture");
      }
    }
    $this->redirect('/');
  }
  
  /**
   * Vérifie que l'utilisateur a les droits en écriture.
   */
  private function _checkReadOnly() {
    $user = $this->Authentication->getIdentity();
    if ($user->readonly) {
      $this->Flash->error(
        'Votre profil ne vous permet de modifier les données');
      return false;
    }
    return true;
  }
}
