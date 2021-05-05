$('.ecritures tr[ref]').click(function(event) {
  window.location = '<?php
    echo $this->Html->url(array(
      'controller' => 'ecritures',
      'action' => 'edit'));
  ?>/' + $(event.currentTarget).attr('ref');
});
