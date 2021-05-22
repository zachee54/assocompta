<?php
$this->Html->css(
  array('users/delete', 'button'),
  array('block' => true));

?>
<section id="UserDelete">
  <p>Êtes-vous sûr de vouloir supprimer l'utiliseur <?php echo $username; ?> ?</p>
  <?php
  
  echo $this->Form->create(null, array('type' => 'delete'));
  
    echo $this->Form->submit('Oui', array(
      'class' => 'button',
      'after' => $this->Html->link('Non',
        array('action' => 'index'),
        array('class' => 'button'))));
  
  echo $this->Form->end();
  ?>
</section>