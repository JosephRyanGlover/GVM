<?php

/**
Here is a github change
*/

function pre_print_r($array) {
  print "<pre>\n";
  print_r($array);
  print "</pre>\n";
}


require('library/autoload.php');

use GVM\Clusterer;
use GVM\Point;

// so, instead of adding points to a cluster, we add them to the clusterer
// the clusterer then takes care of them, doing as it will
// we then return the clusters it's found, and la de da



$max_clusters = 5;
$dimensions = 1; // linear example
$clusterer = new Clusterer($max_clusters, $dimensions);

for($i=0;$i<=20;$i++) {
  $number = intval(rand(0,50));
  $coords = array($number);
  $point = new Point($coords, $dimensions);
  $clusterer->add($point);
  
}





pre_print_r($clusterer->clusters);

/*
foreach($clusterer->clusters as $c) {
  $c->update();
  print "Var: " . $c->variance() . "<br>";
}
*/
