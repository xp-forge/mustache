<?php namespace com\github\mustache\unittest;

use com\github\mustache\VariableNode;
use unittest\{Test, Values};

class VariableNodeTest extends \unittest\TestCase {
  
  #[Test]
  public function can_create() {
    new VariableNode('test');
  }

  #[Test]
  public function name() {
    $this->assertEquals('test', (new VariableNode('test'))->name());
  }

  #[Test]
  public function escaped_is_true_by_default() {
    $this->assertTrue((new VariableNode('test'))->escaped());
  }

  #[Test, Values([false, true])]
  public function escaped($value) {
    $this->assertEquals($value, (new VariableNode('test', $value))->escaped());
  }

  #[Test]
  public function options_is_empty_by_default() {
    $this->assertEquals([], (new VariableNode('test'))->options());
  }

  #[Test, Values([[[]], [['a']], [['a', 'b']]])]
  public function options($value) {
    $this->assertEquals($value, (new VariableNode('test', true, $value))->options());
  }
  
  #[Test]
  public function string_representation() {
    $this->assertEquals('{{test}}', (string)new VariableNode('test', true));
  }

  #[Test]
  public function string_representation_when_not_escaped() {
    $this->assertEquals('{{& test}}', (string)new VariableNode('test', false));
  }

  #[Test, Values([['', []], [' a', ['a']], [' a b', ['a', 'b']], [' a b "c d"', ['a', 'b', 'c d']],])]
  public function string_representation_with_options($string, $options) {
    $this->assertEquals('{{test'.$string.'}}', (string)new VariableNode('test', true, $options));
  }
}