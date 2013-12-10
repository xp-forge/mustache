<?php namespace com\github\mustache;

class DataContextTest extends \unittest\TestCase {

  public function newFixture($variables) {
    return new DataContext($variables, new MustacheEngine());
  }

  #[@test]
  public function can_create() {
    $this->newFixture(array());
  }

  #[@test, @values(['test', 'test.sub', 'test.sub.child'])]
  public function lookup_on_empty_data($key) {
    $fixture= $this->newFixture(array());
    $this->assertEquals('', $fixture->lookup($key));
  }

  #[@test]
  public function lookup_from_hash() {
    $fixture= $this->newFixture(array('test' => 'data'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_hash_sub() {
    $fixture= $this->newFixture(array('test' => array('sub' => 'data')));
    $this->assertEquals('data', $fixture->lookup('test.sub'));
  }
}