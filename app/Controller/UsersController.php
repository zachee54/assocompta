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
  
  public function admin_index() {
    $this->set('users', $this->User->find('all'));
  }
  
  public function admin_edit($id = null) {
    if ($this->request->is(array('post', 'put'))) {
      $data = $this->request->data['User'];
      
      if (empty($data['mdp'])) {
        unset($data['mdp']);
      } else {
        $data['mdp'] = $this->_hashPassword($data['mdp']);
      }
      
      if (isset($data['admin']) && !$data['admin']) {
        if (!$this->_hasOtherAdmin($id)) {
          $this->Flash->error("L'application doit avoir au moins un administrateur");
          return;
        }
      }
      
      $this->User->id = $id;
      if ($this->User->save($data)) {
        $this->Flash->success('Les modifications ont été sauvegardées');
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Flash->error("Erreur pendant l'enregistrement");
      }
    }
    
    $this->request->data = $this->User->findById($id, array(
      'nom', 'login', 'admin'));
  }
  
  public function moncompte() {
    if (!$this->request->isPost()) {
      return;
    }
    if ($this->Auth->user('readonly')) {
      $this->Flash->error(
        'Votre profil ne vous permet pas de modifier le mot de passe');
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
  
  public function admin_delete($id) {
    $user = $this->User->findById($id);
    $name = $user['User']['nom'];
    $this->set('username', $name);
    
    if (!$this->_hasOtherAdmin($id)) {
      $this->Flash->error('Vous ne pouvez pas supprimer le dernier administrateur');
      $this->redirect(array('action' => 'index'));
    }
    
    if ($this->request->isDelete()) {
      if (($id != $this->Auth->user('id')) && $this->User->delete($id)) {
        $this->Flash->success("L'utilisateur $name a été supprimé");
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Flash->error("Erreur pendant la suppression de l'utilisateur");
      }
    }
  }
  
  private function _hasOtherAdmin($id) {
    return $this->User->find('count', array(
      'conditions' => array(
        'admin' => true,
        'id !=' => $id)));
  }
}
