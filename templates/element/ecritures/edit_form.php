<?php
$this->Html->css(
  array('ecritures/edit', 'button'),
  array('block' => true));

$readonly = $this->Identity->get('readonly');

echo $this->Form->create(null, array('class' => 'EcritureEditForm'));
?>
  <article>
    <div>
      <?php
      echo $this->Form->control('date_engagement', array(
        'type' => 'DATE',
        'default' => date_format(
            date_modify(date_create(), 'first day of -1 month'),
            'Y-m-d'),
        'label' => 'Date&nbsp;:'));
      
      echo $this->Form->control('date_bancaire', array(
        'type' => 'DATE',
        'label' => 'Date banque&nbsp;:'));
      
      if ($this->Identity->get('admin')) {
        echo $this->Form->control('rattachement', array(
          'label' => 'Rattachement&nbsp;:'));
      }
      
      echo $this->Form->control('poste_id', array(
        'default' => 8,
        'label' => 'Poste&nbsp;:'));
      
      echo $this->Form->control('activite_id', array(
        'default' => 2,
        'label' => 'Activité&nbsp;:'));
      ?>
    </div>
    <div>
      <?php
      echo $this->Form->control('description', array(
        'label' => 'Description&nbsp;:'));
      
      echo $this->Form->control('personne', array(
        'label' => 'Personne&nbsp;:'));
      
      echo $this->Form->control('piece', array(
        'class' => 'piece',
        'label' => 'N°&nbsp;pièce&nbsp;:'));
      
      echo $this->Form->control('debit', array(
        'required' => false,
        'label' => 'Débit&nbsp;:'));
      
      echo $this->Form->control('credit', array(
        'required' => false,
        'label' => 'Crédit&nbsp;:'));
      
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