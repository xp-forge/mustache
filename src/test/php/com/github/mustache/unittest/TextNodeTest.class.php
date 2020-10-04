<?php namespace com\github\mustache\unittest;

use com\github\mustache\TextNode;
use unittest\{Test, Values};

class TextNodeTest extends \unittest\TestCase {
  
  #[Test]
  public function can_create() {
    new TextNode('test');
  }

  #[Test]
  public function text() {
    $this->assertEquals('test', (new TextNode('test'))->text());
  }

  #[Test, Values(['', 'test', 'Hello World'])]
  public function string_representation($text) {
    $this->assertEquals($text, (string)new TextNode($text));
  }
}