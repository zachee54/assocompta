<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Entity\User;
use Authentication\Identifier\IdentifierInterface;

class UsersController extends AppController {
  
  /**
   * Liste des utilisateurs.
   */
  public function index() {
    $this->set('users', $this->Users->find()->all());
  }
  
  /**
   * Modification d'un utilisateur par l'administrateur.
   */
  public function edit($id = null) {
    $user = ($id === null)
      ? $this->Users->newEmptyEntity()
      : $this->Users->findById($id)
          ->select(['id', 'nom', 'login', 'admin'])
          ->first();
    
    if ($this->request->is(['post', 'put'])) {
      $data = $this->request->getData();
      
      // Ne pas sauvegarder le mot de passe si non saisi
      if (empty($data['mdp'])) {
        unset($data['mdp']);
      }
      
      // Sauvegarde
      $this->Users->patchEntity($user, $data);
      if ($this->Users->save($user)) {
        $this->Flash->success('Les modifications ont été sauvegardées');
        $this->redirect(['action' => 'index']);
      } else {
        $this->Flash->error('Erreur pendant l\'enregistrement');
      }
    }
    $this->set('user', $user);
  }
  
  /**
   * Suppression d'un utilisateur.
   */
  public function delete($id) {
    $user = $this->Users->get($id);
    $name = $user->nom;
    
    if ($this->request->isDelete()) {
      $identity = $this->Authentication->getIdentity();
      if (($id != $identity->id) && $this->Users->delete($user)) {
        $this->Flash->success("L'utilisateur $name a été supprimé");
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Flash->error("Erreur pendant la suppression de l'utilisateur");
      }
    }
  }
}
