<?php

// Ecraser le format monétaire par défaut (USD, évidemment... :-P )
$this->Number->addFormat('USD',  array(
  'wholePosition' => 'after', // Monnaie affichée après le nombre
  'before' => '&nbsp;€',      // Monnaie à afficher : ' €'
  'places' => 2,              // 2 décimales
  'decimals' => ',',          // Séparateur de décimales
  'thousands' => '&nbsp;',    // Séparateur de milliers
  'negative' => '-',
  'zero' => 0,                // Afficher les zéros comme les autres nombres
  'escape' => false));        // Ne pas échapper le &nbsp;)