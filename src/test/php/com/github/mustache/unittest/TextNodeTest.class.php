<?php namespace com\github\mustache\unittest;

use com\github\mustache\TextNode;
use unittest\{Assert, Test, Values};

class TextNodeTest {
  
  #[Test]
  public function can_create() {
    new TextNode('test');
  }

  #[Test]
  public function text() {
    Assert::equals('test', (new TextNode('test'))->text());
  }

  #[Test, Values(['', 'test', 'Hello World'])]
  public function string_representation($text) {
    Assert::equals($text, (string)new TextNode($text));
  }
}