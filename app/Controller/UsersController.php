<?php
class UsersController extends AppController {
  
  public function index() {
    $this->set('users', $this->User->find('all'));
  }
}