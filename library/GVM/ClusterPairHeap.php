<?php
  
namespace GVM;

use GVM\ClusterPair;

Class ClusterPairHeap extends \SplHeap
{
  
  /**
   * We modify the abstract method compare so we can sort our
   * rankings using the values of the two ClusterPairs
   */

  public function compare(ClusterPair $pair1, ClusterPair $pair2) {
    if ($pair1->value() === $pair2->value()) return 0;
    return $pair1->value() < $pair2->value() ? 1 : -1;
  } 
  
  
}