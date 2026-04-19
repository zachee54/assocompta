<?php
$title = $this->fetch('title');
$controller = $this->request->getParam('controller');

?><!DOCTYPE html>
<html>
<head>
  <?= $this->Html->charset() ?>
  <meta name="robots" content="noindex, nofollow">
  <title><?= $title ? 'CFE - '.$title : 'CFE' ?></title>
  <?= $this->Html->css('cfe') ?>
  <?= $this->fetch('css') ?>
  <?= $this->fetch('jquery') ?>
  <link rel="icon" href="<?= $this->Url->image('favicon.ico'); ?>"/>
</head>
<body>
  <header class="d-flex align-items-end justify-content-between bg-tertiary p-2">
    <h1 class="text-primary fw-bold fs-2 mb-0">
      Centre de Formation et d'Entraide
    </h1>
    <?php if ($this->Identity->isLoggedIn()): ?>
      <div class="d-flex flex-column text-blue-dark text-end">

        <h6 class="fw-bold fs-5 mb-0">
          <?= $this->Identity->get('nom') ?>
        </h6>

        <?php if (!$this->Identity->get('readonly')): ?>
          <?= $this->Html->link('Changer mon mot de passe',
            [ 'prefix' => false,
              'controller' => 'users',
              'action' => 'moncompte' ],
            ['class' => 'text-blue-dark text-decoration-none fw-bold fs-xsmall'] ) ?>
        <?php endif ?>

        <?= $this->Html->link('Déconnexion',
          [ 'prefix' => false,
            'controller' => 'users',
            'action' => 'logout' ],
          ['class' => 'text-blue-dark text-decoration-none fw-bold fs-xsmall'] ) ?>
      </div>
    <?php endif ?>
  </header>

  <ul class="nav nav-pills bg-secondary fs-small py-2 px-3 gap-3">
    <?= $this->element('nav-link', [
      'text' => 'Opérations',
      'url' => [
        'controller' => 'ecritures',
        'action' => 'index' ]]) ?>

    <?= $this->element('nav-link', [
      'text' => 'Bilan',
      'url' => [
        'controller' => 'stats',
        'action' => 'bilan' ]]) ?>

    <?php if ($this->Identity->get('admin')): ?>
      <?= $this->element('nav-link', [
        'text' => 'Administration',
        'url' => [
          'controller' => 'users',
          'action' => 'index' ]]) ?>
    <?php endif ?>
  </ul>

  <?= $this->Flash->render() ?>
  <?= $this->fetch('content') ?>
  <?= $this->fetch('scriptBottom') ?>
  
  <footer>
    <?= $this->Html->link('Mentions légales', [
      'controller' => 'pages',
      'action' => 'legal' ]) ?>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
