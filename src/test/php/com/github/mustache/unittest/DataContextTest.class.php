<?php namespace com\github\mustache\unittest;

use com\github\mustache\{DataContext, MustacheEngine};
use unittest\{Assert, Test, Values};

class DataContextTest {

  /**
   * Creates new fixture
   *
   * @param  [:string] $variables
   * @param  [:var] $helpers
   * @return com.github.mustache.Context
   */
  public function newFixture($variables, $helpers= []) {
    return (new DataContext($variables))->inScope((new MustacheEngine())->withHelpers($helpers));
  }

  #[Test]
  public function can_create() {
    $this->newFixture([]);
  }

  #[Test, Values(['test', 'test.sub', 'test.sub.child'])]
  public function lookup_on_empty_data($key) {
    $fixture= $this->newFixture([]);
    Assert::null($fixture->lookup($key));
  }

  #[Test]
  public function lookup_from_hash() {
    $fixture= $this->newFixture(['test' => 'data']);
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_hash_sub() {
    $fixture= $this->newFixture(['test' => ['sub' => 'data']]);
    Assert::equals('data', $fixture->lookup('test.sub'));
  }

  #[Test]
  public function lookup_from_object_public_field() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      public $test = "data";
    }'));
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_protected_field() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      protected $test = "data";
    }'));
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_private_field() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      private $test = "data";
    }'));
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_public_method() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      public function test() { return "data"; }
    }'));
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_private_method() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      private function test() { return "data"; }
    }'));
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_protected_method() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      protected function test() { return "data"; }
    }'));
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_public_getter() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      public function getTest() { return "data"; }
    }'));
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_private_getter() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      private function getTest() { return "data"; }
    }'));
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_protected_getter() {
    $fixture= $this->newFixture(newinstance(Value::class, [], '{
      protected function getTest() { return "data"; }
    }'));
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function supports_array_access_overloading() {
    $fixture= $this->newFixture(newinstance(\ArrayAccess::class, [], '{
      public function offsetExists($h) { return "test" === $h; }
      public function offsetGet($h) { return "test" === $h ? "data" : null; }
      public function offsetSet($h, $v) { /* Empty */ }
      public function offsetUnset($h) { /* Empty */ }
    }'));
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function using_helper() {
    $fixture= $this->newFixture([], [
      'test' => function($node, $ctx) { return 'data'; }
    ]);
    Assert::instance(\Closure::class, $fixture->lookup('test'));
  }

  #[Test]
  public function variables_are_preferred_over_helpers() {
    $fixture= $this->newFixture(
      ['test' => 'variable'],
      ['test' => function($node, $ctx) { return 'helper'; }]
    );
    Assert::equals('variable', $fixture->lookup('test'));
  }

  #[Test]
  public function parent_initially_null() {
    Assert::null($this->newFixture([])->parent);
  }

  #[Test]
  public function asContext_sets_itself_as_parent_for_new_context_by_default() {
    $parent= $this->newFixture([]);
    $child= $parent->asContext([]);
    Assert::equals($parent, $child->parent);
  }

  #[Test]
  public function asContext_sets_itself_as_parent_for_new_context_when_passed_null() {
    $parent= $this->newFixture([]);
    $child= $parent->asContext([], null);
    Assert::equals($parent, $child->parent);
  }

  #[Test]
  public function asContext_uses_given_value_as_parent() {
    $parent= $this->newFixture([]);
    $child= $parent->asContext([]);
    $parallel= $child->asContext([], $parent);
    Assert::equals($parent, $parallel->parent);
  }
}