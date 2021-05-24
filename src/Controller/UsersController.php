<?php
namespace App\Controller;

use Authentication\Identifier\IdentifierInterface;

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
   * Page de déconnexion.
   */
  public function logout() {
    $this->Authentication->logout();
    return $this->redirect(['action' => 'login']);
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
    $data = $this->request->getData();
    
    if ($data['new_password'] != $data['password_confirm']) {
      $this->Flash->error('Les nouveaux mots de passe ne correspondent pas');
      return;
    }
    
    if ($this->_recheckActualPassword($data['old_password'])) {
      $user = $this->Users->newEntity([
        'id' => $identity->id,
        'mdp' => $data['new_password'] ]);
      $user->isNew(false);
      
      if ($this->Users->save($user)) {
        $this->Flash->success('Votre mot de passe a été modifié');
        $this->redirect('/');
      } else {
        $this->Flash->error('Erreur pendant la mise à jour du mot de passe');
      }
    }
  }
  
  /**
   * Vérifie le mot de passe actuel.
   */
  private function _recheckActualPassword($password) {
    $login = $this->Authentication->getIdentity()->login;
    
    $authService = $this->Authentication->getAuthenticationService();
    $identifiers = $authService->identifiers();
    
    if (!$identifiers->identify([
        IdentifierInterface::CREDENTIAL_USERNAME => $login,
        IdentifierInterface::CREDENTIAL_PASSWORD => $password])) {
      
      $this->Flash->error('Votre mot de passe est erroné');
      return false;
    }
    
    return true;
  }
}
