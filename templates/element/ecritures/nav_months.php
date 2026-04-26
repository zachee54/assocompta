<div id="months" class="accordion accordion-flush fs-small bg-custom-light h-100">
  <?php foreach ($monthsByYear as $year => $months):
    $show = ($year == $date->year); ?>

    <div class="accordion-item">

      <div class="accordion-header">
        <button class="accordion-button fs-small <?= $show ? null : 'collapsed' ?> p-2"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#months-<?= $year ?>">

          <?= $year ?>

        </button>
      </div>

      <div id="months-<?= $year ?>"
        class="accordion-collapse collapse <?= ($show) ? 'show' : null ?>"
        data-bs-parent="#months">

        <div class="list-group-item list-group-flush text-primary bg-secondary">
          <?php foreach ($months as $month): ?>

            <?= $this->Html->link(
              (new \Cake\I18n\Date("$year-$month-1"))->i18nFormat('MMMM'),
              [ 'controller' => 'ecritures',
                'action' => 'index',
                $year,
                $month ],
              ['class' => 'list-group-item text-end p-1 px-3'] ) ?>
          <?php endforeach ?>
        </div>

      </div>
    </div>
  <?php endforeach ?>
</div>
