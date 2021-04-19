<?php
$this->Html->css(
  array('stats/pivot.min', 'stats/bilan', 'ecritures/index'),
  array('inline' => false));

$this->element('ecritures/click2edit');

?>
<nav id="years">
  <ul>
    <?php
    foreach ($years as $value) {
      $exercice = $value[0]['years'];
      ?>
      <li>
        <?php
        $exerciceCaption = ($exercice-1).'-'.$exercice;
        echo ($exercice == $year)
          ? "<span>$exerciceCaption</span>"
          : $this->Html->link($exerciceCaption, array($exercice));
        ?>
      </li>
      <?php
    }
    ?>
  </ul>
</nav>

<nav id="toggleAttached">
  <?php
  echo $this->fetch('content');
  ?>
</nav>

<div id="bilan"></div>
<div id="detail"></div>
<?php

$this->element('jquery');
$this->element('jquery-ui');

$scriptBottom = array('inline' => 'scriptBottom');
echo $this->Html->script('pivottable/pivot.min', $scriptBottom);
echo $this->Html->script('pivottable/pivot.fr.min', $scriptBottom);
echo $this->Html->script('plotly-basic-latest.min', $scriptBottom);
echo $this->Html->script('pivottable/plotly_renderers.min', $scriptBottom);

function quote_array($text) {
  $escapedText = addcslashes($text, "'");
  return "'$escapedText'";
}

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
                  .domain([-1000, 0, 1000])
                  .range(["#F77", "#FFF", "#7F7"])
            }
          },
          table: {
            clickCallback: displayEcritures
          }
        },
        sorters: {
          'Sens': $.pivotUtilities.sortAs([
            'Recettes',
            'Dépenses',
            '<?php echo StatsController::ATTACHED; ?>',
            '<?php echo StatsController::DETACHED; ?>']),
          'Activite': $.pivotUtilities.sortAs(
            [<?php echo implode(',', array_map('quote_array', $activites)); ?>]),
          'Poste': $.pivotUtilities.sortAs(
            [<?php echo implode(',', array_map('quote_array', $postes)); ?>])
        },
        showUI: false
      },
      false,
      'fr'
    );
    
    function displayEcritures(e, value, filters, pivotData) {
      $.post(
        '<?php echo $this->Url->build(array(
          'action' => 'bilan_detail',
          $year, $this->fetch('ajuste')));
        ?>',
        filters,
        function(data, textStatus, jqXHR) {
          $('#detail').html(data);
          <?php
          echo $this->element('ecritures/click2edit_script');
          ?>
        }
      );
    }
    
  });
  -->
</script>
<?php
$this->end();
