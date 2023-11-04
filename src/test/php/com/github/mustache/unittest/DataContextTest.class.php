<?php namespace com\github\mustache\unittest;

use com\github\mustache\{DataContext, MustacheEngine};
use test\{Assert, Test, Values};

class DataContextTest {

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
    $fixture= $this->newFixture(new class() extends Value {
      public $test = 'data';
    });
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_protected_field() {
    $fixture= $this->newFixture(new class() extends Value {
      protected $test = 'data';
    });
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_private_field() {
    $fixture= $this->newFixture(new class() extends Value {
      private $test = 'data';
    });
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from___get() {
    $fixture= $this->newFixture(new class() extends Value {
      public function __get($name) { return 'data'; }
    });
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_public_method() {
    $fixture= $this->newFixture(new class() extends Value {
      public function test() { return 'data'; }
    });
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_private_method() {
    $fixture= $this->newFixture(new class() extends Value {
      private function test() { return 'data'; }
    });
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_protected_method() {
    $fixture= $this->newFixture(new class() extends Value {
      protected function test() { return 'data'; }
    });
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_public_getter() {
    $fixture= $this->newFixture(new class() extends Value {
      public function getTest() { return 'data'; }
    });
    Assert::equals('data', $fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_private_getter() {
    $fixture= $this->newFixture(new class() extends Value {
      private function getTest() { return 'data'; }
    });
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function lookup_from_object_protected_getter() {
    $fixture= $this->newFixture(new class() extends Value {
      protected function getTest() { return 'data'; }
    });
    Assert::null($fixture->lookup('test'));
  }

  #[Test]
  public function supports_array_access_overloading() {
    $fixture= $this->newFixture(newinstance(\ArrayAccess::class, [], '{
      #[\ReturnTypeWillChange]
      public function offsetExists($h) { return "test" === $h; }
      #[\ReturnTypeWillChange]
      public function offsetGet($h) { return "test" === $h ? "data" : null; }
      #[\ReturnTypeWillChange]
      public function offsetSet($h, $v) { /* Empty */ }
      #[\ReturnTypeWillChange]
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
  public function newInstance_sets_itself_as_parent_for_new_context_by_default() {
    $parent= $this->newFixture([]);
    $child= $parent->newInstance([]);
    Assert::equals($parent, $child->parent);
  }

  #[Test]
  public function newInstance_sets_itself_as_parent_for_new_context_when_passed_null() {
    $parent= $this->newFixture([]);
    $child= $parent->newInstance([], null);
    Assert::equals($parent, $child->parent);
  }

  #[Test]
  public function newInstance_uses_given_value_as_parent() {
    $parent= $this->newFixture([]);
    $child= $parent->newInstance([]);
    $parallel= $child->newInstance([], $parent);
    Assert::equals($parent, $parallel->parent);
  }
}