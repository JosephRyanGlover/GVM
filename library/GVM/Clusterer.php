<?php
  
namespace GVM;

use GVM\ClusterPair;
use GVM\ClusterPairHeap;
use GVM\Cluster;

Class Clusterer
{
  
  const MAX_FLOAT = PHP_INT_MAX;
  
  protected $capacity; // max number of allowed clusters
  protected $dimensions; // how many dimensions is the point data? e.g. lat/long = 2
  public $clusters = array(); // the collection of clusters
  public $pairs; // This is the ClusterPairHeap object
  
  public function __construct($capacity, $dimensions=2) {
    $this->capacity = $capacity;
    $this->dimensions = $dimensions;
    
    $this->pairs = new ClusterPairHeap();
    
    // create $capacity clusters. could use a factory, but only have one type of cluster right now REFACTOR
    for($i=0;$i<$this->capacity;$i++) {
      $this->clusters[] = new Cluster($this->dimensions);
      // add a pair for this new cluster and everyone before it;
      $this->add_pairs();
    }
  }
  
  protected function clear() {
    $this->clusters = array();
    $this->pairs = new ClusterPairHeap();
  }

  public function add(Point $point) {
    // feed a point into the clusterer, decide what to do with it

    // look for an empty cluster first
    foreach($this->clusters as $c) {
      if ($c->count()==0) { // found an empty one
        $c->add($point);
        $this->update_pairs();
        return $this;
      }
    }
    
    // if that didn't work, we need to work with existing clusters
    
    // first, let's find the cheapest cluster to merge with    
    // this is the top of the heap, the first element. we only check that one
    $this->pairs->top(); // move to top of the heap
    $merge_pair = $this->pairs->current(); // grab the current element, i.e. the top one    
    $merge_t = $merge_pair->value(); 
  
    //pre_print_r($this->pairs);die();
  

    
    // second, let's find the cheapest cluster to add too 
    $best_c = NULL;
    $addition_t = self::MAX_FLOAT;
    foreach($this->clusters as $c) {
      $t = $c->test_point($point);     // test this new point against every cluster
      if ($t<$addition_t) {
        $best_c = $c;
        $addition_t = $t;
      }
    }
    
    
    
    if ($addition_t<=$merge_t) {
      print "Adding to cluster. Add: " . $addition_t . " / Merge: " . $merge_t . "<br>";
      // we'll go with addition, thanks
      $best_c->add($point);
      $this->update_pairs();
    } else {
      print "Merging two clusters. Add: " . $addition_t . " / Merge: " . $merge_t . "<br>";
      // let us join hands and merge
      $c1 = $merge_pair->c1;
      $c2 = $merge_pair->c2;
      if ($c1->mass()>=$c2->mass()) {
        $c1->merge($c2);
        // clean c2 up for its new single point
        $c2->set($point);
      } else {
        $c2->merge($c1);
        // clean c1 up for its new single point
        $c1->set($point);
      }
      $this->update_pairs(); 
    }
    
    return $this;
    
  }

  protected function update_pairs() {
    // pairs need to have their values updated, if they've been changed
    // e.g. had points added to them to modify their variance
    // pairs will do this automatically when their value() function is called
    // which is called as part of the compare() function in ClusterPairHeap
    // so we just need to get the heap to call compare
    // calling top() has the effect of peeking at tbe top of the heap
    // in so doing, I think that it runs an internal heapify, thus redistributing
    // the pairs, by calling compare on each and therefore updating each pair
    // seems to work anyway
    $this->pairs->top();
  }

  protected function add_pairs() {
    // for each cluster in the clusterer, add a pair
    // so we're making a lower (or upper) triangular matrix
    
    $c1 = $this->clusters[count($this->clusters)-1];
    foreach($this->clusters as $c) {
      if ($c1===$c) {
        
      } else {
        $pair = new ClusterPair($c1, $c);
        $this->pairs->insert($pair);
      }
    }
  }  
}