<?php
/**
 * Un item de la barre de navigation principale.
 * 
 * @var $text Le texte du lien
 * @var $url  Un tableau de routage.
 */

$url = $url + ['prefix' => false];
$currentController = strtolower($this->request->getParam('controller'));

?>
<li class="nav-item">
  <?= $this->Html->link($text, $url, [
    'class' => 'nav-link text-decoration-none'
        .(($currentController == $url['controller']) ? ' active' : null) ]) ?>
</li>
