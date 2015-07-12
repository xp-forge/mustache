<?php namespace com\github\mustache\unittest;

use com\github\mustache\PartialNode;

class PartialNodeTest extends \unittest\TestCase {
  
  #[@test]
  public function can_create() {
    new PartialNode('test');
  }

  #[@test]
  public function template() {
    $this->assertEquals('test', (new PartialNode('test'))->template());
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals('{{> test}}', (string)new PartialNode('test'));
  }
}