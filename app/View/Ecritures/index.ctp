<?php
$this->Html->css('ecritures', array('inline' => false));
$this->element('currency');

?>
<div class="ecritures">
  <?php
  foreach ($ecritures as $ecriture) {
    ?>
    <a href="<?php
      echo $this->Html->url(array('action' => 'edit', $ecriture['Ecriture']['id']));
      ?>">
      <div><?php echo $ecriture['Ecriture']['engagement']; ?></div>
      <div><?php echo $ecriture['Ecriture']['bancaire']; ?></div>
      <div><?php echo $ecriture['Poste']['name']; ?></div>
      <div><?php echo $ecriture['Activite']['name']; ?></div>
      <div><?php echo $ecriture['Ecriture']['description']; ?></div>
      <div><?php echo $ecriture['Ecriture']['personne']; ?></div>
      <div><?php echo $ecriture['Ecriture']['piece']; ?></div>
      <div>
        <?php
        if ($ecriture['Ecriture']['debit']) {
          echo $this->Number->currency($ecriture['Ecriture']['debit']);
        }
        ?>
      </div>
      <div>
        <?php
        if ($ecriture['Ecriture']['credit']) {
          echo $this->Number->currency($ecriture['Ecriture']['credit']);
        }
        ?>
      </div>
    </a>
    <?php
  }
  ?>
</div>
