<?php namespace com\github\mustache\unittest;

use com\github\mustache\DataContext;
use com\github\mustache\MustacheEngine;

class DataContextTest extends \unittest\TestCase {

  /**
   * Creates new fixture
   *
   * @param  [:string] $variables
   * @return com.github.mustache.Context
   */
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

  #[@test]
  public function lookup_from_object_public_field() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      public $test = "data";
    }'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_protected_field() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      protected $test = "data";
    }'));
    $this->assertEquals('', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_private_field() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      private $test = "data";
    }'));
    $this->assertEquals('', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_public_method() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      public function test() { return "data"; }
    }'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_private_method() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      private function test() { return "data"; }
    }'));
    $this->assertEquals('', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_protected_method() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      protected function test() { return "data"; }
    }'));
    $this->assertEquals('', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_public_getter() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      public function getTest() { return "data"; }
    }'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_private_getter() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      private function getTest() { return "data"; }
    }'));
    $this->assertEquals('', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_protected_getter() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      protected function getTest() { return "data"; }
    }'));
    $this->assertEquals('', $fixture->lookup('test'));
  }
}