<?php
$this->Html->css(
  array('stats/pivot.min', 'stats/bilan'),
  array('inline' => false));

?>
<div id="bilan">
</div>
<?php

echo $this->element('jquery');

$scriptBottom = array('inline' => 'scriptBottom');
echo $this->Html->script('pivottable/pivot.min', $scriptBottom);
echo $this->Html->script('pivottable/pivot.fr.min', $scriptBottom);

$this->append('scriptBottom');
?>
<script type="text/javascript">
  <!--
  $(function() {
    
    $('#bilan').pivot(
      <?php echo json_encode($ecritures); ?>,
      {
        rows: ['Sens', 'Poste'],
        cols: ['Activité'],
        aggregator: $.pivotUtilities.locales.fr.aggregators['Somme'](['Montant'])
      }
    );
    
  });
  -->
</script>
<?php
$this->end();
