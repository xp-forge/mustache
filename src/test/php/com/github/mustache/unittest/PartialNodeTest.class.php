<?php namespace com\github\mustache\unittest;

use com\github\mustache\PartialNode;
use unittest\{Assert, Test};

class PartialNodeTest {
  
  #[Test]
  public function can_create() {
    new PartialNode('test');
  }

  #[Test]
  public function template() {
    Assert::equals('test', (new PartialNode('test'))->template());
  }

  #[Test]
  public function string_representation() {
    Assert::equals('{{> test}}', (string)new PartialNode('test'));
  }
}