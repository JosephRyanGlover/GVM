<?php
  
require('vendor/autoload.php');

function pre_print_r($array) {
  print "<pre>\n";
  print_r($array);
  print "</pre>\n";
}
  
  
$c = new \GVM\Cluster(1);

$p1 = new \GVM\Point(array(10),1);
$p2 = new \GVM\Point(array(20),1);
      
$c->add($p1);
$c->add($p2);

print_r($c->variance());