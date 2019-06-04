<?php
class EcrituresController extends AppController {
  
  public function index($year = null, $month = null) {
    
    // Année et mois par défaut : les derniers saisis
    if (!$year) {
      $max_date = $this->Ecriture->find('first', array(
        'fields' => 'MAX(date_bancaire) as max_date',
        'recursive' => -1));
      $max_date = date_create($max_date[0]['max_date']);
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
    
    $this->set('ecritures', $this->Ecriture->find('all', array(
      'conditions' => array(
        'date_bancaire >=' => $debut,
        'date_bancaire <=' => $fin),
      'order' => array('date_bancaire', 'created'))));
  }
  
  /**
   * Met à disposition de la vue les soldes bancaires de début et de fin du
   * mois.
   * 
   * @param string $debut Le premier jour du mois, au format AAAAMMJJ.
   * @param string $fin   Le dernier jour du mois, au format AAAAMMJJ.
   */
  private function _setSoldesDebutFin($debut, $fin) {
    $a_nouveau = $this->Ecriture->find('first', array(
      'recursive' => -1,
      'fields' => 'SUM(credit-debit) as solde',
      'conditions' => array(
        'date_bancaire <' => $debut)));
    $this->set('a_nouveau', $a_nouveau[0]['solde']);
    
    $solde = $this->Ecriture->find('first', array(
      'recursive' => -1,
      'fields' => 'SUM(credit-debit) as solde',
      'conditions' => array(
        'date_bancaire <=' => $fin)));
    $this->set('solde', $solde[0]['solde']);
  }
  
  /**
   * Édite une écriture.
   * 
   * @param int $id L'identifiant de l'écriture.
   */
  public function edit($id = null) {
    if ($this->request->is(array('put', 'post'))) {
      
    } else if ($id !== null) {
      $this->request->data = $this->Ecriture->findById($id);
    }
    
    $this->set('postes', $this->Ecriture->Poste->find('list'));
    $this->set('activites', $this->Ecriture->Activite->find('list'));
  }
}