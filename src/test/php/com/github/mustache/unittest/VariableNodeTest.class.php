<?php namespace com\github\mustache\unittest;

use com\github\mustache\VariableNode;

class VariableNodeTest extends \unittest\TestCase {
  
  #[@test]
  public function can_create() {
    new VariableNode('test');
  }

  #[@test]
  public function name() {
    $this->assertEquals('test', (new VariableNode('test'))->name());
  }

  #[@test]
  public function escaped_is_true_by_default() {
    $this->assertTrue((new VariableNode('test'))->escaped());
  }

  #[@test, @values([false, true])]
  public function escaped($value) {
    $this->assertEquals($value, (new VariableNode('test', $value))->escaped());
  }

  #[@test]
  public function options_is_empty_by_default() {
    $this->assertEquals([], (new VariableNode('test'))->options());
  }

  #[@test, @values([[[]], [['a']], [['a', 'b']]])]
  public function options($value) {
    $this->assertEquals($value, (new VariableNode('test', true, $value))->options());
  }
  
  #[@test]
  public function string_representation() {
    $this->assertEquals('{{test}}', (string)new VariableNode('test', true));
  }

  #[@test]
  public function string_representation_when_not_escaped() {
    $this->assertEquals('{{& test}}', (string)new VariableNode('test', false));
  }

  #[@test, @values([
  #  ['', []],
  #  [' a', ['a']],
  #  [' a b', ['a', 'b']],
  #  [' a b "c d"', ['a', 'b', 'c d']],
  #])]
  public function string_representation_with_options($string, $options) {
    $this->assertEquals('{{test'.$string.'}}', (string)new VariableNode('test', true, $options));
  }
}