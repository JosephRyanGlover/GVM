<?php
  
class ClusterTest extends \PHPUnit_Framework_TestCase
{
    public function testClusterCount()
    {
      $c = new \GVM\Cluster(1);

      $p1 = new \GVM\Point(array(10),1);
      $p2 = new \GVM\Point(array(20),1);
      
      $c->add($p1);
      $c->add($p2);
      
      $this->assertEquals(2, $c->count());
    }
    
    public function testClusterMass()
    {
      $c = new \GVM\Cluster(1);

      $p1 = new \GVM\Point(array(10),1);
      $p2 = new \GVM\Point(array(20),1);
      
      $c->add($p1);
      $c->add($p2);
      
      $this->assertEquals(2, $c->mass());
    }
    
    public function testClusterCenter()
    {
      $c = new \GVM\Cluster(1);

      $p1 = new \GVM\Point(array(10),1);
      $p2 = new \GVM\Point(array(20),1);
      
      $c->add($p1);
      $c->add($p2);
      
      $o = $c->center();
      
      $this->assertEquals(15, $o[0]);
    }    

    public function testClusterVariance()
    {
      $c = new \GVM\Cluster(1);

      $p1 = new \GVM\Point(array(10),1);
      $p2 = new \GVM\Point(array(20),1);
      
      $c->add($p1);
      $c->add($p2);
      
      $this->assertEquals(50, $c->variance());
    }      
    
}