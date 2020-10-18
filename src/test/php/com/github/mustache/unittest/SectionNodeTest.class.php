<?php namespace com\github\mustache\unittest;

use com\github\mustache\{NodeList, SectionNode, TextNode};
use lang\IndexOutOfBoundsException;
use unittest\{Assert, Expect, Test, Values};

class SectionNodeTest {
  
  #[Test]
  public function can_create() {
    new SectionNode('test');
  }

  #[Test]
  public function name() {
    Assert::equals('test', (new SectionNode('test'))->name());
  }

  #[Test]
  public function inverted_is_false_by_default() {
    Assert::false((new SectionNode('test'))->inverted());
  }

  #[Test, Values([false, true])]
  public function inverted($value) {
    Assert::equals($value, (new SectionNode('test', $value))->inverted());
  }

  #[Test]
  public function options_is_empty_by_default() {
    Assert::equals([], (new SectionNode('test'))->options());
  }

  #[Test, Values([[[]], [['a']], [['a', 'b']]])]
  public function options($value) {
    Assert::equals($value, (new SectionNode('test', false, $value))->options());
  }

  #[Test]
  public function add_returns_node_added() {
    $node= new TextNode('test');
    Assert::equals($node, (new SectionNode('test'))->add($node));
  }

  #[Test]
  public function length_initially_zero() {
    Assert::equals(0, (new SectionNode('test'))->length());
  }

  #[Test]
  public function length_after_adding_a_node() {
    $fixture= new SectionNode('test');
    $fixture->add(new TextNode('test'));
    Assert::equals(1, $fixture->length());
  }

  #[Test]
  public function length_after_adding_two_nodes() {
    $fixture= new SectionNode('test');
    $fixture->add(new TextNode('test1'));
    $fixture->add(new TextNode('test2'));
    Assert::equals(2, $fixture->length());
  }

  #[Test]
  public function nodes_initially_empty() {
    Assert::equals([], (new SectionNode('test'))->nodes());
  }

  #[Test]
  public function nodes_after_adding_a_node() {
    $fixture= new SectionNode('test');
    $node= $fixture->add(new TextNode('test'));
    Assert::equals([$node], $fixture->nodes());
  }

  #[Test]
  public function nodes_after_adding_two_nodes() {
    $fixture= new SectionNode('test');
    $node1= $fixture->add(new TextNode('test1'));
    $node2= $fixture->add(new TextNode('test2'));
    Assert::equals([$node1, $node2], $fixture->nodes());
  }

  #[Test, Expect(IndexOutOfBoundsException::class), Values([0, -1, 1])]
  public function nodeAt_raises_exception_on_empty_list($offset) {
    (new SectionNode('test'))->nodeAt($offset);
  }

  #[Test, Expect(IndexOutOfBoundsException::class), Values([-1, 1, 2])]
  public function nodeAt_raises_exception($offset) {
    $fixture= new SectionNode('test');
    $fixture->add(new TextNode('test'));
    $fixture->nodeAt($offset);
  }

  #[Test]
  public function nodeAt_returns_added_node() {
    $fixture= new SectionNode('test');
    $node= $fixture->add(new TextNode('test'));
    Assert::equals($node, $fixture->nodeAt(0));
  }

  #[Test]
  public function nodeAt_returns_added_nodes() {
    $fixture= new SectionNode('test');
    $node1= $fixture->add(new TextNode('test1'));
    $node2= $fixture->add(new TextNode('test2'));
    Assert::equals([$node1, $node2], [$fixture->nodeAt(0), $fixture->nodeAt(1)]);
  }

  #[Test]
  public function string_representation() {
    Assert::equals("{{#test}}\n\n{{/test}}\n", (string)new SectionNode('test', false));
  }

  #[Test]
  public function string_representation_when_inverted() {
    Assert::equals("{{^test}}\n\n{{/test}}\n", (string)new SectionNode('test', true));
  }

  #[Test, Values([['', []], [' a', ['a']], [' a b', ['a', 'b']], [' a b "c d"', ['a', 'b', 'c d']],])]
  public function string_representation_with_options($string, $options) {
    Assert::equals(
      "{{#test".$string."}}\n\n{{/test}}\n",
      (string)new SectionNode('test', false, $options)
    );
  }

  #[Test]
  public function string_representation_with_sub_nodes() {
    Assert::equals(
      "{{#test}}\nTest\n{{/test}}\n",
      (string)new SectionNode('test', false, [], new NodeList([new TextNode('Test')]))
    );
  }
}