<?php
$title = $this->fetch('title');

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="vstack min-vh-100">
  <header class="d-flex align-items-end justify-content-between bg-custom-light p-2">
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

  <?= $this->Flash->render() ?>

  <div class="flex-grow-1">
    <?= $this->fetch('content') ?>
  </div>

  <?= $this->fetch('scriptBottom') ?>
  
  <footer class="fs-xsmall">
    <nav class="nav">
      <?= $this->Html->link('Mentions légales',
        [ 'controller' => 'pages',
          'action' => 'legal' ],
        ['class' => 'nav-link'] ) ?>
    </nav>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
