<?php namespace com\github\mustache;

use lang\IndexOutOfBoundsException;

/**
 * Represents a list of nodes. The template itself is represented
 * by this list, and sections contain a list of (nested) nodes.
 *
 * @see   xp://com.github.mustache.SectionNode
 * @test  xp://com.github.mustache.unittest.NodeListTest
 */
class NodeList extends Node {
  protected $nodes;

  /**
   * Create a new node list
   *
   * @param  com.github.mustache.Node[] $nodes
   */
  public function __construct(array $nodes= []) {
    $this->nodes= $nodes;
  }

  /**
   * Add a node
   *
   * @param  com.github.mustache.Node $node
   * @return com.github.mustache.Node $node The added node
   */
  public function add(Node $node) {
    $this->nodes[]= $node;
    return $node;
  }

  /**
   * Returns node list's length
   *
   * @return int
   */
  public function length() {
    return sizeof($this->nodes);
  }

  /**
   * Returns a node at a given ofset
   *
   * @param  int $i
   * @return com.github.mustache.Node
   * @throws lang.IndexOutOfBoundsException
   */
  public function nodeAt($i) {
    if ($i < 0 || $i >= sizeof($this->nodes)) {
      throw new IndexOutOfBoundsException('Illegal offset '.$i);
    }
    return $this->nodes[$i];
  }

  /**
   * Returns all nodes
   *
   * @return com.github.mustache.Node[]
   */
  public function nodes() {
    return $this->nodes;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'@'.\xp::stringOf($this->nodes);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    $output= '';
    foreach ($this->nodes as $node) {
      $output.= $node->evaluate($context);
    }
    return $output;
  }

  /**
   * Check whether a given value is equal to this node list
   *
   * @param  var $cmp The value
   * @return bool
   */
  public function equals($cmp) {
    if (!$cmp instanceof self) return false;
    if (sizeof($this->nodes) !== sizeof($cmp->nodes)) return false;
    foreach ($this->nodes as $i => $node) {
      if (!$node->equals($cmp->nodes[$i])) return false;
    }
    return true;
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return trim(implode('', $this->nodes));
  }
}