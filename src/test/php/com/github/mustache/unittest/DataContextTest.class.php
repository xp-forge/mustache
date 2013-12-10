<?php namespace com\github\mustache\unittest;

use com\github\mustache\DataContext;
use com\github\mustache\MustacheEngine;

class DataContextTest extends \unittest\TestCase {

  /**
   * Creates new fixture
   *
   * @param  [:string] $variables
   * @param  [:var] $helpers
   * @return com.github.mustache.Context
   */
  public function newFixture($variables, $helpers= array()) {
    return create(new DataContext($variables))->withEngine(create(new MustacheEngine())->withHelpers($helpers));
  }

  #[@test]
  public function can_create() {
    $this->newFixture(array());
  }

  #[@test, @values(['test', 'test.sub', 'test.sub.child'])]
  public function lookup_on_empty_data($key) {
    $fixture= $this->newFixture(array());
    $this->assertNull($fixture->lookup($key));
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
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_private_field() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      private $test = "data";
    }'));
    $this->assertNull($fixture->lookup('test'));
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
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_protected_method() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      protected function test() { return "data"; }
    }'));
    $this->assertNull($fixture->lookup('test'));
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
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_protected_getter() {
    $fixture= $this->newFixture(newinstance('lang.Object', array(), '{
      protected function getTest() { return "data"; }
    }'));
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function supports_array_access_overloading() {
    $fixture= $this->newFixture(newinstance('php.ArrayAccess', array(), '{
      public function offsetExists($h) { return "test" === $h; }
      public function offsetGet($h) { return "test" === $h ? "data" : null; }
      public function offsetSet($h, $v) { /* Empty */ }
      public function offsetUnset($h) { /* Empty */ }
    }'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function using_helper() {
    $fixture= $this->newFixture(array(), array(
      'test' => function($node, $ctx) { return 'data'; }
    ));
    $this->assertInstanceOf('Closure', $fixture->lookup('test'));
  }

  #[@test]
  public function variables_are_preferred_over_helpers() {
    $fixture= $this->newFixture(
      array('test' => 'variable'),
      array('test' => function($node, $ctx) { return 'helper'; })
    );
    $this->assertEquals('variable', $fixture->lookup('test'));
  }
}