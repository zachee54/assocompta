$(function() {

  var bilan = $('#bilan');
  $.get({
    url: bilan.data('url'),
    success: function (jsonData, textStatus, jqXHR) {
      var data = JSON.parse(jsonData);
      bilan.pivotUI(
        data.ecritures,
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
              bilan.data('attached-field'),
              bilan.data('detached-field')
            ]),
            'Activite': $.pivotUtilities.sortAs(data.activites),
            'Poste': $.pivotUtilities.sortAs(data.postes)
          },
          showUI: false
        },
        false,
        'fr'
      );
    }
  });
  
  function displayEcritures(e, value, filters, pivotData) {
    filters['_csrfToken'] = bilan.data('csrf-token');
    
    $.post(
      bilan.data('detail-url'),
      filters,
      function(data, textStatus, jqXHR) {
        $('#detail').html(data);
      }
    );
  }
  
});
