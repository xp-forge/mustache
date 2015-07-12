<?php namespace com\github\mustache\unittest;

use com\github\mustache\SectionNode;
use com\github\mustache\NodeList;
use com\github\mustache\TextNode;

class SectionNodeTest extends \unittest\TestCase {
  
  #[@test]
  public function can_create() {
    new SectionNode('test');
  }

  #[@test]
  public function name() {
    $this->assertEquals('test', (new SectionNode('test'))->name());
  }

  #[@test]
  public function inverted_is_false_by_default() {
    $this->assertFalse((new SectionNode('test'))->inverted());
  }

  #[@test, @values([false, true])]
  public function inverted($value) {
    $this->assertEquals($value, (new SectionNode('test', $value))->inverted());
  }

  #[@test]
  public function options_is_empty_by_default() {
    $this->assertEquals(array(), (new SectionNode('test'))->options());
  }

  #[@test, @values([[[]], [['a']], [['a', 'b']]])]
  public function options($value) {
    $this->assertEquals($value, (new SectionNode('test', false, $value))->options());
  }

  #[@test]
  public function add_returns_node_added() {
    $node= new TextNode('test');
    $this->assertEquals($node, (new SectionNode('test'))->add($node));
  }

  #[@test]
  public function length_initially_zero() {
    $this->assertEquals(0, (new SectionNode('test'))->length());
  }

  #[@test]
  public function length_after_adding_a_node() {
    $fixture= new SectionNode('test');
    $fixture->add(new TextNode('test'));
    $this->assertEquals(1, $fixture->length());
  }

  #[@test]
  public function length_after_adding_two_nodes() {
    $fixture= new SectionNode('test');
    $fixture->add(new TextNode('test1'));
    $fixture->add(new TextNode('test2'));
    $this->assertEquals(2, $fixture->length());
  }

  #[@test]
  public function nodes_initially_empty() {
    $this->assertEquals(array(), (new SectionNode('test'))->nodes());
  }

  #[@test]
  public function nodes_after_adding_a_node() {
    $fixture= new SectionNode('test');
    $node= $fixture->add(new TextNode('test'));
    $this->assertEquals(array($node), $fixture->nodes());
  }

  #[@test]
  public function nodes_after_adding_two_nodes() {
    $fixture= new SectionNode('test');
    $node1= $fixture->add(new TextNode('test1'));
    $node2= $fixture->add(new TextNode('test2'));
    $this->assertEquals(array($node1, $node2), $fixture->nodes());
  }

  #[@test, @expect('lang.IndexOutOfBoundsException'), @values([0, -1, 1])]
  public function nodeAt_raises_exception_on_empty_list($offset) {
    (new SectionNode('test'))->nodeAt($offset);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException'), @values([-1, 1, 2])]
  public function nodeAt_raises_exception($offset) {
    $fixture= new SectionNode('test');
    $fixture->add(new TextNode('test'));
    $fixture->nodeAt($offset);
  }

  #[@test]
  public function nodeAt_returns_added_node() {
    $fixture= new SectionNode('test');
    $node= $fixture->add(new TextNode('test'));
    $this->assertEquals($node, $fixture->nodeAt(0));
  }

  #[@test]
  public function nodeAt_returns_added_nodes() {
    $fixture= new SectionNode('test');
    $node1= $fixture->add(new TextNode('test1'));
    $node2= $fixture->add(new TextNode('test2'));
    $this->assertEquals(array($node1, $node2), array($fixture->nodeAt(0), $fixture->nodeAt(1)));
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals("{{#test}}\n\n{{/test}}\n", (string)new SectionNode('test', false));
  }

  #[@test]
  public function string_representation_when_inverted() {
    $this->assertEquals("{{^test}}\n\n{{/test}}\n", (string)new SectionNode('test', true));
  }

  #[@test, @values([
  #  ['', []],
  #  [' a', ['a']],
  #  [' a b', ['a', 'b']],
  #  [' a b "c d"', ['a', 'b', 'c d']],
  #])]
  public function string_representation_with_options($string, $options) {
    $this->assertEquals(
      "{{#test".$string."}}\n\n{{/test}}\n",
      (string)new SectionNode('test', false, $options)
    );
  }

  #[@test]
  public function string_representation_with_sub_nodes() {
    $this->assertEquals(
      "{{#test}}\nTest\n{{/test}}\n",
      (string)new SectionNode('test', false, array(), new NodeList(array(new TextNode('Test'))))
    );
  }
}