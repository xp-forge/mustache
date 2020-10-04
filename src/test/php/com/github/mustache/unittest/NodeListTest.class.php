<?php namespace com\github\mustache\unittest;

use com\github\mustache\{NodeList, TextNode};
use lang\{IllegalArgumentException, IndexOutOfBoundsException};
use unittest\{Expect, Test, Values};

class NodeListTest extends \unittest\TestCase {

  #[Test]
  public function can_create_without_argument() {
    new NodeList();
  }

  #[Test]
  public function can_create_with_empty_nodelist() {
    new NodeList([]);
  }

  #[Test]
  public function can_create_with_non_empty_nodelist() {
    new NodeList([new TextNode('test')]);
  }

  #[Test]
  public function initially_empty() {
    $this->assertEquals(0, (new NodeList())->length());
  }

  #[Test]
  public function length_after_adding_a_node() {
    $list= new NodeList();
    $list->add(new TextNode('test'));
    $this->assertEquals(1, $list->length());
  }

  #[Test]
  public function length_after_adding_two_nodes() {
    $list= new NodeList();
    $list->add(new TextNode('test1'));
    $list->add(new TextNode('test2'));
    $this->assertEquals(2, $list->length());
  }

  #[Test]
  public function add_returns_added_node() {
    $node= new TextNode('test');
    $this->assertEquals($node, (new NodeList())->add($node));
  }

  #[Test]
  public function nodeAt_returns_node_at_offset() {
    $node= new TextNode('test');
    $this->assertEquals($node, (new NodeList([$node]))->nodeAt(0));
  }

  #[Test, Values([-1, 1, 2]), Expect(IndexOutOfBoundsException::class)]
  public function nodeAt_raises_exception_for_offset_out_of_bounds($offset) {
    (new NodeList([new TextNode('test')]))->nodeAt($offset);
  }

  #[Test]
  public function nodes_initially_returns_empty_list() {
    $this->assertEquals([], (new NodeList())->nodes());
  }

  #[Test]
  public function nodes_returns_all_added_nodes() {
    $list= new NodeList();
    $nodes= [];
    $nodes[]= $list->add(new TextNode('test1'));
    $nodes[]= $list->add(new TextNode('test2'));
    $this->assertEquals($nodes, $list->nodes());
  }

  #[Test]
  public function reading_offsets() {
    $node= new TextNode('test');
    $list= new NodeList([$node]);
    $this->assertEquals($node, $list[0]);
  }

  #[Test]
  public function testing_offsets() {
    $list= new NodeList([new TextNode('test')]);
    $this->assertEquals([true, false], [isset($list[0]), isset($list[1])]);
  }

  #[Test]
  public function adding_offsets() {
    $node= new TextNode('test');
    $list= new NodeList();
    $list[]= $node;
    $this->assertEquals($node, $list[0]);
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function cannot_modify_offsets() {
    $list= new NodeList();
    $list[0]= new TextNode('test');
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function cannot_remove_offsets() {
    $list= new NodeList([new TextNode('test')]);
    unset($list[0]);
  }

  #[Test]
  public function can_be_used_in_foreach() {
    $nodes= [new TextNode('test')];
    $list= new NodeList($nodes);
    $this->assertEquals($nodes, iterator_to_array($list));
  }
}