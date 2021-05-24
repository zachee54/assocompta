<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Authentication\Identifier\IdentifierInterface;

class UsersController extends AppController {
  
  /**
   * Liste des utilisateurs.
   */
  public function index() {
    $this->set('users', $this->Users->find('all'));
  }
  
  /**
   * Modification d'un utilisateur par l'administrateur.
   */
  public function edit($id = null) {
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
   * Suppression d'un utilisateur.
   */
  public function delete($id) {
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
