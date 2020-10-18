<?php namespace com\github\mustache\unittest;

use com\github\mustache\IteratorNode;
use unittest\{Assert, Test, Values};

class IteratorNodeTest {
  
  #[Test]
  public function can_create() {
    new IteratorNode();
  }

  #[Test]
  public function escaped_is_true_by_default() {
    Assert::true((new IteratorNode())->escaped());
  }

  #[Test, Values([false, true])]
  public function escaped($value) {
    Assert::equals($value, (new IteratorNode($value))->escaped());
  }

  #[Test]
  public function string_representation() {
    Assert::equals('{{.}}', (string)new IteratorNode(true));
  }

  #[Test]
  public function string_representation_when_not_escaped() {
    Assert::equals('{{& .}}', (string)new IteratorNode(false));
  }
}