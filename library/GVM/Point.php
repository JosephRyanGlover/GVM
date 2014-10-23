<?php
  
namespace GVM;
  
Class Point 
{
  protected $key;
  public $mass;
  protected $dimension = 2; // the number of dimensions in the point coordinates
  public $coords = array(); // the $dimension coordinates for this point
  
  public function __construct(array $coords, $dimension=2, $mass=1.0, $key='') {
    $this->coords = $coords;
    $this->dimension = $dimension;
    $this->mass = $mass;
    $this->key = $key;
  }
  
}