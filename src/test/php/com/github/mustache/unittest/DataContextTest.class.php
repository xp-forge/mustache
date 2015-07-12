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
  public function newFixture($variables, $helpers= []) {
    return (new DataContext($variables))->withEngine((new MustacheEngine())->withHelpers($helpers));
  }

  #[@test]
  public function can_create() {
    $this->newFixture([]);
  }

  #[@test, @values(['test', 'test.sub', 'test.sub.child'])]
  public function lookup_on_empty_data($key) {
    $fixture= $this->newFixture([]);
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
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      public $test = "data";
    }'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_protected_field() {
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      protected $test = "data";
    }'));
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_private_field() {
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      private $test = "data";
    }'));
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_public_method() {
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      public function test() { return "data"; }
    }'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_private_method() {
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      private function test() { return "data"; }
    }'));
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_protected_method() {
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      protected function test() { return "data"; }
    }'));
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_public_getter() {
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      public function getTest() { return "data"; }
    }'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_private_getter() {
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      private function getTest() { return "data"; }
    }'));
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function lookup_from_object_protected_getter() {
    $fixture= $this->newFixture(newinstance('lang.Object', [], '{
      protected function getTest() { return "data"; }
    }'));
    $this->assertNull($fixture->lookup('test'));
  }

  #[@test]
  public function supports_array_access_overloading() {
    $fixture= $this->newFixture(newinstance('php.ArrayAccess', [], '{
      public function offsetExists($h) { return "test" === $h; }
      public function offsetGet($h) { return "test" === $h ? "data" : null; }
      public function offsetSet($h, $v) { /* Empty */ }
      public function offsetUnset($h) { /* Empty */ }
    }'));
    $this->assertEquals('data', $fixture->lookup('test'));
  }

  #[@test]
  public function using_helper() {
    $fixture= $this->newFixture([], array(
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

  #[@test]
  public function parent_initially_null() {
    $this->assertNull($this->newFixture([])->parent);
  }

  #[@test]
  public function newInstance_sets_itself_as_parent_for_new_context_by_default() {
    $parent= $this->newFixture([]);
    $child= $parent->newInstance([]);
    $this->assertEquals($parent, $child->parent);
  }

  #[@test]
  public function newInstance_sets_itself_as_parent_for_new_context_when_passed_null() {
    $parent= $this->newFixture([]);
    $child= $parent->newInstance([], null);
    $this->assertEquals($parent, $child->parent);
  }

  #[@test]
  public function newInstance_uses_given_value_as_parent() {
    $parent= $this->newFixture([]);
    $child= $parent->newInstance([]);
    $parallel= $child->newInstance([], $parent);
    $this->assertEquals($parent, $parallel->parent);
  }
}