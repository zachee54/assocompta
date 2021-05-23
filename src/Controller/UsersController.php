<?php
namespace App\Controller;

class UsersController extends AppController {
  
  public function beforeFilter(\Cake\Event\EventInterface $event) {
    parent::beforeFilter($event);
    
    $this->Authentication->allowUnauthenticated(['login']);
  }
  
  /**
   * Page de connexion.
   */
  public function login() {
    $result = $this->Authentication->getResult();
    if ($result->isValid()) {
      $target = $this->Authentication->getLoginRedirect() ?? '/';
      return $this->redirect($target);
    }
    if ($this->request->is('post') && !$result->isValid()) {
      $this->Flash->error('Identifiant ou mot de passe invalide');
    }
  }
  
  /**
   * Copie les identifiants de login en GET comme s'ils avaient été passés en
   * POST dans la requête, s'ils existent.
   * 
   * @return  true si l'identifiant avait été passé en GET.
   */
  private function _allowLoginInGet() {
    if (isset($this->request->query['data']['User']['login'])) {
      $this->request->data = $this->request->query['data'];
      return true;
    }
    return false;
  }
  
  /**
   * Page de déconnexion.
   */
  public function logout() {
    $this->Authentication->logout();
    return $this->redirect(['action' => 'login']);
  }
  
  /**
   * Liste des utilisateurs.
   */
  public function admin_index() {
    $this->set('users', $this->User->find('all'));
  }
  
  /**
   * Modification d'un utilisateur par l'administrateur.
   */
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
  
  /**
   * Modification du mot de passe de l'utilisateur courant.
   */
  public function moncompte() {
    if (!$this->request->isPost()) {
      return;
    }
    
    $identity = $this->Authentication->getIdentity();
    
    if ($identity->readonly) {
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
    $userId = $identity->id;
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
  
  /**
   * Hachage du mot de passe pour modification.
   */
  private function _hashPassword($password) {
    $authenticates = $this->Auth->constructAuthenticate();
    $hasher = $authenticates[0]->passwordHasher();
    return $hasher->hash($password);
  }
  
  /**
   * Suppression d'un utilisateur.
   */
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
  
  /**
   * Vérification de l'existence d'un administrateur, autre que l'utilisateur
   * spécifié.
   * 
   * @param $id:  Identifiant d'un utilisateur.
   * @return      true s'il existe un administrateur portant un autre
   *              identifiant que $id.
   */
  private function _hasOtherAdmin($id) {
    return $this->User->find('count', array(
      'conditions' => array(
        'admin' => true,
        'id !=' => $id)));
  }
}
