<?php
namespace App\Controller;

class EcrituresController extends AppController {
  
  public function index($year = null, $month = null) {
    
    // Inclure le traitement du formulaire d'ajout d'une écriture
    $this->edit();
    // Ne pas réutiliser les données de l'écriture ajoutée, s'il y en a une
    // TODO remplacer par la réinitialisation d'une entité
    // $this->request->data = null;
    
    // Année et mois par défaut : les plus récents saisis
    if (!$year) {
      $query = $this->Ecritures->find()
        ->select(['max_date' => 'MAX(date_bancaire)']);
      $max_date = date_create($query->first()->max_date);
      $month = date_format($max_date, 'm');
      $year = date_format($max_date, 'Y');
    }
    
    $debut = sprintf('%04u%02u01', $year, $month);
    $fin = sprintf('%04u%02u%02u', $year, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year));
    
    $this->set('year', $year);
    $this->set('month', $month);
    $this->set('debut', date_create($debut));
    $this->set('fin', date_create($fin));
    
    $this->_setSoldesDebutFin($debut, $fin);
    
//     $this->_setMonths(); // Déjà dans la méthode edit
    
    $this->set('ecritures',
      $this->Ecritures->find()
      ->where([
        'date_bancaire >=' => $debut,
        'date_bancaire <=' => $fin])
      ->order(['date_bancaire', 'created']));
    
    $this->set('enAttente',
      $this->Ecritures->find()
      ->whereNull('date_bancaire')
      ->order(['created']));
  }
  
  /**
   * Met à disposition de la vue les soldes bancaires de début et de fin du
   * mois.
   * 
   * @param string $debut Le premier jour du mois, au format AAAAMMJJ.
   * @param string $fin   Le dernier jour du mois, au format AAAAMMJJ.
   */
  private function _setSoldesDebutFin($debut, $fin) {
    $query = $this->Ecritures->find();
    $query->select(
      ['solde' => $query->func()->sum('credit-debit')]);
    
    $this->set('a_nouveau', $query
      ->where(['date_bancaire <' => $debut])
      ->first()->solde);
    
    $this->set('solde', $query
      ->where(['date_bancaire <=' => $fin], [], true)
      ->first()->solde);
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
    if ($this->request->is(array('put', 'post')) && $this->_checkReadOnly()) {
      $this->Ecriture->id = $id;  // id peut être null
      $saved = $this->Ecriture->save($this->request->data);
      if ($saved) {
        $this->Flash->success("L'écriture a été sauvegardée");
        $yearMonth = $this->_getMonth();
        $this->redirect(array(
          'action' => 'index',
          $yearMonth['year'],
          $yearMonth['month']));
      } else {
        $this->Flash->error('Erreur pendant la sauvegarde');
        
        // Réinsérer l'id dans les données. Il est utilisé pour le bouton Supprimer
        if ($id !== null) {
          $this->request->data['Ecriture']['id'] = $id;
        }
      }
      
    } else if ($id !== null) {
      $this->request->data = $this->Ecriture->findById($id);
      
      $this->_nullIfZero('debit');
      $this->_nullIfZero('credit');
    }
    
    $this->_setMonths();
    $this->set('postes', $this->Ecritures->Postes->find('list'));
    $this->set('activites', $this->Ecritures->Activites->find('list'));
  }
  
  /**
   * Renvoie le mois de la date bancaire de l'écriture (écriture dans
   * $this->request->data).
   * 
   * @return array  Un tableau contenant les clés 'year' et 'month' avec valeurs
   *                numériques, ou avec des chaînes vides si la date bancaire
   *                n'est pas fournie.
   */
  private function _getMonth() {
    if (empty($this->request->data['Ecriture']['date_bancaire'])) {
      return array(
        'year' => '',
        'month' => '');
    }
    
    $date = date_create($this->request->data['Ecriture']['date_bancaire']);
    return array(
      'year' => date_format($date, 'Y'),    // Année sur 4 chiffres
      'month' => date_format($date, 'n'));  // Mois sur 1 ou 2 chiffres
  }
  
  /**
   * Supprime un champ de la requête s'il est égal à zéro (y compris la chaîne
   * '0.00').
   * 
   * @param string $ecritureField Le nom d'un champ du modèle Ecriture.
   */
  private function _nullIfZero($ecritureField) {
    if ($this->request->data['Ecriture'][$ecritureField] == 0) {
      unset($this->request->data['Ecriture'][$ecritureField]);
    }
  }
  
  /**
   * Efface une écriture.
   * 
   * @param int id  L'identifiant de l'écriture à supprimer.
   */
  public function delete($id) {
    if ($id && $this->request->is('put', 'post') && $this->_checkReadOnly()) {
      if ($this->Ecriture->delete($id)) {
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
    if ($this->Auth->user('readonly')) {
      $this->Flash->error(
        'Votre profil ne vous permet de modifier les données');
      return false;
    }
    return true;
  }
}
