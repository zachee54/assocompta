<?php
class UsersController extends AppController {
  
  public function login() {
    if ($this->request->isPost()) {
      if ($this->Auth->login()) {
        $this->redirect($this->Auth->redirectUrl());
      } else {
        $this->Flash->error('Identifiant ou mot de passe invalide');
      }
    }
  }
  
  public function logout() {
    $this->redirect($this->Auth->logout());
  }
  
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
  
  public function moncompte() {
    if (!$this->request->isPost()) {
      return;
    }
    $data = $this->request->data;
    
    if ($data['new_password'] != $data['password_confirm']) {
      $this->Flash->error('Les nouveaux mots de passe ne correspondent pas');
      return;
    }
    
    $oldHash = $this->_hashPassword($data['old_password']);
    $userId = $this->Auth->user('id');
    $password = $this->User->field('mdp', array('id' => $userId));
    if ($oldHash != $password) {
      $this->Flash->error('Votre mot de passe est erroné');
      return;
    }
    
    $this->User->id = $userId;
    $hash = $this->_hashPassword($data['new_password']);
    if ($this->User->saveField('mdp', $hash)) {
      $this->Flash->success('Votre mot de passe a été modifié');
      $this->redirect('/');
    } else {
      $this->Flash->error('Erreur pendant la mise à jour du mot de passe');
    }
  }
  
  private function _hashPassword($password) {
    $authenticates = $this->Auth->constructAuthenticate();
    $hasher = $authenticates[0]->passwordHasher();
    return $hasher->hash($password);
  }
}