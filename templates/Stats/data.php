<?php
$this->layout = 'ajax';

echo json_encode(compact('ecritures', 'activites', 'postes'));
