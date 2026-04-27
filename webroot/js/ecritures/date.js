$(function() {
  $('#pointage-button').on('click', function() {
    var date = $('#date-engagement').val();
    $('#date-bancaire').val(date);
    $(this).hide();
    $('#pointage').show();
  });
});
