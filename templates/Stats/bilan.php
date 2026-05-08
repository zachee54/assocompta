<?php
use App\Controller\StatsController;

$this->Html->css('stats/pivot.min', ['block' => true]);

$this->loadHelper('Year');

$this->assign('title', 'Bilan '.$this->Year->getExerciceName($year));

?>
<ul class="nav nav-pills fs-small m-3">
  <?php foreach ($years as $entity): ?>
    <li class="nav-item">
      <?= $this->Html->link(
        $this->Year->getExerciceName($entity->year),
        [$entity->year, $ajuste],
        ['class' => 'nav-link'.(($entity->year == $year) ? ' active' : null)] ) ?>
    </li>
  <?php endforeach ?>
</ul>

<ul class="nav nav-underline fs-small m-3">
  <li class="nav-item">
    <?= $this->Html->link(
      'Opérations de l\'exercice',
      [$year],
      ['class' => 'nav-link text-blue-dark'.($ajuste ? null : ' active')] ) ?>
  </li>
  <li class="nav-item">
    <?= $this->Html->link(
      'Opérations rattachées à l\'exercice',
      [$year, 1],
      ['class' => 'nav-link text-blue-dark'.($ajuste ? ' active' : null)] ) ?>
  </li>
</ul>

<div id="bilan"
  data-url="<?= $this->Url->build(['action' => 'data', $year, $ajuste]) ?>"
  data-attached-field="<?= StatsController::ATTACHED ?>"
  data-detached-field="<?= StatsController::DETACHED ?>"
  data-csrf-token="<?= $this->request->getAttribute('csrfToken') ?>"
  data-detail-url="<?= $this->Url->build([
    'action' => 'bilan_detail',
    $year, $ajuste ]) ?>">
</div>
<div id="detail"></div>
<?php

$scriptBottom = ['block' => 'scriptBottom'];
$this->Html->script('jquery-ui-1.12.1/jquery-ui.min', $scriptBottom);
$this->Html->script('pivottable/pivot.min', $scriptBottom);
$this->Html->script('pivottable/pivot.fr.min', $scriptBottom);
$this->Html->script('plotly-basic-latest.min', $scriptBottom);
$this->Html->script('pivottable/plotly_renderers.min', $scriptBottom);

$this->Html->script('stats/bilan', $scriptBottom);
