<?php
use App\Controller\StatsController;

$this->Html->css(
  array('stats/pivot.min', 'stats/bilan', 'ecritures/index'),
  array('block' => true));

$this->element('ecritures/click2edit');

?>
<nav id="years">
  <ul>
    <?php
    foreach ($years as $value) {
      $exercice = $value->year;
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

$scriptBottom = array('block' => 'scriptBottom');
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
      <?= json_encode($ecritures) ?>,
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
            '<?= StatsController::ATTACHED ?>',
            '<?= StatsController::DETACHED ?>']),
          'Activite': $.pivotUtilities.sortAs(
            [<?= implode(',', array_map('quote_array', $activites)) ?>]),
          'Poste': $.pivotUtilities.sortAs(
            [<?= implode(',', array_map('quote_array', $postes)) ?>])
        },
        showUI: false
      },
      false,
      'fr'
    );
    
    function displayEcritures(e, value, filters, pivotData) {
      filters['_csrfToken'] = '<?= $this->request->getAttribute('csrfToken') ?>';
      
      $.post(
        '<?= $this->Url->build([
          'action' => 'bilan_detail',
          $year, $this->fetch('ajuste')]);
        ?>',
        filters,
        function(data, textStatus, jqXHR) {
          $('#detail').html(data);
          <?= $this->element('ecritures/click2edit_script') ?>
        }
      );
    }
    
  });
  -->
</script>
<?php
$this->end();
