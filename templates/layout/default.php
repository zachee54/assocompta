<?php
$this->extend('root');

?>
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

<main>
  <?= $this->fetch('content') ?>
</main>
