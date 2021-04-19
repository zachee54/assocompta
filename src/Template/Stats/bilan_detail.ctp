<?php
$this->layout = 'ajax';

if ($ecritures) {
  echo $this->element('ecritures/table');
}
