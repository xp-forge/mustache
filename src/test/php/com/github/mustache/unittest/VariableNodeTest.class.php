<?php namespace com\github\mustache\unittest;

use com\github\mustache\VariableNode;
use test\{Assert, Test, Values};

class VariableNodeTest {
  
  #[Test]
  public function can_create() {
    new VariableNode('test');
  }

  #[Test]
  public function name() {
    Assert::equals('test', (new VariableNode('test'))->name());
  }

  #[Test]
  public function escaped_is_true_by_default() {
    Assert::true((new VariableNode('test'))->escaped());
  }

  #[Test, Values([false, true])]
  public function escaped($value) {
    Assert::equals($value, (new VariableNode('test', $value))->escaped());
  }

  #[Test]
  public function options_is_empty_by_default() {
    Assert::equals([], (new VariableNode('test'))->options());
  }

  #[Test, Values([[[]], [['a']], [['a', 'b']]])]
  public function options($value) {
    Assert::equals($value, (new VariableNode('test', true, $value))->options());
  }
  
  #[Test]
  public function string_representation() {
    Assert::equals('{{test}}', (string)new VariableNode('test', true));
  }

  #[Test]
  public function string_representation_when_not_escaped() {
    Assert::equals('{{& test}}', (string)new VariableNode('test', false));
  }

  #[Test, Values([['', []], [' a', ['a']], [' a b', ['a', 'b']], [' a b "c d"', ['a', 'b', 'c d']],])]
  public function string_representation_with_options($string, $options) {
    Assert::equals('{{test'.$string.'}}', (string)new VariableNode('test', true, $options));
  }
}