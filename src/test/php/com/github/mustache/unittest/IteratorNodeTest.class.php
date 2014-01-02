<?php namespace com\github\mustache\unittest;

use com\github\mustache\IteratorNode;

class IteratorNodeTest extends \unittest\TestCase {
  
  #[@test]
  public function can_create() {
    new IteratorNode();
  }

  #[@test]
  public function escaped_is_true_by_default() {
    $this->assertTrue(create(new IteratorNode())->escaped());
  }

  #[@test, @values([false, true])]
  public function escaped($value) {
    $this->assertEquals($value, create(new IteratorNode($value))->escaped());
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals('{{.}}', (string)new IteratorNode(true));
  }

  #[@test]
  public function string_representation_when_not_escaped() {
    $this->assertEquals('{{& .}}', (string)new IteratorNode(false));
  }
}