<?php
  
namespace GVM;

use GVM\Cluster;

Class ClusterPair 
{
  /**
    * The First Cluster in the pair
    * @var \GVM\Cluster;
  */
  public $c1;
  /**
    * The Second Cluster in the pair
    * @var \GVM\Cluster;
  */
  public $c2;
  
  protected $value; // the combined variance measure of the pair of Clusters
  
  public function __construct(Cluster $c1, Cluster $c2) {
    $this->c1 = $c1;
    $this->c2 = $c2;
    $this->update();
  }
  
  public function update() {
    // update the value of the pair
    $this->value = $this->c1->test_cluster($this->c2) - $this->c1->variance() - $this->c2->variance();
  }
  
  public function value() {
    $this->update();
    return $this->value;
  }
}