<?php
  
namespace GVM;

Class Cluster 
{
  public $points = array(); // array of points that make up the cluster
  
  protected $mass; // weighted value of the cluster
  protected $variance; // total variance of the cluster
  protected $key; // a string identifier for the cluster

  public $removed; // is a flag to indicate if a cluster has been flagged for removal

  public $dimensions;
  public $m1 = array(); // mass-weighted coordinate sum, size $dimension from coords
  public $m2 = array(); // mass-weighted coordinate-square sum, size $dimension from coords
  
  public function __construct($dimensions, $mass=0.0, $key='') {
    $this->removed = false;
    $this->mass = $mass;
    $this->dimensions = $dimensions;
    $this->key = $key;
    $this->clear();
  }
  
  public function center() {
    // return the average value of the m1 mass-weighted coordinate sum
    $mass = $this->mass;
    return array_map(
              function($val) use ($mass) { return $val / $mass; },
              $this->m1
          );
  }

  public function mass() {
    return $this->mass;
  }  
  
  public function variance() {
    return $this->variance;
  }
  
  public function key() {
    return $this->key;
  }  
  
  public function count() {
    return count($this->points);
  }    
  
  public function set(Point $point) {
    // wipe out the cluster and then add in a new, single point
    $this->clear();
    $this->add($point);
  }
  
  public function add(Point $point) {
    // add a point to the cluster
    $this->points[] = $point;
    $this->mass+=$point->mass;
   
    for($i=0;$i<$this->dimensions; $i++) {
      $this->m1[$i]+=$point->coords[$i] * $point->mass;
      $this->m2[$i]+=$point->coords[$i] * $point->coords[$i] * $point->mass;
    }
    $this->update();
    return $this;
  }
  
  public function clear() {
    // Completely clears this cluster. All points and their associated mass is removed
    $this->m1 = array_fill(0, $this->dimensions, 0);
    $this->m2 = array_fill(0, $this->dimensions, 0);
    $this->variance = 0.0;
    $this->mass = 0.0;
    $this->points = array();    
  }
  
  public function merge(Cluster $cluster) {
    // merge another cluster with this one
    // just add the mass and the center to this cluster
    
    $mass = $cluster->mass;
    $this->mass+=$mass;
    
    $center = $cluster->center();
    for($i=0;$i<$this->dimensions; $i++) {
      $this->m1[$i]+=$center[$i] * $mass;
      $this->m2[$i]+=$center[$i] * $center[$i] * $mass;
    }
    

    $this->points = array_merge($this->points, $cluster->points);
    
    $this->update();
    return $this;
  }
  
  public function update() {
    // compute the variance of the cluster
    $total = 0.0;    
    for($i=0;$i<$this->dimensions; $i++) {
      $total+=($this->m2[$i]*$this->mass) - ($this->m1[$i] * $this->m1[$i]);
    }

    $this->variance = ($total/$this->mass > 0.0 ? $total/$this->mass : 0.0);
  }
  
  public function test_point(Point $point) {
    // determine the change in this cluster's variance if we add a new point to this cluster
    $test_mass = $this->mass + $point->mass;
    if ($test_mass==0) {
      return 0.0;
    }
    
    $test_total = 0.0;    
    for($i=0;$i<$this->dimensions; $i++) {
      $m1 = $this->m1[$i] + $point->mass * $point->coords[$i];
      $m2 = $this->m2[$i] + $point->mass * $point->coords[$i] * $point->coords[$i];
      $test_total+=($m2*$test_mass) - ($m1 * $m1);
    }
    $test_variance = ($test_total/$test_mass > 0.0 ? $test_total/$test_mass : 0.0);
    return $test_variance - $this->variance;
  }
  
  public function test_cluster(Cluster $cluster) {
    // determine the change in this cluster's variance if we add a new cluster to this cluster
    
    $test_mass = $this->mass + $cluster->mass;  
    if ($test_mass==0) {
      return 0.0;
    }
    
    $test_total = 0.0; 
    for($i=0;$i<$this->dimensions; $i++) {
      $m1 = $this->m1[$i] + $cluster->m1[$i];
      $m2 = $this->m2[$i] + $cluster->m2[$i];
      $test_total+=($m2*$test_mass) - ($m1 * $m1);
    }
    $test_variance = ($test_total/$test_mass > 0.0 ? $test_total/$test_mass : 0.0);
    return $test_variance - $this->variance;        
  }
  
}