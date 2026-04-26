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
    $ecriture = $this->Ecritures->newEmptyEntity();
    $this->set('ecriture', $ecriture);
    
    $monthsByYear = $this->_setMonthsByYear();
    
    // Date par défaut : celle de l'écriture la plus récente
    if (!$year || !$month) {
      $year = array_key_first($monthsByYear);
      $month = array_key_first($monthsByYear[$year]);
    }
    
    $date = new \Cake\I18n\Date("$year-$month-1");
    $this->set('date', $date);
    
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
    
    $this->set('enAttente', $this->Ecritures->find()
      ->contain(['Postes', 'Activites'])
      ->whereNull('date_bancaire')
      ->order(['created'])
      ->all() );
    
    $this->_setRattachement($ecriture);
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
   * 
   * @return [ (int) year => [ (int) month => (int) $month ] ]
   */
  private function _setMonthsByYear() {
    $query = $this->Ecritures->find();
    $month = $query->func()->month([
      'date_bancaire' => 'identifier']);
    $year = $query->func()->year([
      'date_bancaire' => 'identifier']);
    
    $monthsByYear = $query
      ->select([
        'month' => $month,
        'year' => $year])
      ->distinct(['month', 'year'])
      ->whereNotNull('date_bancaire')
      ->order(['date_bancaire' => 'DESC'])
      ->all()
      ->combine('month', 'month', 'year')
      ->toArray();
      
    $this->set(compact('monthsByYear'));
    return $monthsByYear;
  }
  
  /**
   * Édite ou ajoute une écriture.
   * 
   * @param int $id L'identifiant de l'écriture.
   */
  public function edit($id = null) {
    $ecriture = ($id === null)
      ? $this->Ecritures->newEmptyEntity()
      : $this->Ecritures->get($id);
    
    if ($this->request->is(['put', 'post']) && $this->_checkReadOnly()) {
      $data = $this->request->getData();
      $this->Ecritures->patchEntity($ecriture, $data);
      
      if ($this->Ecritures->save($ecriture)) {
        $this->Flash->success("L'écriture a été sauvegardée");
        
        $dateBancaire = $ecriture->date_bancaire;
        if ($dateBancaire) {
          return $this->redirect([
            'action' => 'index',
            $dateBancaire->year,
            $dateBancaire->month ]);
        } else {
          return $this->redirect([
            'action' => 'index' ]);
        }
        
      } else {
        $this->Flash->error('Erreur pendant la sauvegarde');
      }
    }
    
    $this->set('ecriture', $ecriture);
    $this->_setMonthsByYear();
    $this->set('date', $ecriture->date ?? \Cake\I18n\Date::now());
    $this->set('postes', $this->Ecritures->Postes->find('list'));
    $this->set('activites', $this->Ecritures->Activites->find('list'));
    $this->_setRattachement($ecriture);
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
  private function _setRattachement($ecriture) {
    $refDates = [\Cake\I18n\Date::now()];
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
    
    $this->set('rattachement', $result);
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
