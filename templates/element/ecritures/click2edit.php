<?php
$this->element('jquery');

$this->append('scriptBottom');
?>
<script type="text/javascript">
  <?php
  echo $this->element('ecritures/click2edit_script');
  ?>
</script>
<?php
$this->end();
