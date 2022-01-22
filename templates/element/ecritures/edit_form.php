<?php
$this->Html->css(
  array('ecritures/edit', 'button'),
  array('block' => true));

$readonly = $this->Identity->get('readonly');

echo $this->Form->create($ecriture, array('class' => 'EcritureEditForm'));
?>
  <article>
    <div>
      <?php
      echo $this->Form->control('date_engagement', array(
        'type' => 'DATE',
        'default' => date_format(
            date_modify(date_create(), 'first day of -1 month'),
            'Y-m-d'),
        'label' => 'Date'));
      
      echo $this->Form->control('date_bancaire', array(
        'type' => 'DATE',
        'label' => 'Date banque'));
      
      echo $this->Form->control('rattachement', array(
        'label' => 'Rattachement (facultatif)',
        'type' => 'select',
        'options' => $rattachement,
        'empty' => true));
      
      echo $this->Form->control('poste_id', array(
        'default' => 8,
        'label' => 'Poste'));
      
      echo $this->Form->control('activite_id', array(
        'default' => 2,
        'label' => 'Activité'));
      ?>
    </div>
    <div>
      <?php
      echo $this->Form->control('description', array(
        'label' => 'Description'));
      
      echo $this->Form->control('personne', array(
        'label' => 'Personne'));
      
      echo $this->Form->control('piece', array(
        'class' => 'piece',
        'label' => 'N° pièce'));
      
      echo $this->Form->control('debit', array(
        'default' => '',
        'required' => false,
        'label' => 'Débit'));
      
      echo $this->Form->control('credit', array(
        'default' => '',
        'label' => 'Crédit'));
      
      ?>
    </div>
    <div class="submit">
      <?php
      if (!$readonly) {
        echo $this->Form->button('Valider', [
          'class' => 'button']);
      }
      
      if (!empty($showCancel)) {
        echo $this->Html->link('Annuler',
          $this->request->referer() ?? '/',
          array('class' => 'button cancelButton'));
        
        if (!$readonly) {
          echo $this->Form->button('Supprimer', [
            'id' => 'deleteButton',
            'formaction' => $this->Url->build(array(
              'action' => 'delete',
              $ecriture->id)),
            'formmethod' => 'post',
            'class' => 'button']);
          
          echo $this->element('jquery');

          $this->append('scriptBottom');
          ?>
          <script type="text/javascript">
            $(function() {
              $('#deleteButton').click(function(evt) {
                if (!confirm("Confirmez la suppression de l'écriture")) {
            evt.preventDefault();
                }
              });
            });
          </script>
          <?php
          $this->end();
        }
      }
      ?>
    </div>
  </article>
  <?php
  
echo $this->Form->end();

// La saisie d'une date d'écriture se reporte automatiquement en date banque
$this->append('scriptBottom');
  ?>
  <script type="text/javascript">
    $(function() {
      var dateEngagement = $('#date-engagement');
      var dateBancaire = $('#date-bancaire');
      
      // Réinitialiser l'animation après qu'elle aura eu lieu
      dateBancaire.get(0).addEventListener('animationend', function() {
        $(this).removeClass('autoUpdated');
      });
      
      // Recopie de la valeur + animation
      dateEngagement.change(function(event) {
        dateBancaire.val(this.value);
        dateBancaire.addClass('autoUpdated');
      });
    });
  </script>
  <?php
$this->end();
