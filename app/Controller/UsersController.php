<?php
class UsersController extends AppController {
  
  public function index() {
    $this->set('users', $this->User->find('all'));
  }
  
  public function edit($id = null) {
    if ($this->request->is(array('post', 'put'))) {
      $data = $this->request->data['User'];
      
      if (empty($data['mdp'])) {
        unset($data['mdp']);
      } else {
        $data['mdp'] = $this->_hashPassword($data['mdp']);
      }
      
      $this->User->id = $id;
      if ($this->User->save($data)) {
        $this->Flash->success('Les modifications ont été sauvegardées');
      } else {
        $this->Flash->error("Erreur pendant l'enregistrement");
      }
      $this->redirect(array('action' => 'index'));
    }
    
    $this->request->data = $this->User->findById($id, array(
      'nom', 'login', 'admin'));
  }
  
  private function _hashPassword($password) {
    $authenticates = $this->Auth->constructAuthenticate();
    $hasher = $authenticates[0]->passwordHasher();
    return $hasher->hash($password);
  }
}