<?php namespace com\github\mustache\unittest;

use com\github\mustache\PartialNode;
use unittest\Test;

class PartialNodeTest extends \unittest\TestCase {
  
  #[Test]
  public function can_create() {
    new PartialNode('test');
  }

  #[Test]
  public function template() {
    $this->assertEquals('test', (new PartialNode('test'))->template());
  }

  #[Test]
  public function string_representation() {
    $this->assertEquals('{{> test}}', (string)new PartialNode('test'));
  }
}