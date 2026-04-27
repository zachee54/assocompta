$(function() {
  var dateEngagement = $('#date-engagement');
  var dateBancaire = $('#date-bancaire');
  
  // Réinitialiser l'animation après qu'elle aura eu lieu
  dateBancaire.get(0).addEventListener('animationend', function() {
    $(this).removeClass('autoUpdated');
  });
  
  // Recopie de la valeur + animation
  dateEngagement.change(function(event) {
    dateBancaire.val(this.value);
    dateBancaire.addClass('autoUpdated');
  });
});
