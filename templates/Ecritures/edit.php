<?php
$this->Html->css('ecritures/common', array('block' => true));
?>
<div id="content">
  <?php
  echo $this->element('ecritures/nav_months');
  ?>
  <section>
    <h1>
      <div>Modification d'une écriture</div>
    </h1>
    <?php
    
    echo $this->element('ecritures/edit_form',
      array('showCancel' => true));
    ?>
  </section>
</div>
