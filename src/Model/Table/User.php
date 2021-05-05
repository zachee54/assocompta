<?php
class User extends AppModel {
  public $validate = array(
    'nom' => array(
      'rule' => 'notBlank',
      'message' => 'Vous devez saisir un nom'),
    'login' => array(
      'notBlank' => array(
        'rule' => 'notBlank',
        'message' => 'Veuillez saisir un identifiant'),
      'alphaNumeric' => array(
        'rule' => 'alphaNumeric',
        'message' => "L'identifiant ne doit contenir que des chiffres et des lettres"),
    ),
    'mdp' => array(
      'rule' => 'notBlank',
      'required' => 'create',
      'message' => 'Vous devez spécifier un mot de passe'));
}