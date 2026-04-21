<div id="months" class="accordion accordion-flush fs-small bg-custom-light h-100">
  <?php for ( $yearDate = $maxDate;
              $yearDate->greaterThanOrEquals($minDate);
              $yearDate = $yearDate->subYears(1)->endOfYear() ):
    
    $show = ($yearDate->year == $maxDate->year);
    ?>

    <div class="accordion-item">

      <div class="accordion-header">
        <button class="accordion-button fs-small <?= $show ? null : 'collapsed' ?> p-2"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#months-<?= $yearDate->year ?>">

          <?= $yearDate->year ?>

        </button>
      </div>

      <div id="months-<?= $yearDate->year ?>"
        class="accordion-collapse collapse <?= ($show) ? 'show' : null ?>"
        data-bs-parent="#months">

        <div class="list-group-item list-group-flush text-primary bg-secondary">
          <?php for ( $monthDate = $yearDate->firstOfMonth();
                      ($monthDate->year == $yearDate->year)
                        && $monthDate->greaterThanOrEquals($minDate->firstOfMonth());
                      $monthDate = $monthDate->subMonths(1) ): ?>

            <?= $this->Html->link(
              $monthDate->i18nFormat('MMMM'),
              [ 'controller' => 'ecritures',
                'action' => 'index',
                $monthDate->year,
                $monthDate->month ],
              ['class' => 'list-group-item text-end p-1 px-3'] ) ?>
          <?php endfor ?>
        </div>

      </div>
    </div>
  <?php endfor ?>
</div>
