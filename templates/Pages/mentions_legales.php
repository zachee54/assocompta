<?php
$this->layout = 'root';
$this->assign('title', 'Mentions légales');

?>
<h1 class="h3 text-center text-primary my-3">Mentions légales</h1>

<h2 class="h5 text-primary mb-0">Directeur de la publication</h1>
<p>Olivier Haas</p>

<h2 class="h5 text-primary mb-0">Nous contacter :</h1>
<p>
  Association CENTRE DE FORMATION ET D'ENTRAIDE<br/>
  <style type="text/css">
    .tel::after {content:' 7 33+'}
    .tel::before {content:'38 91 '}
    .tel{unicode-bidi:bidi-override;direction:rtl}
  </style>
  <span class="tel">33 86</span>
</p>

<h2 class="fs-5 text-primary mb-0">Hébergeur&nbsp;:</h1>
<p>
  SARL ALWAYSDATA<br/>
  91 rue du Faubourg Saint-Honoré<br/>
  75008 PARIS<br/>
  Tél.&nbsp;: +33&nbsp;1&nbsp;84&nbsp;16&nbsp;23&nbsp;49
</p>

<div class="text-center">
  <?= $this->Html->link('Retour', '/', [
    'class' => 'btn btn-secondary' ]) ?>
</div>
