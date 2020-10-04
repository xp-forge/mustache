<?php namespace com\github\mustache\unittest;

use com\github\mustache\IteratorNode;
use unittest\{Test, Values};

class IteratorNodeTest extends \unittest\TestCase {
  
  #[Test]
  public function can_create() {
    new IteratorNode();
  }

  #[Test]
  public function escaped_is_true_by_default() {
    $this->assertTrue((new IteratorNode())->escaped());
  }

  #[Test, Values([false, true])]
  public function escaped($value) {
    $this->assertEquals($value, (new IteratorNode($value))->escaped());
  }

  #[Test]
  public function string_representation() {
    $this->assertEquals('{{.}}', (string)new IteratorNode(true));
  }

  #[Test]
  public function string_representation_when_not_escaped() {
    $this->assertEquals('{{& .}}', (string)new IteratorNode(false));
  }
}