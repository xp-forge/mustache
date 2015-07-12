<?php namespace com\github\mustache\unittest;

use com\github\mustache\NodeList;
use com\github\mustache\TextNode;

class NodeListTest extends \unittest\TestCase {

  #[@test]
  public function can_create_without_argument() {
    new NodeList();
  }

  #[@test]
  public function can_create_with_empty_nodelist() {
    new NodeList(array());
  }

  #[@test]
  public function can_create_with_non_empty_nodelist() {
    new NodeList(array(new TextNode('test')));
  }

  #[@test]
  public function initially_empty() {
    $this->assertEquals(0, (new NodeList())->length());
  }

  #[@test]
  public function length_after_adding_a_node() {
    $list= new NodeList();
    $list->add(new TextNode('test'));
    $this->assertEquals(1, $list->length());
  }

  #[@test]
  public function length_after_adding_two_nodes() {
    $list= new NodeList();
    $list->add(new TextNode('test1'));
    $list->add(new TextNode('test2'));
    $this->assertEquals(2, $list->length());
  }

  #[@test]
  public function add_returns_added_node() {
    $node= new TextNode('test');
    $this->assertEquals($node, (new NodeList())->add($node));
  }

  #[@test]
  public function nodeAt_returns_node_at_offset() {
    $node= new TextNode('test');
    $this->assertEquals($node, (new NodeList(array($node)))->nodeAt(0));
  }

  #[@test, @values([-1, 1, 2]), @expect('lang.IndexOutOfBoundsException')]
  public function nodeAt_raises_exception_for_offset_out_of_bounds($offset) {
    (new NodeList(array(new TextNode('test'))))->nodeAt($offset);
  }

  #[@test]
  public function nodes_initially_returns_empty_list() {
    $this->assertEquals(array(), (new NodeList())->nodes());
  }

  #[@test]
  public function nodes_returns_all_added_nodes() {
    $list= new NodeList();
    $nodes= array();
    $nodes[]= $list->add(new TextNode('test1'));
    $nodes[]= $list->add(new TextNode('test2'));
    $this->assertEquals($nodes, $list->nodes());
  }
}