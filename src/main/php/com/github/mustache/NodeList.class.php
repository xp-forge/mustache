<?php namespace com\github\mustache;

use lang\IllegalArgumentException;

/**
 * Represents a list of nodes. The template itself is represented
 * by this list, and sections contain a list of (nested) nodes.
 *
 * @see   xp://com.github.mustache.SectionNode
 * @test  xp://com.github.mustache.unittest.NodeListTest
 */
class NodeList extends Node implements \ArrayAccess, \IteratorAggregate {
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
  public function length() { return sizeof($this->nodes); }

  /**
   * Returns a node at a given ofset
   *
   * @param  int $i
   * @return com.github.mustache.Node
   * @throws lang.IndexOutOfBoundsException
   */
  public function nodeAt($i) { return $this->nodes[$i]; }

  /**
   * Returns all nodes
   *
   * @return com.github.mustache.Node[]
   */
  public function nodes() { return $this->nodes; }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    $s= nameof($this)."@[\n";
    foreach ($this->nodes as $node) {
      $s.= '  '.$node->toString()."\n";
    }
    return $s.']';
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

  /**
   * Overload isset
   *
   * @param  int $offset
   * @return bool
   */
  public function offsetExists($offset) {
    return $offset >= 0 && $offset < sizeof($this->nodes);
  }

  /**
   * Overload unset
   *
   * @param  int $offset
   * @return void
   */
  public function offsetUnset($offset) {
    throw new IllegalArgumentException('Cannot remove by offset');
  }

  /**
   * Overload =[]
   *
   * @param  int $offset
   * @return bool
   */
  public function offsetGet($offset) {
    return $this->nodes[$offset];
  }

  /**
   * Overload []=
   *
   * @param  int $offset
   * @param  var $value
   * @return void
   */
  public function offsetSet($offset, $value) {
    if (null === $offset) {
      $this->nodes[]= $value;
    } else {
      throw new IllegalArgumentException('Cannot modify offsets');
    }
  }

  /**
   * Overload iteration
   *
   * @return php.Iterator
   */
  public function getIterator() {
    return new \ArrayIterator($this->nodes);
  }
}