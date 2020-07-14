<?php
$this->Html->css(
  array('stats/pivot.min', 'stats/bilan'),
  array('inline' => false));

?>
<div id="bilan">
</div>
<?php

echo $this->element('jquery');
$this->element('jquery-ui');

$scriptBottom = array('inline' => 'scriptBottom');
echo $this->Html->script('pivottable/pivot.min', $scriptBottom);
echo $this->Html->script('pivottable/pivot.fr.min', $scriptBottom);
echo $this->Html->script('plotly-basic-latest.min', $scriptBottom);
echo $this->Html->script('pivottable/plotly_renderers.min', $scriptBottom);

$this->append('scriptBottom');
?>
<script type="text/javascript">
  <!--
  $(function() {
    
    $('#bilan').pivotUI(
      <?php echo json_encode($ecritures); ?>,
      {
        rows: ['Sens', 'Poste'],
        cols: ['Activité'],
        vals: ['Montant'],
        aggregators: $.pivotUtilities.locales.fr.aggregators,
        aggregatorName: 'Somme',
        renderers: $.extend(
          $.pivotUtilities.renderers,
          $.pivotUtilities.plotly_renderers),
        rendererName: 'Heatmap',
        rendererOptions: {
          heatmap: {
            colorScaleGenerator: function(values) {
              // Plotly happens to come with d3 on board
              return Plotly.d3.scale.linear()
                  .domain([-10000, 0, 10000])
                  .range(["#F77", "#FFF", "#7F7"])
            }
          }
        },
        showUI: false
      }
    );
    
  });
  -->
</script>
<?php
$this->end();
