<?php namespace com\github\mustache\unittest;

use com\github\mustache\TextNode;

class TextNodeTest extends \unittest\TestCase {
  
  #[@test]
  public function can_create() {
    new TextNode('test');
  }

  #[@test]
  public function text() {
    $this->assertEquals('test', create(new TextNode('test'))->text());
  }

  #[@test, @values(['', 'test', 'Hello World'])]
  public function string_representation($text) {
    $this->assertEquals($text, (string)new TextNode($text));
  }
}