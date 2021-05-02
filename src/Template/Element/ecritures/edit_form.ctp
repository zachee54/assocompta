<?php
$this->Html->css(
  array('ecritures/edit', 'button'),
  array('block' => true));

$readonly = $Auth->user('readonly');

echo $this->Form->create(null, array('class' => 'EcritureEditForm'));
?>
  <article>
    <div>
      <?php
      echo $this->Form->input('date_engagement', array(
        'type' => 'DATE',
        'default' => date_format(
            date_modify(date_create(), 'first day of -1 month'),
            'Y-m-d'),
        'label' => 'Date'));
      
      echo $this->Form->input('date_bancaire', array(
        'type' => 'DATE',
        'label' => 'Date banque'));
      
      if ($Auth->user('admin')) {
        echo $this->Form->input('rattachement', array(
          'label' => 'Rattachement'));
      }
      
      echo $this->Form->input('poste_id', array(
        'default' => 8,
        'label' => 'Poste'));
      
      echo $this->Form->input('activite_id', array(
        'default' => 2,
        'label' => 'Activité'));
      ?>
    </div>
    <div>
      <?php
      echo $this->Form->input('description', array(
        'label' => 'Description'));
      
      echo $this->Form->input('personne', array(
        'label' => 'Personne'));
      
      echo $this->Form->input('piece', array(
        'class' => 'piece',
        'label' => 'N°pièce'));
      
      echo $this->Form->input('debit', array(
        'required' => false,
        'label' => 'Débit'));
      
      echo $this->Form->input('credit', array(
        'required' => false,
        'label' => 'Crédit'));
      
      ?>
    </div>
    <div class="submit">
      <?php
      if (!$readonly) {
        echo $this->Form->submit('Valider', array(
          'class' => 'button',
          'div' => false));
      }
      
      if (!empty($showCancel)) {
        echo $this->Html->link('Annuler',
          $this->request->referer(),
          array('class' => 'button cancelButton'));
        
        if (!$readonly) {
          echo $this->Form->submit('Supprimer', array(
            'id' => 'deleteButton',
            'formaction' => $this->Url->build(array(
              'action' => 'delete',
              $this->data['Ecriture']['id'])),
            'formmethod' => 'post',
            'class' => 'button',
            'div' => false));
          
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
