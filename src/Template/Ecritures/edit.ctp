<?php
$this->Html->css('ecritures/common', array('inline' => false));
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
