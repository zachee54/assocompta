$('.ecritures tr[ref]').click(function(event) {
  window.location = '<?php
    echo $this->Url->build(array(
      'controller' => 'ecritures',
      'action' => 'edit'));
  ?>/' + $(event.currentTarget).attr('ref');
});
