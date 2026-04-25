<div class="d-flex">

  <div style="min-width:7rem">
    <?= $this->element('ecritures/nav_months') ?>
  </div>

  <div class="p-4">
    <?php if (!$this->Identity->get('readonly')): ?>
      <div class="accordion mb-5" id="ecriture-edition">
        <div class="accordion-item">
          <div class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ecriture-edition__item">
              <i class="bi bi-plus-circle-fill me-2"></i>
              Ajouter une écriture
            </button>
          </div>
          <div id="ecriture-edition__item" class="accordion-collapse collapse" data-bs-parent="#ecriture-edition">
            <div class="accordion-body bg-custom-light">
              <?= $this->element('ecritures/edit_form') ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif ?>

    <?= $this->element('ecritures/browse_months') ?>

    <h2 class="h2">
      <div>Relevé bancaire <?php
        // Cf. http://userguide.icu-project.org/formatparse/datetime
        $monthName = $this->Time->format($date, 'MMMM yyyy');
        
        echo in_array(substr($monthName, 0, 1), ['a', 'o']) ? "d'" : "de ";
        echo $monthName;
      ?></div>
    </h2>
    
    <?= $this->element('ecritures/table', [
      'displaySoldes' => true ]) ?>

    <?= $this->element('ecritures/browse_months') ?>

    <h2 class="h2">
      Opérations en attente
    </h2>
   
    <div>
      <?php if ($enAttente->isEmpty()): ?>

        <div class="fst-italic">
          Aucune opération en attente
        </div>

      <?php else: ?>

        <?= $this->element('ecritures/table', [
          'ecritures' => $enAttente,
          'no_bancaire' => true ]) ?>

      <?php endif ?>
    </div>
  </div>
</div>
